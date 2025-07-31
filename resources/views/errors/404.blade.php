@extends('template')
@section('content')
    @include('blocks.bread')
    <main>
        <section class="error">
            <div class="error__bg lazy" data-bg="/static/images/common/error.webp"></div>
            <div class="error__title">Такой страницы не существует</div>
            <a class="error__link" href="{{ route('main') }}">Перейти на главную</a>
        </section>
    </main>
@stop
