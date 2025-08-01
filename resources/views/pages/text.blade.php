@extends('template')
@section('content')
    @include('blocks.bread')
    <main>
        <section class="text page">
            <div class="text__container container text-block">
                <h1 class="h1">{{ $h1 ?? '' }}</h1>
                {!! $text !!}
            </div>
        </section>
    </main>
@stop
