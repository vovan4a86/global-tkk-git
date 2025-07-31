@extends('template')
@section('content')
    <main>
        <!--section.hero-->
        <section class="hero">
            <div class="hero__top lazy" data-bg="/static/images/common/hero-bg.jpg">
                <div class="hero__container container">
                    <div class="hero__head">
                        @if(!Route::is('main'))
                            <a class="hero__logo" href="{{ route('main') }}" title="На главную">
                                <img class="hero__logo-img bounce" src="/static/images/common/logo.png" width="400"
                                     height="400" alt="logo"/>
                            </a>
                            <a class="hero__mobile" href="{{ route('main') }}" title="На главную">
                                <img class="hero__mobile-img bounce" src="/static/images/common/logo--mobile.png"
                                     width="100" height="100" alt="logo"/>
                            </a>
                        @else
                            <div class="hero__logo">
                                <img class="hero__logo-img bounce" src="/static/images/common/logo.png" width="400"
                                     height="400" alt="logo"/>
                            </div>
                            <div class="hero__mobile">
                                <img class="hero__mobile-img bounce" src="/static/images/common/logo--mobile.png"
                                     width="100" height="100" alt="logo"/>
                            </div>
                        @endif
                        <button class="hero__burger hamburger hamburger--collapse" type="button"
                                aria-label="Открыть меню" :class="overlayIsOpen &amp;&amp; 'is-active'"
                                @click="overlayIsOpen = true">
								<span class="hamburger-box">
									<span class="hamburger-inner"></span>
								</span>
                        </button>
                        @if(isset($header_menu) && count($header_menu))
                            <nav class="hero__nav" itemscope="itemscope"
                                 itemtype="https://schema.org/SiteNavigationElement" aria-label="Меню">
                                <ul class="hero__nav-list" itemprop="about" itemscope="itemscope"
                                    itemtype="https://schema.org/ItemList">
                                    @foreach($header_menu as $menu_item)
                                        <li class="hero__nav-item" itemprop="itemListElement" itemscope="itemscope"
                                            itemtype="https://schema.org/ItemList">
                                            <a class="hero__nav-link"
                                               href="{{ starts_with($menu_item->alias, '#') ? $menu_item->alias : $menu_item->url }}"
                                               itemprop="url">
                                                <span>{{ $menu_item->name }}</span>
                                                <meta itemprop="name" content="{{ $menu_item->name }}"/>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        @endif
                        <div class="hero__info">
                            @if($phone = S::get('header_phone'))
                                <a class="hero__phone phone"
                                   href="tel:{{ SiteHelper::clearPhone($phone) }}">{{ $phone }}</a>
                            @endif
                            <div class="hero__links">
                                <ul class="links">
                                    @if($vk = S::get('soc_vk'))
                                        <li class="links__item">
                                            <a class="links__link links__link--vk" href="{{ $vk  }}"
                                               target="_blank" title="Вконтакте">
                                                <svg class="svg-sprite-icon icon-vk" width="1em" height="1em">
                                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#vk"></use>
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                    @if($tg= S::get('soc_tg'))
                                        <li class="links__item">
                                            <a class="links__link links__link--telegram" href="{{ $tg }}"
                                               target="_blank" title="Telegram">
                                                <svg class="svg-sprite-icon icon-tg" width="1em" height="1em">
                                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#tg"></use>
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                    @if($inst= S::get('soc_inst'))
                                        <li class="links__item">
                                            <a class="links__link links__link--insta" href="{{ $inst }}"
                                               target="_blank" title="Instagram">
                                                <svg class="svg-sprite-icon icon-insta" width="1em" height="1em">
                                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#insta"></use>
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($slogan = S::get('main_slogan'))
                <div class="hero__body lazy" data-bg="{{ S::fileSrc($slogan['image']) }}">
                    <div class="hero__slogan">{{ $slogan['text'] }}</div>
                </div>
            @endif
            @if($phone = S::get('header_phone'))
                <div class="hero__actions">
                    <a class="hero__link button bounce" href="tel:{{ SiteHelper::clearPhone($phone) }}"
                       title="Позвонить">Связаться</a>
                </div>
            @endif
        </section>
        @if($main_features = S::get('main_features'))
            <section class="s-feat">
                <div class="s-feat__container container">
                    <div class="s-feat__grid">
                        @foreach($main_features as $f)
                            <div class="s-feat__item">
                                @if($f['icon'])
                                    <div class="s-feat__icon lazy" data-bg="{{ S::fileSrc($f['icon']) }}"></div>
                                @endif
                                <div class="s-feat__title">{{ $f['text'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        @if(count($uslugi_gallery_items) || count($uslugi_icons_items))
            <section class="s-srv">
                <div class="s-srv__container container">
                    <div class="s-srv__title" id="uslugi">Наши услуги:</div>
                    @if(count($uslugi_gallery_items))
                        <div class="s-srv__grid">
                            @foreach($uslugi_gallery_items as $gal_item)
                                <a class="s-srv__link" href="{{ $gal_item->src }}" data-fancybox="gallery"
                                   data-caption="{{ isset($gal_item->data) ? array_get($gal_item->data, 'title') : '' }}"
                                   title="{{ isset($gal_item->data) ? array_get($gal_item->data, 'title') : '' }}">
                                    <img class="s-srv__img" src="{{ $gal_item->thumb(2) }}" width="277" height="208"
                                         alt="{{ isset($gal_item->data) ? array_get($gal_item->data, 'title') : '' }}"
                                         loading="lazy"/>
                                    <svg class="svg-sprite-icon icon-zoom" width="1em" height="1em">
                                        <use xlink:href="/static/images/sprite/symbol/sprite.svg#zoom"></use>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    @if($uslugi_icons_items)
                        <div class="s-srv__grid">
                            @foreach($uslugi_icons_items as $gal_icon)
                                <div class="s-srv__col">
                                    <div class="s-srv__preview">
                                        <img class="s-srv__view" src="{{ $gal_icon->thumb(2) }}"
                                             width="265" height="265"
                                             alt="{{ isset($gal_icon->data) ? array_get($gal_icon->data, 'title') : '' }}"
                                             loading="lazy"/>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if(isset($main_about) && $main_about)
            <section class="s-about" id="okompanii">
                <div class="s-about__bg lazy" data-bg="/static/images/common/about-bg.jpg"></div>
                <div class="s-about__container container">
                    <div class="s-about__wrapper">
                        <div class="s-about__content text-block">
                            {!! $main_about['text'] !!}
                            @if($main_about['btn_name'] && $main_about['phone'])
                                <div class="s-about__actions">
                                    <a class="button bounce"
                                       href="tel:{{ SiteHelper::clearPhone($main_about['phone']) }}"
                                       title="{{ $main_about['btn_name'] }}">{{ $main_about['btn_name'] }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($main_about['image'])
                        <div class="s-about__view">
                            <img class="s-about__img" src="{{ S::fileSrc($main_about['image']) }}" width="550"
                                 height="550" alt="О нас" loading="lazy"/>
                        </div>
                    @endif
                </div>
            </section>
        @endif
        <!--section.s-text-->
        <section class="s-text">
            <div class="s-text__container container">
                @if($uslugi = S::get('main_before_why'))
                    <div class="s-text__grid">
                        @foreach($uslugi as $item)
                            <div class="s-text__item">
                                <p>{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="s-text__body text-block">
                    @if($title = S::get('main_why_header'))
                        <h3 class="h3">{{ $title }}</h3>
                    @endif
                    @if($why_items = S::get('main_why'))
                        <div class="s-text__columns">
                            @foreach($why_items as $why_item)
                                <div class="s-text__col text-block">
                                    @if($why_item['title'])
                                        <h4 class="h4">{{ $why_item['title'] }}</h4>
                                    @endif
                                    @if($why_item['text'])
                                        {!! $why_item['text'] !!}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
        @if(isset($main_brand) && $main_brand)
            <section class="s-brand">
                <div class="s-brand__container container container--wide">
                    <div class="s-brand__container container">
                        <div class="s-brand__grid">
                            <div class="s-brand__text text-block">
                                @if($main_brand['title'])
                                    <h2 class="h3">{{ $main_brand['title'] }}</h2>
                                @endif
                                @if($main_brand['text'])
                                    {!! $main_brand['text'] !!}
                                @endif
                            </div>
                            <div class="s-brand__view">
                                @if($main_brand['image'])
                                    <img class="s-brand__img" src="{{ S::fileSrc($main_brand['image']) }}"
                                         width="800" height="400" alt="Компания GLOBAL LOGISTIC" loading="lazy"/>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        @endif
        @if(isset($main_trans) && $main_trans)
            <section class="s-trans">
                <div class="s-trans__bg lazy" data-bg="/static/images/common/about-bg.jpg"></div>
                <div class="s-trans__container container">
                    <div class="s-trans__grid">
                        <div class="s-trans__view">
                            @if($main_trans['image'])
                                <img class="s-trans__img" src="{{ S::fileSrc($main_trans['image']) }}"
                                     width="610" height="610" alt="Ж/Д перевозки" loading="lazy"/>
                            @endif
                        </div>
                        <div class="s-trans__body text-block">
                            @if($main_trans['title'])
                                <h2 class="h3" style="text-align:center">{{ $main_trans['title'] }}</h2>
                            @endif
                            @if($main_trans['text'])
                                {!! $main_trans['text'] !!}
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif
        @if(isset($main_faq) && count($main_faq))
            <section class="s-faq">
                <div class="s-faq__bg lazy" data-bg="/static/images/common/about-bg.jpg"></div>
                <div class="s-faq__container container container--wide">
                    <div class="s-faq__container container">
                        <div class="s-faq__head text-block">
                            <h2 class="h2">Часто задаваемые вопросы:</h2>
                        </div>
                        <div class="s-faq__list">
                            @foreach($main_faq as $item)
                                <div class="s-faq__item" x-data="{ isOpen: false }">
                                    <button class="s-faq__button btn-reset" type="button"
                                            aria-label="Открыть / закрыть вопрос"
                                            @click="isOpen = !isOpen" :class="isOpen &amp;&amp; 'is-active'">
                                        <svg class="svg-sprite-icon icon-caret" width="1em" height="1em">
                                            <use xlink:href="/static/images/sprite/symbol/sprite.svg#caret"></use>
                                        </svg>
                                        {{ $item['title'] }}
                                    </button>
                                    <div class="s-faq__answer text-block" x-show="isOpen" x-transition="">
                                        {!! $item['text'] !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <!--section.s-form-->
        <section class="s-form">
            <div class="s-form__container container">
                <div class="s-form__head text-block">
                    <h2 class="h3" style="text-align:center">Любой груз, любые расстояния, мы доставляем — вы
                        отдыхаете!</h2>
                </div>
                <div class="s-form__form">
                    <!--form.form(action="#")-->
                    <form class="form" id="calc" action="{{ route('ajax.calc') }}">
                        <div class="form__wrapper">
                            <div class="form__head">
                                <div class="form__title">Расчёт стоимости</div>
                                <div class="form__text">Оставьте заявку и узнайте точную стоимость перевозки</div>
                            </div>
                            <div class="form__fields">
                                <label class="form__field">
                                    <span class="form__field-label">Ваше имя</span>
                                    <input class="form__input" type="text" name="name"/>
                                </label>
                                <label class="form__field">
                                    <span class="form__field-label">Ваш Email</span>
                                    <input class="form__input" type="text" name="email"/>
                                </label>
                                <label class="form__field">
                                    <span class="form__field-label" data-required="*">Телефон</span>
                                    <input class="form__input" type="tel" name="phone" required="required"/>
                                </label>
                                <label class="form__field">
                                    <span class="form__field-label">Ваш запрос</span>
                                    <textarea class="form__input" name="text" rows="3"></textarea>
                                </label>
                            </div>
                            <div class="form__actions">
                                <button class="form__btn btn-reset" name="submit" aria-label="Рассчитать">Рассчитать
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
@stop
