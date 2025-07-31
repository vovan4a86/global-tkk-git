@extends('admin::template')

@section('scripts')
	<script type="text/javascript" src="/adminlte/interface_news.js"></script>
@stop

@section('page_name')
	<h1>Новости
		<small><a href="{{ route('admin.news.edit') }}">Добавить новость</a></small>
	</h1>
@stop

@section('breadcrumb')
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Главная</a></li>
		<li class="active">Новости</li>
	</ol>
@stop

@section('content')
	<div class="box box-solid">
		<div class="box-body">
			@if (count($news))
				<table class="table table-striped">
					<thead>
						<tr>
							<th width="150">Дата</th>
							<th width="50"></th>
							<th width="100">Изображение</th>
							<th>Название</th>
							<th>Анонс</th>
							<th width="50"></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($news as $item)
							<tr>
								<td>{{ $item->dateFormat() }}</td>
								<td style="color: {{ $item->type == 1 ? 'green' : 'red' }};">{{ $item->type == 1 ? 'Новость' : 'Акция' }}</td>
								<td style="text-align: center;">
									@if($item->image)
										<img src="{{ $item->thumb(1) }}" alt="{{ $item->name }}">
									@endif
								</td>
								<td><a href="{{ route('admin.news.edit', [$item->id]) }}"
									   style="text-decoration: {{ !$item->published ? 'line-through' : 'none' }}">{{ $item->name }}</a></td>
								<td><a href="{{ route('admin.news.edit', [$item->id]) }}"
									   style="text-decoration: {{ !$item->published ? 'line-through' : 'none' }}">{{ str_limit($item->announce) }}</a></td>
								<td>
									<a class="glyphicon glyphicon-trash" href="{{ route('admin.news.delete', [$item->id]) }}"
									   style="font-size:20px; color:red;" title="Удалить" onclick="return newsDel(this)"></a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{!! Pagination::render('admin::pagination') !!}
			@else
				<p>Нет новостей!</p>
			@endif
		</div>
	</div>
	<style>
		.table tbody tr td {
			vertical-align: middle;
		}
	</style>
@stop
