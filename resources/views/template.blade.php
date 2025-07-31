<!DOCTYPE html>
<html lang="ru-RU">

@include('blocks.head')

<body x-data="{ overlayIsOpen: false }">

@if(isset($h1))
    <h1 class="v-hidden">{{ $h1 }}</h1>
@endif
{!! Settings::get('counters') !!}

@include('blocks.overlay')

@yield('content')

@include('blocks.popups')

<div class="v-hidden" itemscope itemtype="https://schema.org/LocalBusiness" aria-hidden="true" tabindex="-1">
    {!! Settings::get('schema.org') !!}
</div>
@if(isset($admin_edit_link) && strlen($admin_edit_link))
    <div class="adminedit">
        <div class="adminedit__ico"></div>
        <a href="{{ $admin_edit_link }}" class="adminedit__name" target="_blank">Редактировать</a>
    </div>
@endif
</body>
</html>
