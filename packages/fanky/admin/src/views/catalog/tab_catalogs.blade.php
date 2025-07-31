<div class="form-group">
    <label for="product-catalog">Дополнительные разделы</label>
    <div class="input-group">{!! Form::select('', [''=>'Выбрать']+$catalogs, null, ['class' => 'form-control']) !!}
        <span class="input-group-btn">
                      <button type="button"
                              class="btn btn-info btn-flat"
                              onclick="additionalCatalogAdd(this, event)">Добавить</button>
                    </span></div>
</div>
<ul class="list-group city_list">
    @foreach($additional_catalogs as $item)
        <li class="list-group-item add-catalog-item">
            <input type="hidden" name="additional_catalog[]" value="{{ $item->id }}"/>
            <div class="pull-right ">
                <div class="btn-group-sm">
                    <a href="#"
                       class="btn text-red" onclick="additionalCatalogDelete(this, event)">
                        <i class="fa fa-trash fa-2x"></i></a>
                </div>
            </div>
            <span>{{ $item->name }}</span>
        </li>
    @endforeach
    <li class="list-group-item" style="display: none;">
        <input type="hidden" name="additional_catalog[]" value=""/>
        <div class="pull-right ">
            <div class="btn-group-sm">
                <a href="#"
                   class="btn text-red" onclick="additionalCatalogDelete(this, event)">
                    <i class="fa fa-trash fa-2x"></i></a>
            </div>
        </div>
        <span></span>
    </li>
</ul>