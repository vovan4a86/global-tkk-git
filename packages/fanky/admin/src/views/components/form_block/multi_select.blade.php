<div class="form-group">
    {{ Form::label($label, null, ['class' => 'control-label']) }}
    {{--    {!! Form::select($name, $list, $values, array_merge(['class' => 'form-control', 'multiple' => "multiple"], $attributes)) !!}--}}
    <select id="{{ $name }}" class="form-control" multiple="multiple">
        @foreach($list as $i => $name)
            @dump($i)
            <option value="{{ $i }}" selected="{{ in_array($i, $values) ? 'selected' : null }}" >{!! $name !!}</option>
        @endforeach
    </select>
</div>