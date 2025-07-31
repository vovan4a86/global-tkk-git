@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin') }}"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="{{ route('admin.catalog') }}"><i class="fa fa-list"></i> Каталог</a></li>
        @foreach($product->getParents(false, true) as $parent)
            <li><a href="{{ route('admin.catalog.products', [$parent->id]) }}">{{ $parent->name }}</a></li>
        @endforeach
        <li class="active">{{ $product->id ? $product->name : 'Новый товар' }}</li>
    </ol>
@stop
@section('page_name')
    <h1>Каталог
        <small style="max-width: 350px;">{{ $product->id ? $product->name : 'Новый товар' }}</small>
    </h1>
@stop

<form action="{{ route('admin.catalog.productSave') }}" onsubmit="return productSave(this, event)">
    {!! Form::hidden('id', $product->id) !!}

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab">Параметры</a></li>
            <li><a href="#tab_2" data-toggle="tab">Текст</a></li>
            <li><a href="#tab_chars" data-toggle="tab">Характеристики</a></li>
            <li><a href="#tab_4" data-toggle="tab">Изображения</a></li>
            <li class="pull-right">
                <a href="{{ route('admin.catalog.products', [$product->catalog_id]) }}"
                   onclick="return catalogContent(this)">К списку товаров</a>
            </li>
            @if($product->id)
                <li class="pull-right">
                    <a href="{{ $product->url }}" target="_blank">Посмотреть</a>
                </li>
            @endif
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                {!! Form::groupText('name', $product->name, 'Название') !!}
                {!! Form::groupText('h1', $product->h1, 'H1') !!}
                {!! Form::groupSelect('catalog_id', $catalogs, $product->catalog_id, 'Каталог') !!}
                {!! Form::groupText('alias', $product->alias, 'Alias') !!}
                {!! Form::groupText('title', $product->title, 'Title') !!}
                {!! Form::groupText('keywords', $product->keywords, 'keywords') !!}
                {!! Form::groupText('description', $product->description, 'description') !!}
                {!! Form::groupText('og_title', $product->og_title, 'OgTitle') !!}
                {!! Form::groupText('og_description', $product->og_description, 'OgDescription') !!}

                <hr>
                {!! Form::hidden('in_stock', 0) !!}
                {!! Form::groupCheckbox('published', 1, $product->published, 'Показывать товар') !!}
            </div>

            <div class="tab-pane" id="tab_2">
                {!! Form::groupRichtext('announce', $product->announce, 'Анонс для раздела') !!}
                {!! Form::groupRichtext('text', $product->text, 'Текст на странице товара') !!}
            </div>

            <div class="tab-pane" id="tab_chars">
                @if(!$product->id)
                    <div>Добавление дополнительных параметров доступно только после сохранения товара</div>
                @else
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <input type="text" class="param-name form-control" placeholder="Название">
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="param-value form-control" placeholder="Значение">
                        </div>
                        <div class="col-lg-2">
                            <a href="{{ route('admin.catalog.add_param', $product->id) }}" onclick="addParam(this, event)"
                               class="btn btn-primary add-param">Добавить
                                параметр</a>
                        </div>
                    </div>
                    <table class="table table-hover table-condensed" id="param_list">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Название</th>
                            <th>Значение</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="product-params">
                        @foreach ($product->params as $param)
                            @include('admin::catalog.param_row', ['param' => $param])
                        @endforeach
                        </tbody>
                    </table>

                @endif
            </div>

            <div class="tab-pane" id="tab_4">
                <input id="product-image" type="hidden" name="image" value="{{ $product->image }}">
                @if ($product->id)
                    <div class="form-group">
                        <label class="btn btn-success">
                            <input id="offer_imag_upload" type="file" multiple
                                   data-url="{{ route('admin.catalog.productImageUpload', $product->id) }}"
                                   style="display:none;" onchange="productImageUpload(this, event)">
                            Загрузить изображения
                        </label>
                    </div>

                    <div class="images_list">
                        @foreach ($product->images as $image)
                            @include('admin::catalog.product_image', ['image' => $image, 'active' => $product->image])
                        @endforeach
                    </div>
                @else
                    <p class="text-yellow">Изображения можно будет загрузить после сохранения товара!</p>
                @endif
            </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(".images_list").sortable({
        update: function (event, ui) {
            var url = "{{ route('admin.catalog.productImageOrder') }}";
            var data = {};
            data.sorted = $('.images_list').sortable("toArray", {attribute: 'data-id'});
            sendAjax(url, data);
        },
    }).disableSelection();

    $("#param_list tbody").sortable({
        update: function (event, ui) {
            var url = "{{ route('admin.catalog.productParamOrder') }}";
            var data = {};
            data.sorted = $('#param_list tbody').sortable("toArray", {attribute: 'data-id'});
            sendAjax(url, data);
        },
    }).disableSelection();


</script>
