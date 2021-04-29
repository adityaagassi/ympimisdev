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
			<p style="font-size: 18px;">Japanese Food Order <span style="color: purple;">和食弁当の予約</span><br>
			This is an automatic notification. Please do not reply to this address.</p>
			<span style="font-weight: bold; color: green; font-size: 24px;">Your Order Has Been APPROVED</span>
			<br>
			<span style="font-weight: bold;">by: {{ $data['bento_lists'][0]["approver_id"] }} - {{ $data['bento_lists'][0]["approver_name"] }}</span>
			<br>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="60%">
				<thead style="background-color: #63ccff">
					<tr>
						<th style="border:1px solid black; width: 2%;">Name</th>
						<?php
						for ($i=0; $i < count($data['calendars']); $i++) {
							print_r('<th style="border:1px solid black; width: 1%;">'.date('d M', strtotime($data['calendars'][$i]['week_date'])).'</th>');
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					$name = [];
					for ($i=0; $i < count($data['bento_lists']); $i++) {
						if(!in_array($data['bento_lists'][$i]['employee_name'], $name)){
							array_push($name, $data['bento_lists'][$i]['employee_name']);
						}
					}
					for ($i=0; $i < count($name); $i++) {
						print_r('<tr>');

						print_r('
							<td style="border: 1px solid black;">'.$name[$i].'</td>
							');	

						for ($j=0; $j < count($data['calendars']); $j++) { 
							$inserted = false;

							for ($k=0; $k < count($data['bento_lists']); $k++) {
								if($data['calendars'][$j]['week_date'] == $data['bento_lists'][$k]['due_date'] && $data['bento_lists'][$k]['employee_name'] == $name[$i]){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #ccff90; font-weight: bold;">&#9711;</td>');
									$inserted = true;
								}
							}
							if(!$inserted){
								print_r('<td style="border: 1px solid black; text-align: center; background-color: #ff6090;"></td>');
							}
						}
						print_r('</tr>');
					}
					?>
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>