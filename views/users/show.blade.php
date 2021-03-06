@extends(Config::get('vedette.vedette_views.layout'))

@section('title')
@parent
	{{ Config::get('vedette.vedette_html.separator') }}
	{{ $user->email }}
@stop

@section('styles')
@stop

@section('scripts')
	<script src="{{ asset('packages/illuminate3/vedette/assets/js/restfulizer.js') }}"></script>
@stop

@section('inline-scripts')
	var text_confirm_message = '{{ trans('lingos::account.ask.delete') }}';
@stop

@section('content')
<div class="row">
<h1>
	<p class="pull-right">
	{{ Bootstrap::linkIcon(
		'users.index',
		trans('lingos::button.back'),
		'chevron-left fa-fw',
		array('class' => 'btn btn-default')
	) }}
	</p>
	<i class="fa fa-user fa-lg"></i>
	{{ $user->email }}
	<hr>
</h1>
</div>


<div class="row">


	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#info" data-toggle="tab">
				<i class="fa fa-user fa-fw"></i>
				{{ trans('lingos::general.information') }}
			</a>
		</li>
		<li>
			<a href="#roles" data-toggle="tab">
				<i class="fa fa-gavel fa-fw"></i>
				{{ trans('lingos::role.roles') }}
			</a>
		</li>
		<li>
			<a href="#user_control" data-toggle="tab">
				<i class="fa fa-sort-amount-asc fa-fw"></i>
				{{ trans('lingos::auth.user_control') }}
			</a>
		</li>
		<li>
			<a href="#status" data-toggle="tab">
				<i class="fa fa-heart fa-fw"></i>
				{{ trans('lingos::general.status') }}
			</a>
		</li>
	</ul>

	<div id="myTabContent" class="tab-content">
	<div class="tab-pane active" id="info">

		<fieldset>
			<h2>
				<legend>
					<i class="fa fa-user fa-fw"></i>
					{{ trans('lingos::general.information') }}
				</legend>
			</h2>

			<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<td>{{ trans('lingos::account.email') }}</td>
						<td>{{ $user->email }}</td>
					</tr>
				</tbody>
			</table>
			</div><!-- ./responsive -->

		</fieldset>

	</div><!-- tab-info -->
	<div class="tab-pane" id="roles">

		<fieldset>
			<h2>
				<legend>
					<i class="fa fa-gavel fa-fw"></i>
					{{ trans('lingos::role.roles') }}
				</legend>
			</h2>

			<div class="panel panel-default">
				<div class="panel-body">
					{{ $user->present()->roles() }}
				</div>
			</div>

		</fieldset>

	</div><!-- tab-roles -->
	<div class="tab-pane" id="user_control">

		<fieldset>
			<h2>
				<legend>
					<i class="fa fa-heart fa-fw"></i>
					{{ trans('lingos::general.activity') }}
				</legend>
			</h2>

			<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<td>{{ trans('lingos::account.last_login') }}</td>
						<td>{{ $user->last_login }}</td>
					</tr>
					<tr>
						<td>{{ trans('lingos::account.created_at') }}</td>
						<td>{{ $user->created_at }}</td>
					</tr>
					<tr>
						<td>{{ trans('lingos::account.updated_at') }}</td>
						<td>{{ $user->updated_at }}</td>
					</tr>
				</tbody>
			</table>
			</div><!-- ./responsive -->

		</fieldset>

	</div><!-- tab-information -->
	<div class="tab-pane" id="status">

		<fieldset>
			<h2>
				<legend>
					<i class="fa fa-heart fa-fw"></i>
					{{ trans('lingos::general.activity') }}
				</legend>
			</h2>

			<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<td>{{ trans('lingos::account.last_login') }}</td>
						<td>{{ $user->last_login }}</td>
					</tr>
					<tr>
						<td>{{ trans('lingos::account.created_at') }}</td>
						<td>{{ $user->created_at }}</td>
					</tr>
					<tr>
						<td>{{ trans('lingos::account.updated_at') }}</td>
						<td>{{ $user->updated_at }}</td>
					</tr>
				</tbody>
			</table>
			</div><!-- ./responsive -->

		</fieldset>

	</div><!-- tab-status -->
	</div><!-- tab-content -->

	<hr>

	<div class="row">
		<div class="col-sm-6">
		{{ Bootstrap::linkIcon(
			'users.index',
			trans('lingos::button.back'),
			'chevron-left fa-fw',
			[
				'class' => 'btn btn-default btn-block'
			]
		) }}
		</div>
		<div class="col-sm-6">
		{{ Bootstrap::linkIcon(
			'users.destroy',
			trans('lingos::button.delete'),
			'trash-o fa-fw',
			array(
				'class' => 'btn btn-default btn-block action_confirm',
				'data-method' => 'delete',
				'title' => trans('lingos::account.command.delete')
			)
		) }}
		</div>
	</div>


</div>
@stop
