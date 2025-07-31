@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin') }}"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="{{ route('admin.catalog') }}"><i class="fa fa-list"></i> Каталог</a></li>
        @foreach($catalog->getParents(false, true) as $parent)
            <li><a href="{{ route('admin.catalog.products', [$parent->id]) }}">{{ $parent->name }}</a></li>
        @endforeach
        <li class="active">{{ $catalog->id ? $catalog->name : 'Новый раздел' }}</li>

    </ol>
@stop
@section('page_name')
    <h1>Каталог
        <small>{{ $catalog->id ? $catalog->name : 'Новый раздел' }}</small>
    </h1>
@stop

<form action="{{ route('admin.catalog.catalogSave') }}" onsubmit="return catalogSave(this, event)">
    <input type="hidden" name="id" value="{{ $catalog->id }}">

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab">Параметры</a></li>
            <li><a href="#tab_2" data-toggle="tab">Тексты</a></li>
            @if($catalog->id)
                <li class="pull-right">
                    <a href="{{ $catalog->url }}" target="_blank">Посмотреть</a>
                </li>
            @endif
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">

                {!! Form::groupText('name', $catalog->name, 'Название') !!}
                {!! Form::groupText('h1', $catalog->h1, 'H1') !!}
                {!! Form::groupSelect('parent_id', ['0' => '---корень---'] + $catalogs->pluck('name', 'id')->all(),
                    $catalog->parent_id, 'Родительский раздел') !!}
                {!! Form::groupText('alias', $catalog->alias, 'Alias') !!}
                {!! Form::groupText('title', $catalog->title, 'Title') !!}
                {!! Form::groupText('keywords', $catalog->keywords, 'keywords') !!}
                {!! Form::groupText('description', $catalog->description, 'description') !!}
                {!! Form::groupText('og_title', $catalog->og_title, 'OgTitle') !!}
                {!! Form::groupText('og_description', $catalog->og_description, 'OgDescription') !!}


                <div class="form-group" style="display: flex; column-gap: 30px;">
                    <div>
                        <label for="article-image">Изображение (845x576)</label>
                        <input id="article-image" type="file" name="image" accept=".png,.jpeg,.jpg"
                               onchange="return catalogImageAttache(this, event)">
                        <div id="article-image-block">
                            @if ($catalog->image)
                                <img class="img-polaroid"
                                     src="{{ $catalog->thumb(1) }}"
                                     data-image="{{$catalog->image_src }}"
                                     onclick="return popupImage($(this).data('image'))" alt="image">
                            @else
                                <p class="text-yellow">Изображение не загружено.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <span class="box-title">Шаблон автооптимизации для товаров</span>
                    </div>
                    <div class="box-body">
                        {!! Form::groupText('product_title_template', $catalog->product_title_template, 'Шаблон title') !!}
                        <div class="small">Шаблон title по умолчанию</div>
                        <div class="small">{{ \Fanky\Admin\Models\Product::$defaultTitleTemplate }}</div>
                        {!! Form::groupText('product_description_template', $catalog->product_description_template, 'Шаблон description') !!}
                        <div class="small">Шаблон description по умолчанию</div>
                        <div class="small">{{ \Fanky\Admin\Models\Product::$defaultDescriptionTemplate }}</div>

                        {!! Form::groupRichtext('product_text_template', $catalog->product_text_template, 'Шаблон текста') !!}
                    </div>
                    <div class="box-footer">
                        Коды замены:
                        <ul>
                            <li>{name} - название товара</li>
                            <li>{lower_name} - название товара в нижнем регистре</li>
                        </ul>
                        Перед кодом {city} пробел не нужен. Шаблон применяется ко всем подразделам, если у них нет
                        своего шаблона
                    </div>
                </div>

                {!! Form::hidden('published', 0) !!}
                {!! Form::groupCheckbox('published', 1, $catalog->published, 'Показывать раздел') !!}
                {!! Form::hidden('on_main', 0) !!}
                {!! Form::groupCheckbox('on_main', 1, $catalog->on_main, 'На главной') !!}
            </div>

            <div class="tab-pane" id="tab_2">
                <div class="form-group" style="display: flex; column-gap: 30px;">
                    <div>
                        <label for="preview-image">Главное изображение (1270x440)</label>
                        <input id="preview-image" type="file" name="image" accept=".png,.jpeg,.jpg"
                               onchange="return previewImageAttache(this, event)">
                        <div id="preview-image-block">
                            @if ($catalog->image_text_preview)
                                <img class="img-polaroid"
                                     src="{{ $catalog->preview_thumb(1) }}"
                                     data-image="{{ $catalog->image_preview_src }}"
                                     onclick="return popupImage($(this).data('image'))" alt="image">
                                <a class="images_del" href="{{ route('admin.catalog.delete-preview', [$catalog->id]) }}"
                                   onclick="return previewImageDel(this, event)">
                                    <span class="glyphicon glyphicon-trash text-red"></span></a>
                            @else
                                <p class="text-yellow">Изображение не загружено.</p>
                            @endif
                        </div>
                    </div>
                </div>
                {!! Form::groupTextarea('announce', $catalog->announce, 'Заголовок') !!}
                {!! Form::groupRichtext('text', $catalog->text, 'Основной текст') !!}
            </div>
        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</form>
