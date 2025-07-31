<?php namespace App\Http\Controllers;

use Fanky\Admin\Models\Catalog;
use Fanky\Admin\Models\Gallery;
use Fanky\Admin\Models\Page;
use S;

class WelcomeController extends Controller {
    public function index() {
        /** @var Page $page */
        $page = Page::find(1);
        $page->ogGenerate();
        $page->setSeo();

        $uslugi_gallery = Gallery::whereCode('main_uslugi_gallery')->first();
        $uslugi_gallery_items = $uslugi_gallery->items;
        $uslugi_icons = Gallery::whereCode('main_uslugi_iocns')->first();
        $uslugi_icons_items = $uslugi_icons->items;

        $main_about = S::get('main_about');
        $main_brand = S::get('main_brand');
        $main_trans = S::get('main_trans');
        $main_faq = S::get('main_faq');

        return response()->view('pages.index', [
            'page' => $page,
            'text' => $page->text,
            'h1' => $page->getH1(),
            'uslugi_gallery_items' => $uslugi_gallery_items,
            'uslugi_icons_items' => $uslugi_icons_items,
            'main_about' => $main_about,
            'main_brand' => $main_brand,
            'main_trans' => $main_trans,
            'main_faq' => $main_faq,
        ]);
    }
}
