@extends(Config::get('vedette.vedette_views.layout'))

@section('title')
@parent
	{{ Config::get('vedette.vedette_html.separator') }}
	{{ trans('lingos::account.command.create') }}
@stop

@section('styles')
	<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-datepicker/css/datepicker3.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendors/chosen_v1.0.0/chosen.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/vendors/chosen_v1.0.0/chosen_bootstrap.css') }}">
@stop

@section('scripts')
	<script src="{{ asset('packages/illuminate3/vedette/assets/js/restfulizer.js') }}"></script>

	<script src="{{ asset('assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
	<script src="{{ asset('assets/vendors/bootstrap-datepicker/js/datepicker-settings.js') }}"></script>
	<script src="{{ asset('assets/vendors/chosen_v1.0.0/chosen.jquery.min.js') }}"></script>
@stop

@section('inline-scripts')

	var text_confirm_message = '{{ trans('lingos::job_title.ask.delete') }}';

$(document).ready(function(){

	var config = {
		'.chosen-select'           : {},
		'.chosen-select-deselect'  : {allow_single_deselect:true},
		'.chosen-select-no-single' : {disable_search_threshold:10},
		'.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
		'.chosen-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}

});
@stop

@section('content')
<div class="row">

<h1>
	<p class="pull-right">
	{{ Bootstrap::linkIcon(
		'admin.index',
		trans('lingos::button.back'),
		'chevron-left fa-fw',
		array('class' => 'btn btn-default')
	) }}
	</p>
	<i class="fa fa-user fa-lg"></i>
	{{ trans('lingos::account.command.create') }}
	<hr>
</h1>
</div>


<div class="row">
{{ Form::open(array(
	'route' => 'users.store',
	'role' => 'form'
)) }}


	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#info" data-toggle="tab">
				<i class="fa fa-user fa-fw"></i>
				{{ trans('lingos::general.information') }}
			</a>
		</li>
		<li>
			<a href="#password" data-toggle="tab">
				<i class="fa fa-key fa-fw"></i>
				{{ trans('lingos::auth.password') }}
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

			{{ Bootstrap::email(
				'email',
				trans('lingos::account.email'),
				null,
				$errors,
				'envelope fa-fw',
				[
					'id' => 'email',
					'placeholder' => trans('lingos::account.email'),
					'tabindex' => '1',
					'required',
					'autofocus'
				]
			) }}

		</fieldset>

	</div><!-- tab-info -->
	<div class="tab-pane" id="password">

		<fieldset>
			<h2>
				<legend>
					<i class="fa fa-key fa-fw"></i>
					{{ trans('lingos::auth.password') }}
				</legend>
			</h2>

			{{ Bootstrap::password(
				'password',
				trans('lingos::auth.password'),
				$errors,
				'unlock fa-fw',
				[
					'id' => 'password',
					'placeholder' => trans('lingos::auth.password'),
					'tabindex' => '2',
					'required'
				]
			) }}

			{{ Bootstrap::password(
				'password_confirmation',
				trans('lingos::auth.confirm_password'),
				$errors,
				'unlock-alt fa-fw',
				[
					'id' => 'password',
					'placeholder' => trans('lingos::auth.confirm_password'),
					'tabindex' => '3',
					'required',
					'autocomplete' => 'off'
				]
			) }}

		</fieldset>


	</div><!-- tab-password -->
	<div class="tab-pane" id="status">

		<fieldset>
			<h2>
				<legend>
					<i class="fa fa-gavel fa-fw"></i>
					{{ trans('lingos::role.roles') }}
				</legend>
			</h2>

			<div class="panel panel-default">
				<div class="panel-body">
				@foreach (Vedette\models\Role::All() as $role)
					{{ Bootstrap::checkbox('roles[]', $role->present()->name(), $role->id, null) }}
				@endforeach
				</div>
			</div>


		</fieldset>

	</div><!-- tab-status -->
	</div><!-- tab-content -->

	<hr>

	{{ Bootstrap::submit(
		trans('lingos::button.save'),
		[
			'class' => 'btn btn-success btn-block'
		]
	) }}

	<div class="row">
		<div class="col-sm-6">
		{{ Bootstrap::linkIcon(
			'users.index',
			trans('lingos::button.cancel'),
			'times fa-fw',
			[
				'class' => 'btn btn-default btn-block'
			]
		) }}
		</div>
		<div class="col-sm-6">
		{{ Bootstrap::reset(
			trans('lingos::button.reset'),
			[
				'class' => 'btn btn-default btn-block'
			]
		) }}
		</div>
	</div>


{{ Form::close() }}
</div>
@stop
