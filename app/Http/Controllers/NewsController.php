<?php namespace App\Http\Controllers;

use App;
use Fanky\Admin\Models\News;
use Fanky\Admin\Models\Page;
use Fanky\Auth\Auth;
use S;
use Settings;
use View;

class NewsController extends Controller {
	public $bread = [];
	protected $news_page;

	public function __construct() {
		$this->news_page = Page::whereAlias('news')
			->get()
			->first();

		$this->bread[] = [
            'url'  => $this->news_page['url'],
            'name' => $this->news_page['name']
		];
	}

	public function index() {
		$page = $this->news_page;
		if (!$page)
			abort(404, 'Страница не найдена');

		$bread = $this->bread;
        $page->ogGenerate();
        $page->setSeo();

        if (count(request()->query())) {
            View::share('canonical', $this->news_page->alias);
        }

        $items = News::public()
            ->orderByDesc('date')
            ->paginate(S::get('news_per_page', 6));

        return view('news.index', [
            'h1'    => $page->getH1(),
            'bread' => $bread,
            'items' => $items,
        ]);
	}

	public function item($alias) {
		$item = News::whereAlias($alias)->public()->first();
        if (!$item) abort(404);

        Auth::init();
        if (Auth::user() && Auth::user()->isAdmin) {
            View::share('admin_edit_link', route('admin.news.edit', [$item->id]));
        }
        $bread = $this->bread;
        $bread[] = [
            'name' => $item->name,
            'url' => $item->url
        ];

        $item->setSeo();
        $item->ogGenerate();

		return view('news.item', [
            'bread'       => $bread,
			'item'        => $item,
            'h1'          => $item->getH1(),
			'text'        => $item->text,
		]);
	}
}
