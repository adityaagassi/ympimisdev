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
		
		@if($data[0]->posisi == "manager")

		<p style="font-size: 20px;">Suspend Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Suspend Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Requested By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: <b><?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </b></td>	
				</tr>
			</tbody>
		</table>

		<br>

		<table class="table table-bordered" style="font-family: arial; border-collapse: collapse; text-align: left;" cellspacing="0" width="80%">
			<thead>
				<tr>
					<td><span style="text-align: left; font-size: 17px;">Mengajukan Suspend Dengan Keterangan : </span> </td>
				</tr>
			</thead>
        <tbody align="center">
          <tr>
            <td colspan="2" style="border:1px solid black; font-size: 20px; font-weight: bold; width: 50%; height: 70; background-color: #d4e157"><?= $data[0]->remark ?></td>
          </tr>
        </tbody>            
    </table>

		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Suspend Payment?</i></span>
		<br><br>

		@if($data[0]->posisi == "manager")
		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvemanager/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@endif
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/reject/".$data[0]->id) }}">&nbsp; Reject &nbsp;</a>

		<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		<a href="{{ url('suspend/monitoring') }}">Suspend Payment Monitoring</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">


		@elseif($data[0]->posisi == "direktur") <!-- Pak Arief -->

		<p style="font-size: 20px;">Suspend Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Suspend Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Requested By</td>
					<td>: <?= $data[0]->created_by ?> - <?= $data[0]->created_name ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: <?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
			</tbody>
		</table>
		<br><br>
		<span style="font-weight: bold;"><i>Do you want to Approve This Suspend Payment?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>

		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvedirektur/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/reject/".$data[0]->id) }}">&nbsp; Reject (却下）&nbsp;</a>

		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@elseif($data[0]->posisi == "presdir") <!-- Pak Arief -->

		<p style="font-size: 20px;">Suspend Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>

		This is an automatic notification. Please do not reply to this address.<br>
		自動通知です。返事しないでください。<br>

		<h2>Suspend Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Remark</td>
					<td>: <?= $data[0]->remark ?></td></td>
				</tr>
				
				<tr>
					<td style="width: 25%; ">Amount (数量)</td>
					<td>: <?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
			</tbody>
		</table>
		<br><br>
		<span style="font-weight: bold;"><i>Do you want to Approve This Suspend Payment?<br>こちらの購入依頼を承認しますか</i></span>
		<br><br>

		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/approvepresdir/".$data[0]->id) }}">&nbsp;&nbsp;&nbsp; Approve (承認) &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("suspend/reject/".$data[0]->id) }}">&nbsp; Reject (却下）&nbsp;</a>

		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@elseif($data[0]->posisi == "acc")

		<p style="font-size: 18px;">Suspend Payment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Suspend Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?></h2>

		<table width="80%">
			<tbody>
				<tr>
					<td style="width: 25%; ">Remark</td>
					<td>: <?= $data[0]->remark ?></td></td>
				</tr>
				<tr>
					<td style="width: 25%; ">Amount</td>
					<td>: <?= $data[0]->currency ?> <?= number_format($data[0]->amount ,2,",",".");?> </td>	
				</tr>
			</tbody>
		</table>
		<br><br>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here To</i> &#8650;</span><br>
		<a href="{{ url('suspend/receiveacc/'.$data[0]->id) }}">Receive Suspend Payment</a><br>
		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">
			

		<!-- Tolak -->
		@elseif($data[0]->posisi == "user")

		<p style="font-size: 18px;">Suspend Payment Not Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.
		<br>

		<h2>Suspend Payment {{$data[0]->category}} <?= date('d M y', strtotime($data[0]->submission_date)) ?> Not Approved</h2>
		
		<?php if ($data[0]->alasan != null) { ?>
			<h3>Reason :<h3>
			<h3>
				<?= $data[0]->alasan ?>	
			</h3>
		<?php } ?>

		<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
		
		<a href="{{ url('report/suspend/'.$data[0]->id) }}">Suspend Payment Check</a>
		<br>
		<a href="{{url('index/suspend')}}">Suspend Payment List</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
		</center>
	</div>
</body>
</html>