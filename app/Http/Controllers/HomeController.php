<?php

namespace App\Http\Controllers;

use App\Services\Language\Drivers\Translation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $translation;

    public function __construct(Translation $translation)
    {
        $this->translation = $translation;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // If user is authenticated
        if ($request->user()) {
            return redirect()->route('dashboard', [
                'company_uid' => $request->user()->currentCompany()->uid,
            ]);
        }

        return redirect()->route('login');
    }

    /**
     * Show the application demo page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function demo(Request $request)
    {
        // If demo mode is not active then deactivate demo page
        if (config('app.is_demo')) {
            return view('layouts.demo');
        }

        return redirect('/');
    }

    /**
     * Show the api documentation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function api_docs(Request $request)
    {
        return view('layouts.api_docs');
    }

    /**
     * Change language and store the locale pref in session.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function change_language(Request $request)
    {
        app()->setlocale($request->getLocale());
        session()->put('locale', $request->getLocale());

        return redirect()->back();
    }
}
