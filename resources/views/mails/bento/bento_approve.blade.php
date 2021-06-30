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
			<p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
			<span style="font-weight: bold; color: green; font-size: 24px;">Your Order Has Been Confirmed by YMPI あなたのご注文はYMPIにより確認済み</span>
			<br>
			<br>
			<span style="font-weight: bold;">*Note 備考:</span><br>
			<span style="background-color: #ccff90;">&nbsp;&nbsp;&#9745; = Approved 承認済み&nbsp;&nbsp;</span>
			<?php $code = $data['code'] ?>
			@if($data['code'] == 'national')
			<span style="background-color: #ff6090;">&nbsp;&nbsp;&#9746; = Rejected 却下&nbsp;&nbsp;</span>
			@endif
			<span style="">&nbsp;&nbsp;&#9744; = No Order 注文なし&nbsp;&nbsp;</span>
			<br>
			<span style="background-color: #ffee58;">&nbsp;&nbsp;&#9745; = Revise 1 改訂１&nbsp;&nbsp;</span>
			<span style="background-color: #29b6f6;">&nbsp;&nbsp;&#9745; = Revise 2 改訂２以降&nbsp;&nbsp;</span>
			<br>
			<br>
			<table style="border:1px solid black;border-collapse: collapse;" width="95%">
				<thead style="background-color: #63ccff">
					<tr>
						<th style="border:1px solid black; width: 9%; font-size: 11px; padding: 0px 0px 0px 0px;">Name</th>
						<?php
						for ($i=0; $i < count($data['calendars']); $i++) {
							print_r('<th style="border:1px solid black; width: 1%; font-size:11px; padding: 0px 0px 0px 0px;">'.date('d M', strtotime($data['calendars'][$i]['week_date'])).'</th>');
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$name = [];
				for ($i=0; $i < count($data['bento_lists']); $i++) {
					if(!in_array($data['bento_lists'][$i]->employee_name, $name)){
						array_push($name, $data['bento_lists'][$i]->employee_name);
					}
				}
				for ($i=0; $i < count($name); $i++) {
					print_r('<tr>');

					print_r('
						<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px;">'.$name[$i].'</td>
						');	

					for ($j=0; $j < count($data['calendars']); $j++) { 
						$inserted = false;

						for ($k=0; $k < count($data['bento_lists']); $k++) {
							if($data['calendars'][$j]['week_date'] == $data['bento_lists'][$k]->due_date && $data['bento_lists'][$k]->employee_name == $name[$i] && $data['bento_lists'][$k]->status == "Approved"){
								if($data['bento_lists'][$k]->revise == 0){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>');
								}
								if($data['bento_lists'][$k]->revise == 1){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #ffee58; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>');
								}
								if($data['bento_lists'][$k]->revise >= 2){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #29b6f6; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>');
								}
								$inserted = true;
							}
							if($data['calendars'][$j]['week_date'] == $data['bento_lists'][$k]->due_date && $data['bento_lists'][$k]->employee_name == $name[$i] && $data['bento_lists'][$k]->status == "Rejected"){
								if($code == 'national'){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #ff6090; padding: 0px 0px 0px 0px; font-size:16px;">&#9746;</td>');									
								}
								else{
									print_r('<td style="border: 1px solid black; text-align: center; padding: 0px 0px 0px 0px; font-size:16px;">&#9744;</td>');
								}
								$inserted = true;
							}
							if($data['calendars'][$j]['week_date'] == $data['bento_lists'][$k]->due_date && $data['bento_lists'][$k]->employee_name == $name[$i] && $data['bento_lists'][$k]->status == "Cancelled"){
								if($data['bento_lists'][$k]->revise == 1){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #ffee58; color: white; padding: 0px 0px 0px 0px; font-size:16px;">&#9744;</td>');
								}
								if($data['bento_lists'][$k]->revise >= 2){
									print_r('<td style="border: 1px solid black; text-align: center; background-color: #29b6f6; color: white; padding: 0px 0px 0px 0px; font-size:16px;">&#9744;</td>');
								}
								$inserted = true;
							}
						}
						if(!$inserted){
							print_r('<td style="border: 1px solid black; text-align: center; padding: 0px 0px 0px 0px; font-size:16px;">&#9744;</td>');
						}
					}
					print_r('</tr>');
				}
			?>
		</tbody>
	</table>
	<br>
	<br>
	Last order & last change one day before.
	<br>
	注文は遅くても前日となります。注文変更したい場合は遅くても当日の午前9時です。
	<br>
	<a href="http://10.109.52.4/mirai/public/index/ga_control/bento">&#10148; Click this link if you want to change or create order.</a>
	<br>
	<a href="http://10.109.52.4/mirai/public/index/ga_control/bento">&#10148; 注文を作成又は変更したい場合はこちらをクリック</a>
</center>
</div>
</body>
</html>