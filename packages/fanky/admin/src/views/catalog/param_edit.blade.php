<div style="width: 400px">
    <form data-id="{{ $param->id }}" action="{{ route('admin.catalog.save_param', $param->id) }}" onsubmit="saveParam(this, event)">
        <div class="form-group">
            <label>Название</label>
            <input type="text" name="name" value="{{ $param->name }}" class="form-control" />
        </div>
        <div class="form-group">
            <label>Значение</label>
            <input type="text" name="value" value="{{ $param->value }}" class="form-control" />
        </div>

        <input type="submit" value="Сохранить" class="btn btn-primary" />
    </form>
</div>
