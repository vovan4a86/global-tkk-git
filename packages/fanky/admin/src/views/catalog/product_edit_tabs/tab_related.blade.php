<label for="">Добавить товар</label>
<div class="input-group input-group">
    <input type="text" class="form-control" name="pub_name" id="pub_name"
           value="" placeholder="Начните вводить название товара">
    <input type="hidden" name="pub_name_id" id="pub_name_id" value=""/>
    <span class="input-group-btn">
        <button class="btn btn-success btn-flat" type="button"
                onclick="attachRelated(this, event)">Добавить</button>
    </span>
</div>
<ul class="list-group" id="list-group" style="margin-top: 20px;">
    @foreach($related as $relative_item)
        @include('admin::catalog.product_edit_tabs.related_row')
    @endforeach
</ul>

<script>
    $(document).ready(function () {
        $('#pub_name').devbridgeAutocomplete({
            serviceUrl: '{{ route('admin.catalog.related-find') }}',

            onSelect: function (suggestion) {
                $('#pub_name_id').val(suggestion.data);
            },
            ajaxSettings: {
                dataType: 'json',
            },
            paramName: 'query',
            minChars: 2,
            noCache: true,
            transformResult: function (response) {
                return {
                    suggestions: $.map(response.data, function (dataItem) {
                        return {
                            value: dataItem.name, data: dataItem.id
                        };
                    })
                };
            }
        });
    });
</script>