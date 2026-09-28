<?php

namespace App\Http\Controllers;

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
        return view('pages.contact');
    }

    public function portfolio(): View
    {
        return view('pages.portfolio');
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
                'message' => 'Formulir kontak belum dikonfigurasi. Set WEB3FORMS_ACCESS_KEY di file .env.',
            ]);
        }

        $response = Http::asForm()->post('https://api.web3forms.com/submit', [
            'access_key' => $accessKey,
            'subject' => 'Email baru dari PanDev',
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
        ]);

        return $response->successful()
            ? back()->with('success', 'Email berhasil dikirim. Terima kasih!')
            : back()->withErrors(['message' => 'Email gagal dikirim. Silakan coba lagi.']);
    }
}
