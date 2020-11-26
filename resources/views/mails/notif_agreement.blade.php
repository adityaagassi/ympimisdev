<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<h2>
				An Agreement Will Expire Soon
			</h2>
			This is an automatic notification. Please do not reply to this address.
			<br>
			自動通知です。返事しないでください。
			<h3 style="font-size: 18px;">
				{{ $data->vendor }}
			</h3>
			<p style="font-size: 18px;">
				{{ $data->description }}
			</p>
			<h3>
				Validity Period:
			</h3>
			<p style="font-size: 18px; background-color: #ff5c8d; font-weight: bold;">
				{{ date('d F Y', strtotime($data->valid_from)) }} to {{ date('d F Y', strtotime($data->valid_to)) }}<br>
				{{ $data->validity }} Day(s) Left Before Expired
			</p>
			<h3>
				PIC:
			</h3>
			<p style="font-size: 18px;">
				{{ $data->department }}<br>
				{{ $data->created_by }} - {{ $data->name }}
			</p>
			<br>
			<br>
			<p style="font-size: 18px;">
				Please check and decide whether the agreement will be renewed or terminated.
			</p>
			<a href="{{url('index/general/agreement')}}">Click This To Update Agreements</a><br>
		</center>
	</div>
</body>
</html>