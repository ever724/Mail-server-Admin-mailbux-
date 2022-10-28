<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\Language\Drivers\Translation;
use Illuminate\Http\Request;

class PageController extends Controller
{
    private $translation;

    public function __construct(Translation $translation)
    {
        $this->translation = $translation;
    }

    /**
     * Show the page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $page = Page::findBySlug($request->slug);
        $theme = get_system_setting('theme');

        return view("themes.{$theme}.page", [
            'languages' => $this->translation->allLanguages(),
            'page_title' => $page->name,
            'page_content' => $page->content,
        ]);
    }
}
