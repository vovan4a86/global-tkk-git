<header class="header">
    <div class="header__container container">
        <div class="header__brand">
            @if(!Route::is('main'))
                <a class="brand" href="{{ route('main') }}" title="На главную" aria-label="На главную">
                    <img class="brand__img no-select" src="/static/images/common/logo.svg" width="168" height="116"
                         alt="Агросталь-Комплект"/>
                    @if($slogan = S::get('header_slogan'))
                        <span class="brand__slogan">{{ $slogan }}</span>
                    @endif
                </a>
            @else
                <div class="brand">
                    <img class="brand__img no-select" src="/static/images/common/logo.svg" width="168" height="116"
                         alt="Агросталь-Комплект"/>
                    @if($slogan = S::get('header_slogan'))
                        <span class="brand__slogan">{{ $slogan }}</span>
                    @endif
                </div>
            @endif
        </div>
        <div class="header__top">
            <div class="header__city">
                <button class="city-btn btn-reset" type="button" data-src="{{ route('ajax.show-popup-cities') }}"
                        data-fancybox="data-cities" data-type="ajax">
                    <svg class="svg-sprite-icon icon-pin" width="1em" height="1em">
                        <use xlink:href="/static/images/sprite/symbol/sprite.svg#pin"></use>
                    </svg>
                    <span class="city-btn__label">Екатеринбург</span>
                </button>
            </div>
            <div class="header__columns">
                <div class="header__links">
                    <ul class="links list-reset">
                        @if($wa = S::get('soc_wa'))
                            <li class="links__item">
                                <a class="links__link" href="{{ $wa }}">
                                    <span class="links__icon lazy" data-bg="/static/images/common/ico_wa.svg"></span>
                                    <span class="links__label">whatsapp</span>
                                </a>
                            </li>
                        @endif
                        @if($tg = S::get('soc_tg'))
                            <li class="links__item">
                                <a class="links__link" href="{{ $tg }}">
                                    <span class="links__icon lazy" data-bg="/static/images/common/ico_tg.svg"></span>
                                    <span class="links__label">telegram</span>
                                </a>
                            </li>
                        @endif
                        @if($email = S::get('header_email'))
                            <li class="links__item">
                                <a class="links__link" href="mailto:{{ $email }}">
                                        <span class="links__icon lazy"
                                              data-bg="/static/images/common/ico_mail.svg"></span>
                                    <span class="links__label">{{ $email }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                @if($phone = S::get('header_phone'))
                    <div class="header__phone">
                        <a class="phone" href="tel:{{ SiteHelper::clearPhone($phone) }}" title="Позвонить нам"
                           aria-label="Позвонить нам">
								<span class="phone__body">
									<svg class="svg-sprite-icon icon-phone" width="1em" height="1em">
										<use xlink:href="/static/images/sprite/symbol/sprite.svg#phone"></use>
									</svg>
									<span class="phone__num">{{ $phone }}</span>
								</span>
                            <span class="phone__label">Звонок по России бесплатный</span>
                        </a>
                    </div>
                @endif
                <div class="header__action">
                    <button class="btn btn-reset" type="button" data-popup="data-popup" data-src="#write-popup"
                            aria-label="Написать нам">Написать нам
                    </button>
                </div>
            </div>
        </div>
        <div class="header__body">
            <nav class="header__nav" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"
                 aria-label="Меню">
                <ul class="header__nav-list" itemprop="about" itemscope="itemscope"
                    itemtype="https://schema.org/ItemList">
                    @foreach($header_menu as $menu_item)
                        <li class="header__nav-item" itemprop="itemListElement" itemscope="itemscope"
                            itemtype="https://schema.org/ItemList">
                            <a class="header__nav-link" href="{{ $menu_item->url }}" itemprop="url">
                                <span>{{ $menu_item->name }}</span>
                                <meta itemprop="name" content="{{ $menu_item->name }}"/>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>
</header>