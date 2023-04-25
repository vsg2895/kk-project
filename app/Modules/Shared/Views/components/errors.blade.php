@if($errors->count())
	<div class="alert alert-danger">
		<a class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<strong>Fel i formuläret</strong>
		<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif