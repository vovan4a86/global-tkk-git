<tr id="param{{ $param->id }}" data-id="{{ $param->id }}">
    <td width="40"><i class="fa fa-ellipsis-v"></i> <i class="fa fa-ellipsis-v"></i></td>
    <td width="420">{{ $param->name }}</td>
    <td>{{ $param->value }}</td>
    <td>
        <a href="{{ route('admin.catalog.edit_param', [$param->id]) }}"
           class="btn btn-default edit-param" onclick="editParam(this, event)">
            <i class="fa fa-pencil text-yellow"></i></a>
        <a href="{{ route('admin.catalog.del_param', [$param->id]) }}"
           class="btn btn-default del-param" onclick="delParam(this, event)">
            <i class="fa fa-trash text-red"></i></a>
    </td>
</tr>
