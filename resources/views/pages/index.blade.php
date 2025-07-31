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
        <!--section.s-about-->
        <section class="s-about" id="okompanii">
            <div class="s-about__bg lazy" data-bg="/static/images/common/about-bg.jpg"></div>
            <div class="s-about__container container">
                <div class="s-about__wrapper">
                    <div class="s-about__content text-block">
                        <h2 class="h2" style="text-align: center">О нас</h2>
                        <p>GLOBAL LOGISTIC успешно осуществляет деятельность по транспортировке грузов на различные
                            направления по территории России, стран СНГ и Китая.</p>
                        <p>В числе наших клиентов крупные отечественные производственные и торговые предприятия.</p>
                        <p>Наша компания осуществляет полный комплекс мультимодальных перевозок, сочетая перевозки
                            железнодорожным, морским и автомобильным транспортом в единую, эффективную схему доставки
                            груза по всему миру.</p>
                        <p>За годы успешной работы был накоплен профессиональный багаж знаний для решения самых сложных
                            задач связанных с доставкой различных типов грузов в междугороднем и международном
                            сообщении</p>
                        <div class="s-about__actions">
                            <a class="button bounce" href="tel:+73437770065" title="Позвонить">Связаться</a>
                        </div>
                    </div>
                </div>
                <div class="s-about__view">
                    <img class="s-about__img" src="/static/images/common/about-view.jpg" width="550" height="550"
                         alt="О нас" loading="lazy"/>
                </div>
            </div>
        </section>
        <!--section.s-text-->
        <section class="s-text">
            <div class="s-text__container container">
                <div class="s-text__grid">
                    <div class="s-text__item">
                        <p>Контейнерные перевозки</p>
                    </div>
                    <div class="s-text__item">
                        <p>Морские перевозки</p>
                    </div>
                    <div class="s-text__item">
                        <p>Авиаперевозки</p>
                    </div>
                    <div class="s-text__item">
                        <p>Таможенное оформление</p>
                    </div>
                </div>
                <div class="s-text__body text-block">
                    <h3 class="h3">Почему именно мы?</h3>
                    <div class="s-text__columns">
                        <div class="s-text__col text-block">
                            <h4 class="h4">Профессионализм</h4>
                            <p>— Перевозки любой сложности.</p>
                            <p>— Возможность разработки специальных решений для новых нестандартных задач.</p>
                            <p>— Полный комплекс услуг перевозки грузов</p>
                        </div>
                        <div class="s-text__col text-block">
                            <h4 class="h4">Выгода</h4>
                            <p>— Подбор для клиента оптимального маршрута по срокам и стоимости доставки.</p>
                            <p>— Находим самые лучшие решения по перевозке грузов, что позволяет экономить деньги нашим
                                клиентам</p>
                        </div>
                        <div class="s-text__col text-block">
                            <h4 class="h4">Подход</h4>
                            <p>— Поддержка персонального менеджера 24/7 для каждого клиента</p>
                            <p>— Уважаем интересы и время наших клиентов</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--section.s-brand-->
        <section class="s-brand">
            <div class="s-brand__container container container--wide">
                <div class="s-brand__container container">
                    <div class="s-brand__grid">
                        <div class="s-brand__text text-block">
                            <h2 class="h3">Компания GLOBAL LOGISTIC</h2>
                            <p>Оказываем полный комплекс транспортно-экспедиционных услуг по&nbsp;перевозке грузов
                                морским, автомобильным, железнодорожным и&nbsp;воздушным транспортом в&nbsp;междугороднем
                                и&nbsp;международном сообщении, с&nbsp;оказанием полного спектра терминально-складских
                                услуг</p>
                        </div>
                        <div class="s-brand__view">
                            <img class="s-brand__img" src="/static/images/common/brand.jpg" width="800" height="400"
                                 alt="Компания GLOBAL LOGISTIC" loading="lazy"/>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--section.s-trans-->
        <section class="s-trans">
            <div class="s-trans__bg lazy" data-bg="/static/images/common/about-bg.jpg"></div>
            <div class="s-trans__container container">
                <div class="s-trans__grid">
                    <div class="s-trans__view">
                        <img class="s-trans__img" src="/static/images/common/transport.jpg" width="610" height="610"
                             alt="Ж/Д перевозки" loading="lazy"/>
                    </div>
                    <div class="s-trans__body text-block">
                        <h2 class="h3" style="text-align:center">Ж/Д перевозки</h2>
                        <p>Мы на выгодных условиях организуем ж/д перевозки. Этот способ доставки востребован по причине
                            финансовой выгоды и эффективности на дальних расстояниях.</p>
                        <p>На территории России железнодорожные перевозки грузов играют особую роль в развитии
                            экономики. Это обусловлено удаленностью многих производственных организаций и добывающих
                            предприятий от рынка сбыта, и развитостью железнодорожной сети. Впрочем,
                            этот способ доставки подходит не только для отправки крупных партий продукции от одного
                            заказчика, но и для сборных грузов от нескольких клиентов.</p>
                    </div>
                </div>
            </div>
        </section>
        <!--section.s-faq-->
        <section class="s-faq">
            <div class="s-faq__bg lazy" data-bg="/static/images/common/about-bg.jpg"></div>
            <div class="s-faq__container container container--wide">
                <div class="s-faq__container container">
                    <div class="s-faq__head text-block">
                        <h2 class="h2">Часто задаваемые вопросы:</h2>
                    </div>
                    <div class="s-faq__list">
                        <div class="s-faq__item" x-data="{ isOpen: false }">
                            <button class="s-faq__button btn-reset" type="button" aria-label="Открыть / закрыть вопрос"
                                    @click="isOpen = !isOpen" :class="isOpen &amp;&amp; 'is-active'">
                                <svg class="svg-sprite-icon icon-caret" width="1em" height="1em">
                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#caret"></use>
                                </svg>
                                Обязательна ли услуга обрешётки?
                            </button>
                            <div class="s-faq__answer text-block" x-show="isOpen" x-transition="">
                                <p>Обрешетка или деревянный каркас — жесткая упаковка из деревянных брусков и досок для
                                    минимизации повреждений грузов. Защищает от любых механических воздействий,
                                    предотвращает соприкосновение с другими грузами или стенками транспортного
                                    средства.</p>
                                <p>Применяется для особо ценных грузов и хрупких грузов. Определенные типы грузов
                                    принимаются к перевозке только при условии наличия обрешетки.</p>
                            </div>
                        </div>
                        <div class="s-faq__item" x-data="{ isOpen: false }">
                            <button class="s-faq__button btn-reset" type="button" aria-label="Открыть / закрыть вопрос"
                                    @click="isOpen = !isOpen" :class="isOpen &amp;&amp; 'is-active'">
                                <svg class="svg-sprite-icon icon-caret" width="1em" height="1em">
                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#caret"></use>
                                </svg>
                                В чем отличие морского контейнера от железнодорожного?
                            </button>
                            <div class="s-faq__answer text-block" x-show="isOpen" x-transition="">
                                <p>Главное отличие морского контейнера от железнодорожного – стоимость изготовления.
                                    Боксы для наземных перевозок стоят ощутимо дешевле из-за применения более тонкого
                                    металла и традиционного лакокрасочного покрытия.</p>
                                <p>Также железнодорожная тара характеризуется меньшими размерами и
                                    грузоподъемностью.</p>
                                <p>Водные контейнеры более герметичны, прочны, срок их эксплуатации в среднем выше на 5
                                    лет.</p>
                                <p>Выбор тары зависит от маршрута и дальности перевозок, типа и количества багажа. Для
                                    насыпных товаров требуется вертикальная загрузка, следовательно, необходима
                                    герметичная конструкция со съемной или натяжной крышей. Машины и крупные механизмы
                                    проще перемещать на контейнерных модулях FlatRack – платформах с торцевыми стенками,
                                    которые применяются именно в железнодорожных перевозках. Высокие грузы перевозят в
                                    боксах с открытым верхом.</p>
                            </div>
                        </div>
                        <div class="s-faq__item" x-data="{ isOpen: false }">
                            <button class="s-faq__button btn-reset" type="button" aria-label="Открыть / закрыть вопрос"
                                    @click="isOpen = !isOpen" :class="isOpen &amp;&amp; 'is-active'">
                                <svg class="svg-sprite-icon icon-caret" width="1em" height="1em">
                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#caret"></use>
                                </svg>
                                Какой контейнер выбрать ? Морской или железнодорожный?
                            </button>
                            <div class="s-faq__answer text-block" x-show="isOpen" x-transition="">
                                <p>Если вы перевозите небольшой груз только в пределах одного региона или страны –
                                    выбирайте железнодорожный контейнер.</p>
                                <p>Если, ваш груз выходит за пределы объёма 5 тонн, и вы планируете доставлять груз как,
                                    по наземному сообщению, так и через море – выбирайте морской контейнер. Его можно
                                    использовать при передвижении по железной дороге.</p>
                            </div>
                        </div>
                        <div class="s-faq__item" x-data="{ isOpen: false }">
                            <button class="s-faq__button btn-reset" type="button" aria-label="Открыть / закрыть вопрос"
                                    @click="isOpen = !isOpen" :class="isOpen &amp;&amp; 'is-active'">
                                <svg class="svg-sprite-icon icon-caret" width="1em" height="1em">
                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#caret"></use>
                                </svg>
                                Сколько паллет входит в контейнер?
                            </button>
                            <div class="s-faq__answer text-block" x-show="isOpen" x-transition="">
                                <p>В один сухогрузный контейнер входит:</p>
                                <ul>
                                    <li>11 европаллет</li>
                                    <li>9 или 10 поддонов типа FIN (количество зависит от выбранной схемы размещения в
                                        контейнере)
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="s-faq__item" x-data="{ isOpen: false }">
                            <button class="s-faq__button btn-reset" type="button" aria-label="Открыть / закрыть вопрос"
                                    @click="isOpen = !isOpen" :class="isOpen &amp;&amp; 'is-active'">
                                <svg class="svg-sprite-icon icon-caret" width="1em" height="1em">
                                    <use xlink:href="/static/images/sprite/symbol/sprite.svg#caret"></use>
                                </svg>
                                Какая стоимость грузоперевозки?
                            </button>
                            <div class="s-faq__answer text-block" x-show="isOpen" x-transition="">
                                <p>Стоимость грузоперевозки рассчитывается индивидуально, в зависимости от километража
                                    доставки, а также его параметров: массы, габаритов.</p>
                                <p>Для расчета просим связаться с нами по телефону:&nbsp;
                                    <a href="tel:+73437770065">+7 (343) 777-00-65</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--section.s-form-->
        <section class="s-form">
            <div class="s-form__container container">
                <div class="s-form__head text-block">
                    <h2 class="h3" style="text-align:center">Любой груз, любые расстояния, мы доставляем — вы
                        отдыхаете!</h2>
                </div>
                <div class="s-form__form">
                    <!--form.form(action="#")-->
                    <form class="form" action="#">
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
                                    <textarea class="form__input" name="message" rows="3"></textarea>
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
