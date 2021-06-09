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
			<span style="font-weight: bold; color: #ff6090; font-size: 24px;">Sorry, we have to refuse your order, due to lack of quota or unavailability of the menu.<br>申し訳ございません。予約枠が埋まっている、又はメニューが提供されないため、ご予約を拒否いたします。</span>
			<p>If you have any question please contact YMPI's General Affair. 質問がある方は、総務係員までお問い合わせください。</p>
			<br>
			<span style="font-weight: bold;">List of rejected orders 拒否した予約リスト: </span><br>
			<table style="border:1px solid black;border-collapse: collapse;" width="50%">
				<thead>
					<tr>
						<th style="border:1px solid black; width: 1%; font-size:16px; background-color: #ff6090;">Date</th>
						<th style="border:1px solid black; width: 1%; font-size:16px; background-color: #ff6090;">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data); $i++) {
						print_r('<tr>');
						print_r('<td style="border: 1px solid black; text-align: center; padding: 0px 0px 0px 0px; font-size:16px">'.date('d M Y', strtotime($data[$i]['due_date'])).'</td>');
						print_r('<td style="border: 1px solid black; text-align: center; padding: 0px 0px 0px 0px; font-size:16px">'.$data[$i]['status'].'</td>');
						print_r('</tr>');
					}
				?>
			</tbody>
		</table>
		<br>
		<br>
		Last order one day before. Last change on due date before 09:00.
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