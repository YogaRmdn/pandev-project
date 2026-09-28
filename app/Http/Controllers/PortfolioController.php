<?php

namespace App\Http\Controllers;

use App\Enums\PortfolioStatus;
use App\Http\Requests\PortfolioRequest;
use App\Models\Portfolio;
use App\Models\PortfolioGalery;
use App\Services\MediaService;
use App\Support\PortfolioOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Public listing — published portfolios only.
     */
    public function index(Request $request): View
    {
        $filters = $this->filtersFrom($request);

        $portfolios = Portfolio::published()
            ->with('galery')
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    // tech_stacks is a JSON column, so a LIKE runs against its
                    // serialised text. Matches the original search behaviour.
                    ->orWhere('tech_stacks', 'like', "%{$search}%");
            }))
            ->when($filters['categories'], fn ($query, $categories) => $query->whereIn('category', $categories))
            ->latest()
            ->paginate($filters['limit'])
            ->withQueryString();

        return view('portfolio.index', [
            'portfolios' => $portfolios,
            'filters' => $filters,
            'categories' => PortfolioOptions::categories(),
        ]);
    }

    /**
     * Public detail. Drafts are only visible to their author or an admin,
     * matching the draft gate the original client component applied.
     */
    public function show(Request $request, string $uuid): View
    {
        $portfolio = Portfolio::with('galery', 'user')->findOrFail($uuid);

        $isDraft = $portfolio->status === PortfolioStatus::DRAFT;
        $canView = $request->user()
            && ($portfolio->created_by === $request->user()->id || $request->user()->isAdmin());

        abort_if($isDraft && ! $canView, 404);

        return view('portfolio.show', [
            'portfolio' => $portfolio,
        ]);
    }

    /**
     * Accepts `category` either as repeated `category[]` params or as a single
     * comma-separated value, so both form and hand-written URLs work.
     *
     * @return array{search: ?string, categories: array<int, string>, page: int, limit: int}
     */
    private function filtersFrom(Request $request): array
    {
        $raw = $request->query('category', []);

        $requested = is_array($raw)
            ? $raw
            : explode(',', (string) $raw);

        $categories = array_values(array_filter(
            array_map('strval', $requested),
            fn (string $value) => in_array($value, PortfolioOptions::categories(), true)
        ));

        $limit = (int) $request->query('limit', PortfolioOptions::DEFAULT_LIMIT);

        return [
            'search' => trim((string) $request->query('search', '')) ?: null,
            'categories' => $categories,
            'page' => max(1, (int) $request->query('page', 1)),
            'limit' => in_array($limit, PortfolioOptions::LIMIT_OPTIONS, true) ? $limit : PortfolioOptions::DEFAULT_LIMIT,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard CMS
    |--------------------------------------------------------------------------
    |
    | Every action here is scoped to the signed-in author. The original did the
    | same for read/update, and deliberately left the delete rule to the UI.
    |
    */

    public function dashboardIndex(Request $request): View
    {
        $filters = $this->filtersFrom($request);

        $statuses = array_values(array_filter(
            is_array($request->query('status')) ? $request->query('status') : explode(',', (string) $request->query('status', '')),
            fn ($value) => in_array($value, [PortfolioStatus::DRAFT->value, PortfolioStatus::PUBLISHED->value], true)
        ));

        $portfolios = Portfolio::ownedBy($request->user())
            ->with('galery')
            ->when($filters['search'], fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('tech_stacks', 'like', "%{$search}%");
            }))
            ->when($filters['categories'], fn ($query, $categories) => $query->whereIn('category', $categories))
            ->when($statuses, fn ($query) => $query->whereIn('status', $statuses))
            ->latest()
            ->paginate($filters['limit'])
            ->withQueryString();

        return view('dashboard.portfolio.index', [
            'portfolios' => $portfolios,
            'filters' => $filters + ['statuses' => $statuses],
            'categories' => PortfolioOptions::categories(),
            'statuses' => [
                ['value' => PortfolioStatus::PUBLISHED->value, 'label' => 'Published'],
                ['value' => PortfolioStatus::DRAFT->value, 'label' => 'Draft'],
            ],
        ]);
    }

    public function create(): View
    {
        return view('dashboard.portfolio.create', [
            'portfolio' => new Portfolio(['status' => PortfolioStatus::DRAFT]),
        ]);
    }

    public function store(PortfolioRequest $request, MediaService $media): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'galery_files', 'galery', 'remove_thumbnail']);
        $data['created_by'] = $request->user()->id;

        DB::transaction(function () use ($data, $request, $media) {
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $media->upload(
                    $request->file('thumbnail'),
                    config('media.portfolio_folder').'/thumbnails'
                )['url'];
            }

            $portfolio = Portfolio::create($data);

            foreach ($request->file('galery_files', []) as $file) {
                $portfolio->galery()->create([
                    'image_url' => $media->upload($file, config('media.portfolio_folder').'/gallery')['url'],
                ]);
            }
        });

        return redirect()
            ->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio berhasil dibuat');
    }

    public function edit(Request $request, string $uuid): View
    {
        return view('dashboard.portfolio.edit', [
            'portfolio' => $this->findOwned($request, $uuid),
        ]);
    }

    public function update(PortfolioRequest $request, string $uuid, MediaService $media): RedirectResponse
    {
        $portfolio = $this->findOwned($request, $uuid);
        $data = $request->safe()->except(['thumbnail', 'galery_files', 'galery', 'remove_thumbnail']);

        // `galery` holds the URLs the author chose to keep; anything stored but
        // absent from that list was removed in the form and gets deleted.
        $keep = collect($request->input('galery', []))
            ->filter()
            ->map(fn ($url) => trim((string) $url))
            ->values();

        DB::transaction(function () use ($portfolio, $data, $keep, $request, $media) {
            if ($request->hasFile('thumbnail')) {
                $media->delete($portfolio->thumbnail);
                $data['thumbnail'] = $media->upload(
                    $request->file('thumbnail'),
                    config('media.portfolio_folder').'/thumbnails'
                )['url'];
            } elseif ($request->boolean('remove_thumbnail')) {
                $media->delete($portfolio->thumbnail);
                // The column is NOT NULL, matching Prisma's `thumbnail String`,
                // so clearing it means an empty string. The cards already fall
                // back to a placeholder when the value is blank.
                $data['thumbnail'] = '';
            }

            $portfolio->update($data);

            $portfolio->galery()->whereNotIn('image_url', $keep)->get()
                ->each(function (PortfolioGalery $stale) use ($media) {
                    $media->delete($stale->image_url);
                    $stale->delete();
                });

            foreach ($request->file('galery_files', []) as $file) {
                $portfolio->galery()->create([
                    'image_url' => $media->upload($file, config('media.portfolio_folder').'/gallery')['url'],
                ]);
            }
        });

        return redirect()
            ->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio berhasil diperbarui');
    }

    public function destroy(Request $request, string $uuid, MediaService $media): RedirectResponse
    {
        $portfolio = $this->findOwned($request, $uuid);

        DB::transaction(function () use ($portfolio, $media) {
            $media->delete($portfolio->thumbnail);
            $portfolio->galery->each(fn ($image) => $media->delete($image->image_url));

            $portfolio->delete();
        });

        return redirect()
            ->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio berhasil dihapus');
    }

    private function findOwned(Request $request, string $uuid): Portfolio
    {
        $portfolio = Portfolio::with('galery')->findOrFail($uuid);

        abort_if(
            $portfolio->created_by !== $request->user()->id && ! $request->user()->isAdmin(),
            403
        );

        return $portfolio;
    }
}
