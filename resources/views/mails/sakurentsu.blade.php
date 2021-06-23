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
			@if($data[0]->position == "interpreter" || $data[0]->position == "PC1" || $data[0]->position == "PC2" || $data[0]->position == "interpreter2")
			@foreach($data as $datas)
			<?php $id = $datas->id ?>
			<?php $sakurentsu_number = $datas->sakurentsu_number ?>
			<?php $applicant = $datas->applicant ?>
			<?php $title_jp = $datas->title_jp ?>
			<?php $target_date = $datas->target_date ?>
			<?php $translator = $datas->translator ?>
			<?php $category = $datas->category ?>
			<?php $position = $datas->position ?>
			<?php $status = $datas->status ?>
			@endforeach
			@endif

			@if($data[0]->position == "interpreter")

			<h3>Dear Interpreter</h3>

			<p style="font-size: 20px;">A New Sakurentsu Has Been Created<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Sakuretsu Number</td>
						<td style="border:1px solid black; text-align: center;">{{$sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Title</td>
						<td style="border:1px solid black; text-align: center;">{{$title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: center;">{{$applicant}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Sakurentsu Category</td>
						<td style="border:1px solid black; text-align: center;">{{$category}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Date Implementation Target</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($target_date)) ?></td></td>
					</tr>
				</tbody>
			</table>

			<br>

			<b>Please Assign to Staff</b>
			<br>Thank you<br><br>

			Click Below To <br><br> 
			<a href="{{ url('index/sakurentsu/assign/'.$data[0]->sakurentsu_number.'/interpreter') }}">Assign to Staff</a>

			<br><br>

			<span style="font-size: 20px">Regards,</span>
			<br><br>

			<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

			@elseif($data[0]->position == "interpreter2")

			<h3>Dear {{$data[0]->translator}}</h3>

			<p style="font-size: 20px;">A New Sakurentsu Has Been Created<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Sakuretsu Number</td>
						<td style="border:1px solid black; text-align: center;">{{$sakurentsu_number}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Title</td>
						<td style="border:1px solid black; text-align: center;">{{$title_jp}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: center;">{{$applicant}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Sakurentsu Category</td>
						<td style="border:1px solid black; text-align: center;">{{$category}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Date Implementation Target</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($target_date)) ?></td></td>
					</tr>
				</tbody>
			</table>

			<br>

			<b>Please Translate Sakurentsu Based On File Attached</b>
			<br>Thank you<br><br>

			Click Below To <br><br> 
			<a href="{{ url("index/sakurentsu/upload_sakurentsu_translate/".$id) }}">View & Upload Translated Sakurentsu</a>

			<br><br>

			<span style="font-size: 20px">Regards,</span>
			<br><br>

			<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

			@elseif($data[0]->position == "PC1")

			<h3>Dear PC Team</h3>

			<p style="font-size: 20px;">A New Sakurentsu Has Been Created & Translated<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3>Sakuretsu Number {{$sakurentsu_number}}
				<br>Applicant {{$applicant}}</h3>
				<br>Translator {{$translator}}</h3>

				<br>Please Check This Sakurentsu
				<br>Thank you<br><br>

				<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
				<a href="{{ url("receive/sakurentsu/trial/meeting/".$id) }}">Receive and schedule meetings</a>

				<br><br>

				<span style="font-size: 20px">Regards,</span>
				<br><br>

				<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

				@elseif($data[0]->position == "PC2")

				<h3>Dear PC Team</h3>

				<p style="font-size: 20px;">A New Sakurentsu Has Been Created & Translated<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
				This is an automatic notification. Please do not reply to this address.

				<h3>Sakuretsu Number {{$sakurentsu_number}}
					<br>Applicant {{$applicant}}</h3>
					<br>Translator {{$translator}}</h3>

					<br>Please Check This Sakurentsu
					<br>Thank you<br><br>

					<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
					<a href="{{ url("index/sakurentsu/detail/".$id) }}">View Sakurentsu Detail</a>

					<br><br>

					<span style="font-size: 20px">Regards,</span>
					<br><br>

					<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

					@elseif($data[0]->position == "PIC")

					<h3>Dear {{$data[0]->pic}} Manager</h3>

					<p style="font-size: 20px;">A new Sakurentsu has been Made for {{$data[0]->pic}} <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
					This is an automatic notification. Please do not reply to this address.
					<br>
					<table style="border:1px solid black; border-collapse: collapse;" width="70%">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th style="width: 2%; border:1px solid black;">Point</th>
								<th style="width: 2%; border:1px solid black;">Content</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="width: 2%; border:1px solid black;">Sakuretsu Number</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->sakurentsu_number}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Sakurentsu Title</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->title}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Applicant</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->applicant}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Upload Date</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->upload_date}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Target Date</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->target_date}}</td></td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Translator</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->translator}}</td></td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Sakurentsu Category</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->category}}</td></td>
							</tr>
						</tbody>
					</table>

					<br>

					@if($data[0]->category == "3M") 
					<b>Please Check This Sakurentsu and Assign to Staff</b>
					<br>Thank you<br><br>

					Click Below to <br><br> 
					<a href="{{ url('index/sakurentsu/assign/'.$data[0]->sakurentsu_number.'/manager') }}">Assign to Staff</a>
					@elseif($data[0]->category == "Trial")
					<b>Please Check This Sakurentsu and Assign to Staff</b>
					<br>Thank you<br><br>

					Click Below to <br><br> 
					<a href="{{ url('index/sakurentsu/assign/'.$data[0]->sakurentsu_number.'/manager') }}">Accept Trial Request & Assign to Staff</a>
					@elseif($data[0]->category == "Information")
					<b>Please Receive This Sakurentsu</b>
					<br>Thank you<br><br>

					Click Below to <br><br> 
					<a href="{{ url('index/sakurentsu/information/receive/'.$data[0]->sakurentsu_number) }}">Receive Sakurentsu Information</a>
					@endif

					<br><br>

					<span style="font-size: 20px">Regards,</span>
					<br><br>

					<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>

					@elseif($data[0]->position == "PIC2")

					@if($data[0]->category == "Trial" && isset($data[0]->trial_file))

					<h3>Dear Adianto Heru Prasetia</h3>

					<p style="font-size: 20px;">A new Trial Request has been Created <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
					@else

					<h3>Dear {{$data[0]->pic}}</h3>

					<p style="font-size: 20px;">A new Sakurentsu has been Made for {{$data[0]->pic}} <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
					@endif

					This is an automatic notification. Please do not reply to this address.
					<br>
					<table style="border:1px solid black; border-collapse: collapse;" width="70%">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th style="width: 2%; border:1px solid black;">Point</th>
								<th style="width: 2%; border:1px solid black;">Content</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="width: 2%; border:1px solid black;">Sakuretsu Number</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->sakurentsu_number}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Sakurentsu Title</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->title}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Applicant</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->applicant}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Upload Date</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->upload_date}}</td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Target Date</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->target_date}}</td></td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Translator</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->translator}}</td></td>
							</tr>
							<tr>
								<td style="width: 2%; border:1px solid black;">Sakurentsu Category</td>
								<td style="border:1px solid black; text-align: center;">{{$data[0]->category}}</td></td>
							</tr>
						</tbody>
					</table>

					<br>

					@if($data[0]->category == "3M") 
					<b>Please Check This Sakurentsu and Create 3M Form</b>
					<br>Thank you<br><br>

					Click Below to <br><br> 
					<a href="{{ url('index/sakurentsu/list_3m') }}">View 3M List</a>

					@elseif($data[0]->category == "Trial" && !isset($data[0]->trial_file))
					<b>Please Check This Sakurentsu and Upload Trial Results</b>
					<br>Thank you<br><br>

					Click Below to <br><br> 
					<a href="{{ url('index/sakurentsu/list_trial_temp') }}">View Trial Request List </a>

					@elseif($data[0]->category == "Trial" && isset($data[0]->trial_file))
					<b>Please Specify PSS Requirements</b>
					<br>Thank you<br><br>

					Click Below to <br><br> 
					<a href="{{ url('index/sakurentsu/trial/pss/'.$data[0]->sakurentsu_number) }}">Specify PSS</a>

					@endif

					<br><br>

					<span style="font-size: 20px">Regards,</span>
					<br><br>

					<span style="font-size: 20px;font-weight: bold">MIRAI - MIS Team</span>
					@endif
				</center>
			</div>
		</body>
		</html>