<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function services(): View
    {
        return view('pages.services', [
            'services' => SiteContent::serviceDetails(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'team' => SiteContent::team(),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'accessKey' => config('services.web3forms.access_key'),
        ]);
    }

    public function portfolio(): View
    {
        return view('pages.portfolio', [
            'featured' => Portfolio::query()
                ->published()
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    public function buyEbook(): View
    {
        return view('pages.buy-ebook', [
            'ebooks' => SiteContent::ebooks(),
        ]);
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);
        $accessKey = config('services.web3forms.access_key');
        if (blank($accessKey)) {
            return back()->withErrors([
                'message' => 'The contact form is not configured yet. Set WEB3FORMS_ACCESS_KEY in the .env file.',
            ]);
        }
        $response = Http::asForm()->post('https://api.web3forms.com/submit', [
            'access_key' => $accessKey,
            'subject' => 'New message from PanDev',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
        ]);

        return $response->successful()
            ? back()->with('success', 'Your message has been sent. Thank you!')
            : back()->withErrors(['message' => 'The message could not be sent. Please try again.']);
    }
}
