<div class="box box-solid">
    <div class="box-body">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Товары ({{count($products)}})</h4>
            <div class="btn-group pull-right">
                @if(count($products))
                    <button class="btn btn-primary btn-sm" onclick="checkSelectAll()">Выделить всё</button>
                    <button class="btn btn-warning btn-sm" onclick="checkDeselectAll()">Снять выделение</button>
                    <button class="btn btn-primary btn-sm js-move-btn"
                            data-toggle="modal" data-target="#moveDialog"
                            disabled>Переместить
                    </button>

                @endif
            </div>
        </div>

        <form action="{{ route('admin.catalog.search') }}">
            <div class="input-group">
                <input type="text" class="form-control" name="q" placeholder="Наименование"
                       value="{{ Request::get('q') }}">
                <span class="input-group-btn">
                    <button class="btn btn-info" type="submit">Поиск</button>
                    <a href="{{ route('admin.catalog.search') }}" class="btn btn-danger" type="button">Сброс</a>
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
                    <th>Категория</th>
                    <th>Страна</th>
                    <th>Материал</th>
                    <th>Тип</th>
                    <th>Ручка</th>
                    <th width="120">Цена</th>
                    <th width="120">Старая цена</th>
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
                                <img src="{{ $img->thumb(1) }}" height="100" width="100" alt="image">
                            @elseif($item->catalog->section_image)
                                <img class="img-polaroid" src="{{ $item->catalog->section_image }}" height="100" alt="image">
                            @endif
                        </td>
                        <td><a href="{{ route('admin.catalog.productEdit', [$item->id]) }}"

                               onclick="return catalogContent(this)"
                               style="{{ $item->published != 1 ? 'text-decoration:line-through;' : '' }}">{{ $item->name }}</a>
                        </td>
                        <td>{{ $item->catalog ? $item->catalog->name: '-' }}</td>
                        <td style="text-align: center">{{ $item->manufacturer ?? '-'}}</td>
                        <td style="text-align: center">{{ $item->material ?? '-' }}</td>
                        <td style="text-align: center">{{ $item->type ?? '-'}}</td>
                        <td style="text-align: center">{{ $item->handle ?? '-'}}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->old_price > 0 ?: '-' }}</td>
                        <td>
                            <a class="glyphicon glyphicon-trash"
                               href="{{ route('admin.catalog.productDel', [$item->id]) }}"
                               style="font-size:20px; color:red;" title="Удалить" onclick="return productDel(this)"></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $products->render() !!}
        @else
            <p class="text-yellow">
                @if(Request::get('q'))
                    Ничего не найдено
                @endif
            </p>
        @endif
    </div>
</div>

@if($products)
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
@endif