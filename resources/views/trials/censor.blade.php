@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
th {text-align:center}
td {text-align:center}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Trial Censors & Tags <span class="text-purple">???</span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header">
					{{-- <h3 class="box-title">Reading PLC<span class="text-purple"> ???</span></span></h3> --}}
				</div>
				<div class="box-body">
					<table id="inventoryTable" class="table table-bordered table-striped table-hover">
						<thead style="font-size: 22px;">
							<tr>
								<th style="width: 10%;">Work Station</th>
								<th style="width: 10%;">PIC</th>
								<th style="width: 9%;">InWork</th>
								<th style="width: 9%;">1</th>
								<th style="width: 9%;">2</th>
								<th style="width: 9%;">3</th>
								<th style="width: 9%;">4</th>
								<th style="width: 9%;">5</th>
								<th style="width: 9%;">6</th>
								<th style="width: 9%;">7</th>
								<th style="width: 9%;">8</th>
							</tr>
						</thead>
						<tbody style="font-size: 20px">
							<tr>
								<td id="0">WS-01</td>
								<td id="0">Ghozali</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
							</tr>
							<tr>
								<td id="1">WS-02</td>
								<td>Anan</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
							</tr>
							<tr>
								<td id="2">WS-03</td>
								<td>Vio</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
							</tr>
							<tr>
								<td id="3">WS-04</td>
								<td>Matari</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
							</tr>
							<tr>
								<td id="4">WS-05</td>
								<td>Mambo</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script>
	jQuery(document).ready(function() {
		// readCensor();
		setInterval(function(){
			readCensor();
		}, 600);
	});
	function readCensor(){
		$.get('{{ url("trial/censor") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$.each(result.dataCensor, function( index, value ) {
						if(value == 1){
							$('#'+index).css('background-color', 'RGB(255,0,0)');
						}
						else{
							$('#'+index).css('background-color', '');
						}
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}
</script>
@endsection