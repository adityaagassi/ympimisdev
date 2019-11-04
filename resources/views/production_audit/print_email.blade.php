<title>YMPI 情報システム</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
	#approval1 {
	    display: none;
	  }
	  #approval2 {
	    display: none;
	  }
	  #approval3 {
	    display: none;
	  }
}
</style>
<table class="table table-bordered table-hover">
	<tbody>
		<tr>
			<td colspan="6">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
		</tr>
		<tr>
			<td rowspan="5" style="width: 20px"><center><img width="80px" src="{{ asset('images/logo_yamaha.jpg') }}" alt=""></center></td>
		</tr>
		<tr>
			<td>Department</td>
			<td>{{ $departments }}</td>
			<td rowspan="4" colspan="2" style="padding: 15px;"><center><b>{{ $activity_name }}</b></center></td>
			<td rowspan="4"><center>Mengetahui<br><br>
				@if($jml_null == 0)
					<b style='color:green'>Approved</b><br>
					<b style='color:green'>{{ $approved_date }}</b>
				@endif
				<br>{{ $foreman }}
				<br>Foreman</center>
			</td>
			@if($jml_null > 0)
			<td rowspan="5" id="approval1"><center>Approval</center></td>
			@endif
		</tr>
		<tr>
			<td>Product</td>
			<td>{{ $product }}</td>
		</tr>
		<tr>
			<td>Proses</td>
			<td>{{ $proses }}</td>
		</tr>
		<tr>
			<td>Date</td>
			<td>{{ $date_audit }}</td>
		</tr>
		<tr>
			<td>Point Check</td>
			<td>Cara Cek</td>
			<td>Foto Kondisi Aktual</td>
			<td>Kondisi (OK / NG)</td>
			<td>PIC</td>
			<td>Auditor</td>
		</tr>
		<form role="form" method="post" action="{{url('index/production_audit/approval/'.$id)}}">
		@foreach($production_audit as $production_audit)
		<tr>
			<td><?php echo $production_audit->point_check ?></td>
			<td><?php echo $production_audit->cara_cek ?></td>
			<td><img width="200px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}"></td>
			<td>@if($production_audit->kondisi == "Good")
	              <label class="label label-success">{{$production_audit->kondisi}}</label>
	            @else
	              <label class="label label-danger">{{$production_audit->kondisi}}</label>
	            @endif
        	</td>
			<td>{{ $production_audit->pic_name }}</td>
			<td>{{ $production_audit->auditor_name }}</td>
			@if($jml_null > 0)
			<td id="approval2">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				@if($production_audit->approval == Null)
				<div class="custom-control custom-checkbox">
				    <input type="checkbox" class="custom-control-input" id="customCheck" name="approve[]" value="{{ $production_audit->id_production_audit }}">
				    <label class="custom-control-label" for="customCheck">Approve</label>
				</div>
				@endif
			</td>
			@endif
		</tr>
		@endforeach
		@if($jml_null > 0)
		<tr id="approval3">
			<td align="right" colspan="7"><button type="submit">Submit</button></td>
		</tr>
		@endif
		</form>
	</tbody>
</table>
<script>
    // setTimeout(function () { window.print(); }, 200);
</script>
