<footer class="footer">
    <div class="footer__container container">
        <div class="footer__body">
            <div class="footer__info">
                @if(!Route::is('main'))
                    <a class="footer__brand" href="{{ route('main') }}" title="На главную">
                        <img class="footer__brand-img" src="/static/images/common/logo--white.svg" width="117"
                             height="81" loading="lazy" alt="Агросталь-Комплект"/>
                        @if($slogan = S::get('footer_slogan'))
                            <span class="footer__brand-label">{{ $slogan }}</span>
                        @endif
                    </a>
                @else
                    <div class="footer__brand">
                        <img class="footer__brand-img" src="/static/images/common/logo--white.svg" width="117"
                             height="81" loading="lazy" alt="Агросталь-Комплект"/>
                        @if($slogan = S::get('footer_slogan'))
                            <span class="footer__brand-label">{{ $slogan }}</span>
                        @endif
                    </div>
                @endif
                <nav class="footer__nav">
                    <ul class="footer__nav-list">
                        @foreach($footer_menu as $item)
                            <li class="footer__nav-item">
                                <a class="footer__nav-link" href="{{ $item->url }}">
                                    <span>{{ $item->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            <div class="footer__data">
                <div class="footer__row">
                    <div class="footer__links">
                        <ul class="links list-reset">
                            @if($wa = S::get('soc_wa'))
                                <li class="links__item">
                                    <a class="links__link" href="{{ $wa }}">
                                        <span class="links__icon lazy"
                                              data-bg="/static/images/common/ico_wa.svg"></span>
                                        <span class="links__label">whatsapp</span>
                                    </a>
                                </li>
                            @endif
                            @if($tg = S::get('soc_tg'))
                                <li class="links__item">
                                    <a class="links__link" href="{{ $tg }}">
                                        <span class="links__icon lazy"
                                              data-bg="/static/images/common/ico_tg.svg"></span>
                                        <span class="links__label">telegram</span>
                                    </a>
                                </li>
                            @endif
                            @if($email = S::get('footer_email'))
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
                    @if($phone = S::get('footer_phone'))
                        <div class="footer__phone">
                            <a class="phone" href="tel:{{ SiteHelper::clearPhone($phone) }}" title="Позвонить нам" aria-label="Позвонить нам">
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
                </div>
                <div class="footer__row">
                    <div class="footer__actions">
                        <button class="link btn-reset link--light" type="button" data-popup="data-popup"
                                data-src="#order-popup" aria-label="Оставить заявку">Оставить заявку
                        </button>
                        <button class="link btn-reset link--white" type="button" data-popup="data-popup"
                                data-src="#callback-popup" aria-label="Заказать звонок">Заказать звонок
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <a class="footer__link" href="{{ url('policy') }}">Политика конфиденциальности</a>
            <a class="footer__link" href="{{ url('personal') }}">Согласие пользователя сайта на обработку персональных
                данных</a>
            <a class="footer__link" href="{{ url('cookie') }}">Согласие на обработку файлов cookies</a>
        </div>
    </div>
</footer>