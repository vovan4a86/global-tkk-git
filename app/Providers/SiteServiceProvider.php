<?php
namespace App\Providers;

use Cache;
use Illuminate\Support\ServiceProvider;
use View;
use Fanky\Admin\Models\Page;

class SiteServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // пререндер для шаблона
        View::composer(['pages.index'], function (\Illuminate\View\View $view) {
            $header_menu = Cache::get('header_menu', collect());
            if (!count($header_menu)) {
                $header_menu = Page::query()
                    ->public()
                    ->where('on_header', 1)
                    ->orderBy('order')
                    ->get();
                Cache::add('on_header', $header_menu, now()->addMinutes(60));
            }
//            $footer_menu = Cache::get('footer_menu', collect());
//            if(!count($footer_menu)) {
//                $footer_menu = Page::query()
//                    ->public()
//                    ->where('parent_id', 1)
//                    ->where('on_footer', 1)
//                    ->orderBy('order')
//                    ->get();
//                Cache::add('footer_menu', $footer_menu, now()->addMinutes(60));
//            }

            $view->with(
                compact(
                    'header_menu'
                )
            );
        });

//        View::composer(
//            ['errors.404'],
//            function ($view) {
//                $h1 =  'Страница не найдена';
//                $bread[] = [
//                    'name' => 'Страница не найдена',
//                    'url' => 'javascript:void(0)'
//                ];
//                \SEO::setTitle($h1);
//
//                $view->with(
//                    compact(
//                        'h1',
//                                'bread'
//                    )
//                );
//            }
//        );
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('settings', function () {
            return new \App\Classes\Settings();
        });
        $this->app->bind('sitehelper', function () {
            return new \App\Classes\SiteHelper();
        });
        $this->app->alias('settings', \App\Facades\Settings::class);
        $this->app->alias('sitehelper', \App\Facades\SiteHelper::class);
    }
}
