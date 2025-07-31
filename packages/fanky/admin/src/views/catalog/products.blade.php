@section('page_name')
    <h1>Каталог
        <small>{{ $catalog->name }}</small>
    </h1>
@stop
@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin') }}"><i class="fa fa-dashboard"></i> Главная</a></li>
        <li><a href="{{ route('admin.catalog') }}"><i class="fa fa-list"></i> Каталог</a></li>
        @foreach($catalog->getParents(false, true) as $parent)
            <li><a href="{{ route('admin.catalog.products', [$parent->id]) }}">{{ $parent->name }}</a></li>
        @endforeach
        <li class="active">{{ $catalog->name}}</li>
    </ol>
@stop

<div class="box box-solid">
    <div class="box-body">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Товары ({{count($products)}})</h4>
            <div class="btn-group pull-right">
                <a href="{{ route('admin.catalog.productEdit', ['catalog_id' => $catalog->id]) }}"
                   class="btn btn-info btn-sm" onclick="return catalogContent(this)">Добавить товар</a>
                @if(count($products))
                    <button class="btn btn-primary btn-sm" onclick="checkSelectAll()">Выделить всё</button>
                    <button class="btn btn-warning btn-sm" onclick="checkDeselectAll()">Снять выделение</button>
                    <button class="btn btn-primary btn-sm js-move-btn"
                            data-toggle="modal" data-target="#moveDialog"
                            disabled>Переместить
                    </button>
                    <button class="btn btn-danger btn-sm js-delete-btn"
                            onclick="deleteProducts(this, event)"
                            disabled>Удалить
                    </button>
                    <button class="btn btn-danger btn-sm js-delete-btn"
                            onclick="deleteProductsImage(this, event, {{ $catalog->id }})"
                            disabled>Удалить IMGs
                    </button>
                    <button class="btn btn-success btn-sm js-add-btn"
                            onclick="addProductsImages(this)"
                            disabled>Добавить IMGs
                    </button>
                    <div class="form-group form-inline" style="float: left; margin-left: 15px;">
                        <form>
                            <label> Показывать по:</label>
                            <select name="per_page" class="form-control" onchange="this.form.submit();">
                                @foreach([50,100,150,300,500] as $i)
                                    <option value="{{ $i }}" {{ session('per_page', 50) == $i? 'selected': '' }}>{{ $i }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <div class="mass-images" style="display: none;">
            <div class="form-group">
                <label class="btn btn-default btn-sm">
                    <input id="offer_imag_upload" type="file" multiple
                           accept=".jpeg,.jpg,.png"
                           style="display:none;" onchange="massProductImageUpload(this, event)">
                    Загрузить изображения
                </label>
                <button class="btn btn-success btn-sm send-images" disabled
                        data-catalog-id="{{ $catalog->id }}"
                        data-url="{{ route('admin.catalog.add-products-images') }}"
                        onclick="sendAddedProductImages(this, event)"
                >Применить</button>
            </div>
            <div class="mass-images-list">
            </div>
        </div>

        <form action="{{ route('admin.catalog.search') }}">
            <div class="input-group">
                <input type="text" class="form-control" name="q" placeholder="Наименование"
                       value="{{ Request::get('q') }}">
                <span class="input-group-btn">
                    <button class="btn btn-info" type="submit">Поиск</button>
                    <a href="{{ route('admin.catalog.products', ['id' => $catalog->id]) }}" class="btn btn-danger"
                       type="button">Сброс</a>
                  </span>
            </div><!-- /input-group -->
        </form>

        @if (count($products))
            <table class="table table-striped table-v-middle">
                <thead>
                <tr>
                    <th width="40"></th>
                    <th width="100"></th>
                    <th>Название</th>
                    <th width="130" style="text-align: center">Сортировка</th>
                    <th width="50"></th>
                </tr>
                </thead>
                <tbody id="catalog-products">
                @foreach ($products as $item)
                    <tr data-id="{{ $item->id }}">
                        <td>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" class="js_select" value="{{ $item->id }}"
                                           onclick="checkSelectProduct()">
                                </label>
                            </div>
                        </td>
                        <td>
                            @if ($img = $item->single_image)
                                <img src="{{ $img->thumb(1) }}" height="100" width="100" alt="photo">
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.catalog.productEdit', ['catalog_id' => $catalog->id, 'id' => $item->id]) }}"
                               onclick="return catalogContent(this)" style="{{ $item->published != 1 ? 'text-decoration:line-through;' : '' }}">
                                {{ $item->name }}
                                @if($item->is_popular)
                                    <span style="font-size: 10px; font-style: italic; font-weight: bold; color: purple;"> Популярный</span>
                                @endif
                                @if($item->is_new)
                                    <span style="font-size: 10px; font-style: italic; font-weight: bold; color: green;"> Новинка</span>
                                @endif
                            </a>
                        </td>
                        <td style="text-align: center">
                            <form class="input-group input-group-sm"
                                  action="{{ route('admin.catalog.update-order', [$item->id]) }}"
                                  onsubmit="update_order(this, event)">
                                <input type="number" name="order" class="form-control" step="1" value="{{ $item->order }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-success btn-flat" type="submit">
                                       <span class="glyphicon glyphicon-ok"></span>
                                    </button>
                                </span>
                            </form>
                        </td>
                        <td>
                            <a class="glyphicon glyphicon-trash"
                               href="{{ route('admin.catalog.productDel', ['catalog_id' => $catalog->id, 'id' => $item->id]) }}"
                               style="font-size:20px; color:red;" title="Удалить" onclick="return productDel(this)"></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! Pagination::render('admin::pagination') !!}
        @else
            <p class="text-yellow">В разделе нет товаров!</p>
        @endif
    </div>
</div>
<div id="moveDialog" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Переместить товары</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Выберите категорию</label>
                    {!! Form::select('move_category_id', $catalog_list, 0, ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success pull-left" onclick="moveProducts(this, event)">
                    Переместить
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Отмена</button>
            </div>
        </div>

    </div>
</div>

