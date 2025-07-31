@if($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator
    && $paginator->hasPages()
    && $paginator->lastPage() > 1)
        <?
        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */ ?>

        <?php
        // config
        $link_limit = 7; // maximum number of links (a little bit inaccurate, but will be ok for now)
        $half_total_links = floor($link_limit / 2);
        $from = $paginator->currentPage() - $half_total_links;
        $to = $paginator->currentPage() + $half_total_links;
        if ($paginator->currentPage() < $half_total_links) {
            $to += $half_total_links - $paginator->currentPage();
        }
        if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
            $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
        }
        ?>

    @if ($paginator->lastPage() > 1)
        <nav class="pagination">
            <ul class="pagination__list">
                @if ($paginator->currentPage() > 1)
                    <li class="pagination__item">
                        <a class="pagination__link pagination__link--arrow"
                           href="{{ $paginator->previousPageUrl() }}" title="Назад">
                            <svg class="svg-sprite-icon icon-arrow-left" width="1em" height="1em">
                                <use xlink:href="/static/images/sprite/symbol/sprite.svg#arrow-left"></use>
                            </svg>
                        </a>
                    </li>
                @endif

                @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                    @if ($from < $i && $i < $to)
                            <li class="pagination__item {{ $i == $paginator->currentPage() ? 'is-disabled' : '' }}">
                                <a class="pagination__link" href="{{ $paginator->url($i) }}" data-link="data-link"
                                   title="Страница {{ $i }}">{{ $i }}</a>
                            </li>
                    @endif
                @endfor

                @if($to < $paginator->lastPage())
                    <li class="pagination__item">
                        <a class="pagination__link" href="{{ $paginator->url($paginator->lastPage()) }}">
                            ...
                        </a>
                    </li>
                @endif

                @if ($paginator->currentPage() < $paginator->lastPage())
                    <li class="pagination__item">
                        <a class="pagination__link pagination__link--arrow" href="{{ $paginator->nextPageUrl() }}"
                           title="Вперёд">
                            <svg class="svg-sprite-icon icon-arrow-right" width="1em" height="1em">
                                <use xlink:href="/static/images/sprite/symbol/sprite.svg#arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                @endif
            </ul >
        </nav>
    @endif
@endif



