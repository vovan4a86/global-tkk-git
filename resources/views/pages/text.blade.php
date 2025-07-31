@extends('template')
@section('content')
    <div class="layout body-content">
        @include('blocks.bread')
        <div class="layout__container container">
            <div class="layout__heading">
                <div class="title">{{ $h1 ?? '' }}</div>
            </div>
        </div>
        <main>
            <div class="container">
                <div class="layout layout--brands">
                    <div class="layout__grid">
                        <div class="layout__content text-block">
                            {!! $text !!}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@stop
