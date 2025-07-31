{{--<div class="box box-solid">--}}
{{--    <div class="box-header">--}}
{{--        <span class="box-title">Обновление каталога (ожидается .xls, .xlsx файл)</span>--}}
{{--        @if($last_update)--}}
{{--            <br/>Последнее обновление каталога {{ $last_update->format('d.m.Y H:i') }}--}}
{{--        @endif--}}

{{--    </div>--}}
{{--    <div class="box-body">--}}
{{--        <form action="{{ route('admin.catalog.import-price') }}" method="post" enctype="multipart/form-data">--}}
{{--            <div class="form-group">--}}
{{--                <div class="input-group">--}}
{{--                    {!! Form::file('price', ['class'=>'form-control', 'accept' => '.xls, .xlsx']) !!}--}}
{{--                    <span class="input-group-btn">--}}
{{--                    {!! Form::submit('Отправить', ['class'=> 'btn']) !!}--}}
{{--                </span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="box box-solid">--}}
{{--    <div class="box-body">--}}
{{--        <a href="{{ route('admin.catalog.export') }}" target="_blank">Выгрузить каталог</a>--}}
{{--    </div>--}}
{{--</div>--}}