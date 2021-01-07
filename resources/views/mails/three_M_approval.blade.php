<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding: 3px;
		}

		#sakurentsu_table > tbody > tr > th {
			background-color: rgb(126,86,134);
		}
		#tiga_m_table > tbody > tr > th {
			background-color: #605ca8;
		}
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 22px; font-weight: bold;">
				<?php $ids = $data["datas"]['id']; ?>
				<?php if ($data["position"] == "PRESDIR") { ?>
					3M変更申請の承認 Approval 3M Application
				<?php } else if ($data["position"] == "STD" || $data["position"] == "ALL" || $data["position"] == "IMPLEMENT" || $data["position"] == "TRANSLATE" || $data["position"] == "INTERPRETER") { ?>
					3M申請書 3M Application
				<?php } ?>
				<br>
			(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			自動通知です。返事しないでください。 <br><br>
			<p style="font-size: 18px">
				<?php if ($data["position"] == "ALL") { ?>
					This 3M Application has fully approved, Now it can be implemented. <br>
					Don't forget to make the implementation Report. <br><br>
				<?php } else if ($data["position"] == "IMPLEMENT") { ?>
					This 3M Application has been implemented and checked by PIC. <br>
					Please verify this Implementation Report. <br><br>
				<?php } else if ($data["position"] == "INTERPRETER") { ?>
					This 3M Application has been created. <br>
					Please Translate this 3M Application. <br><br>
				<?php } else if ($data["position"] == "TRANSLATE") { ?>
					This 3M Application has been translated. <br>
					Please Check and don't forget to schedule a meeting. <br><br>
				<?php } ?>
			</p>

			<!-- Jika 3M dari Sakurentsu -->
			<?php  if ($data["datas"]['sakurentsu_number']) { ?>

				<table style="border: hidden; width: 80%" id="sakurentsu_table">
					<tr><th colspan="2" style="background-color: rgb(179, 117, 191);">Sakurentsu</th></tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);" width="35%">作連通番号 Sakurentsu Number</th>
						<td>{{ $data["datas"]["sakurentsu_number"] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">作連通の表題 Sakurentsu Title</th>
						<td>{{ $data["datas"]["title_sakurentsu_jp"] }}  {{ $data["datas"]["title_sakurentsu"] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">申請者 Applicant</th>
						<td>{{ $data["datas"]['applicant'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">締切 Target Date</th>
						<td>{{ $data["datas"]['target_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: rgb(179, 117, 191);">アップロード日付 Upload Date</th>
						<td>{{ $data["datas"]['upload_date'] }}</td>
					</tr>
				</table>
			<?php }	 ?>
			<br>
			<table style="border-color: black; width: 80%;" id="tiga_m_table">
				<tr><th colspan="2" style="background-color: #605ca8;">3M申請書 3M Application</th></tr>
				<tr>
					<th style="text-align: left; background-color: #605ca8;" width="35%">3M変更表題 3M Title</th>
					<td>{{ $data["datas"]['title'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #605ca8;">製品名 Product Name</th>
					<td>{{ $data["datas"]['product_name'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #605ca8;">工程名 Proccess Name</th>
					<td>{{ $data["datas"]['proccess_name'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #605ca8;">班名 Unit Name</th>
					<td>{{ $data["datas"]['unit'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #605ca8;">3M変更種類 3M Category</th>
					<td>{{ $data["datas"]['category'] }}</td>
				</tr>
				<tr>
					<th style="text-align: left; background-color: #605ca8;">作成日付け Created Date</th>
					<td>{{ $data["datas"]['created_at'] }}</td>
				</tr>
			</table>
			<br>

			<!-- Jika Sudah implementasi -->
			<?php if ($data["position"] == "IMPLEMENT") { ?>
				<table style="border-color: black; width: 80%;" id="tiga_m_table">
					<tr><th colspan="2" style="background-color: #605ca8;">3M Implementation Report</th></tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;" width="35%">No Reff. 3M</th>
						<td>{{ $data["implement"]['form_number'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Department</th>
						<td>{{ $data["implement"]['section'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Name</th>
						<td>{{ $data["implement"]['name'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Date Issued 3M</th>
						<td>{{ $data["implement"]['form_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Title</th>
						<td>{{ $data["implement"]['title'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Isi Perubahan & Alasan Perubahan</th>
						<td>{{ $data["implement"]['reason'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Tanggal Rencana Perubahan</th>
						<td>{{ $data["implement"]['started_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Tanggal Aktual Perubahan</th>
						<td>{{ $data["implement"]['actual_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Tanggal Pengecekan</th>
						<td>{{ $data["implement"]['check_date'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Yang Melakukan Pengecekan</th>
						<td>{{ $data["implement"]['checker'] }}</td>
					</tr>
					<tr>
						<th style="text-align: left; background-color: #605ca8;">Lampiran</th>
						<td>{{ $data["implement"]['att'] }}</td>
					</tr>
				</table>
				<br>
			<?php } ?>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Below to</i> &#8650;</span><br><br>
			<!-- <a href="{{ url('detail/sakurentsu/3m/'.$data["datas"]['id']) }}">See 3M Detail & Approval</a> -->

			<?php if ($data["position"] == "PRESDIR") { $id = $data["datas"]['id']; ?>

			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('detail/sakurentsu/3m/'.$id.'/presdir') }}">&nbsp;&nbsp;&nbsp; See 3M Detail & Approval &nbsp;&nbsp;&nbsp; <br> 3M変更の詳細＆承認</a>

			<?php }  else if ($data["position"] == "STD") { $id = $data["datas"]['id']; ?>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('detail/sakurentsu/3m/'.$id.'/finish') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
			<?php }  else if ($data["position"] == "ALL") { $id = $data["datas"]['id']; ?>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('detail/sakurentsu/3m/'.$id.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
			<?php }  else if ($data["position"] == "IMPLEMENT") { $id = $data["datas"]['id']; ?>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('detail/sakurentsu/3m/'.$id.'/implement') }}">&nbsp;&nbsp;&nbsp; 3M Detail &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更の詳細 &nbsp;&nbsp;&nbsp;</a>
			<?php }  else if ($data["position"] == "TRANSLATE") { $id = $data["datas"]['id']; ?>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('index/sakurentsu/list_3m') }}">&nbsp;&nbsp;&nbsp; view 3M List &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; ?? &nbsp;&nbsp;&nbsp;</a>
			<?php }  else if ($data["position"] == "INTERPRETER") { $id = $data["datas"]['id']; ?>
			<a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size:20px; text-decoration: none;" href="{{ url('index/sakurentsu/3m/translate/'.$id) }}">&nbsp;&nbsp;&nbsp; Translate 3M &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; ?? &nbsp;&nbsp;&nbsp;</a>
			<?php } ?>
<br>
<br>
<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br><br>
<a style="background-color: blue; width: 50px;text-decoration: none;color: white;font-size:15px; text-decoration: none;" href="{{ url('detail/sakurentsu/3m/'.$ids.'/view') }}">&nbsp;&nbsp;&nbsp; 3M Monitoring &nbsp;&nbsp;&nbsp; <br>&nbsp;&nbsp;&nbsp; 3M変更監視 &nbsp;&nbsp;&nbsp;</a>
</center>
</div>
</body>
</html>