<div class="c-overlay" :class="overlayIsOpen &amp;&amp; 'is-active'" x-cloak="x-cloak">
    <div class="c-overlay__item">
        <a class="c-overlay__logo" href="javascript:void(0)" title="На главную">
            <img class="c-overlay__logo-img bounce" src="static/images/common/logo--mobile.png" width="100" height="100" alt="logo" />
        </a>
        <button class="c-overlay__close btn-reset" type="button" aria-label="Закрыть меню" @click="overlayIsOpen = false">
            <svg class="svg-sprite-icon icon-close" width="1em" height="1em">
                <use xlink:href="static/images/sprite/symbol/sprite.svg#close"></use>
            </svg>
        </button>
    </div>
    <div class="c-overlay__item">
        <nav class="c-overlay__nav">
            <ul class="c-overlay__nav-list">
                <li class="c-overlay__nav-item">
                    <a class="c-overlay__nav-link" href="javascript:void(0)">
                        <span>Наши услуги</span>
                    </a>
                </li>
                <li class="c-overlay__nav-item">
                    <a class="c-overlay__nav-link" href="javascript:void(0)">
                        <span>О компании</span>
                    </a>
                </li>
                <li class="c-overlay__nav-item">
                    <a class="c-overlay__nav-link" href="javascript:void(0)">
                        <span>Новости</span>
                    </a>
                </li>
                <li class="c-overlay__nav-item">
                    <a class="c-overlay__nav-link" href="javascript:void(0)">
                        <span>Контакты</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="c-overlay__item">
        <a class="c-overlay__phone phone" href="tel:+73437770065">+7 (343) 7770065</a>
    </div>
    <div class="c-overlay__item">
        <ul class="links">
            <li class="links__item">
                <a class="links__link links__link--vk" href="https://vk.com/club222639503" target="_blank" title="Вконтакте">
                    <svg class="svg-sprite-icon icon-vk" width="1em" height="1em">
                        <use xlink:href="static/images/sprite/symbol/sprite.svg#vk"></use>
                    </svg>
                </a>
            </li>
            <li class="links__item">
                <a class="links__link links__link--telegram" href="javascript:void(0)" target="_blank" title="Telegram">
                    <svg class="svg-sprite-icon icon-tg" width="1em" height="1em">
                        <use xlink:href="static/images/sprite/symbol/sprite.svg#tg"></use>
                    </svg>
                </a>
            </li>
            <li class="links__item">
                <a class="links__link links__link--insta" href="javascript:void(0)" target="_blank" title="Instagram">
                    <svg class="svg-sprite-icon icon-insta" width="1em" height="1em">
                        <use xlink:href="static/images/sprite/symbol/sprite.svg#insta"></use>
                    </svg>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="c-overlay c-overlay--backdrop" :class="overlayIsOpen &amp;&amp; 'is-active'" @click="overlayIsOpen = false"></div>