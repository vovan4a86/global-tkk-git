<li class="list-group-item add-catalog-item">
    <input type="hidden" name="additional_catalog[]" value="{{ $relative_item->id }}"/>
    <div class="pull-right ">
        <div class="btn-group-sm">
            <a href="#"
               class="btn text-red" onclick="return detachRelated(this)">
                <i class="fa fa-trash fa-2x"></i></a>
        </div>
    </div>
    <span>{{ $relative_item->name }}</span>
</li>