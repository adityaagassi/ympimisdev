<!DOCTYPE html>
<html>
<head>
	<title>Overtime</title>
</head>

<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<style type="text/css">
	@page {
		size: A4 portrait;
		margin: 1;
	}
	@media print {
		#tb-collapse {
			background-color: #dddddd !important;
			-webkit-print-color-adjust: exact;
		}
		body {
			font-size: 16pt;
		}

		.head {
			background-color: #dddddd !important
		}

		.foot {
			background-color: #e6e8e1 !important;
		}
	}

	body {
		font-family: sans-serif;
		padding: 5px;
	}
	.div {
		border: 1px solid black;
		width: 90%;
		height: 35px;
		margin: 0 auto;
		padding: 0 5px 0 5px;
		line-height: 25px;
	}
	.kep {
		border: 1px solid black;
	}

	.head {
		background-color: #dddddd
	}

	.foot {
		background-color: #e6e8e1;
	}

	p {
		max-width: 300px;
		word-wrap: break-word;
	}
	#anggota {
		border-top: 1px solid black;
		border-collapse:collapse;
	}
	#anggota tr th { 
		border-bottom: 1px solid #000;
		padding: 5px 0 5px 0;
		text-align: center;
	}
	#anggota #bottom td { 
		border-top: 1px solid #000;
		padding: 5px 0 5px 0;
	}
	#tb-collapse {
		border-collapse:collapse; border: 1px solid black;
	}
	#tb-collapse td {
		border: 1px solid black;
	}

	table,
	table tr td,
	table tr th {
		page-break-inside: avoid;
	}
</style>
<body>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<?php	
	$hari = date("D",strtotime($datas[0]->overtime_date));

	switch($hari){
		case 'Sun':
		$hari_ini = "Minggu";
		break;

		case 'Mon':			
		$hari_ini = "Senin";
		break;

		case 'Tue':
		$hari_ini = "Selasa";
		break;

		case 'Wed':
		$hari_ini = "Rabu";
		break;

		case 'Thu':
		$hari_ini = "Kamis";
		break;

		case 'Fri':
		$hari_ini = "Jumat";
		break;

		case 'Sat':
		$hari_ini = "Sabtu";
		break;
		
		default:
		$hari_ini = "Tidak di ketahui";		
		break;
	}
	?>
	<table width="100%" border="0" style="padding-right: 20px;">
		<tr>
			<td width="85%" colspan="5"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></td>
		</tr>
		<tr>
			<td colspan="4"><center><h2>FORM LEMBUR KARYAWAN</h2></center></td>
			<td style="text-align: center;">
				<b>ID SPL : {{$datas[0]->overtime_id}}</b>
			</td>
		</tr>
		<tr>
			<td width="10%" style="padding: 5px 0  5px 20px">Hari</td>
			<td width="2%">:</td>
			<td width="35%" ><?php echo $hari_ini; ?></td>
			<td colspan="2" style="padding-right: 20px">Keperluan :</td>
		</tr>
		<tr>
			<td width="10%" style="padding: 5px 0  5px 20px">Tanggal</td>
			<td width="2%">:</td>
			<td width="25%"><p id='tgl'>
				<?php 
				$time = strtotime($datas[0]->overtime_date);

				$newformat = date('d-m-Y',$time);

				echo $newformat;
				?>
			</p></td>
			<td colspan="2" rowspan="2" class='kep' style="padding: 7px"><p>{{$datas[0]->purpose}}</p></td>
		</tr>
		<tr>
			<td width="10%" style="padding: 5px 0  5px 20px">Bagian</td>
			<td width="2%">:</td>
			<td width="25%"><?php echo $datas[0]->department." - ".$datas[0]->section." - ".$datas[0]->subsection." - ".$datas[0]->group ?></td>
		</tr>
	</table>
	<table width="100%" style="margin-top: 10px" id="anggota"  align="center" border="1">
		<tr class="head">
			<th width="3%">No</th>
			<th width="9%">NIK</th>
			<th >Nama</th>
			<th width="5%">Dari</th>
			<th width="5%">Sampai</th>
			<th width="5%">Trans</th>
			<th width="6%">Makan</th>
			<th width="6%">E.Food</th>
			<th width="8%">TTD</th>
			<th width="4%">Jam</th>
			<th width="8%">TTD Atasan</th>
		</tr>
		<?php $no=1; $jml=0; $total=0; $mkn=0; $efood=0; $b=0; $p=0; 
		foreach ($datas as $key) { ?>
		<tr>
			<td style="padding: 10px 0 10px 0; text-align: center"><?php echo $no ?></td>
			<td style="text-align: center"><?php echo $key->employee_id ?></td>
			<td><?php echo $key->name ?></td>
			<td style="text-align: center;"><?php echo date("H:i",strtotime($key->start_time)); ?></td>
			<td style="text-align: center;"><?php echo date("H:i",strtotime($key->end_time)) ?></td>
			<td style="text-align: center;"><?php echo $key->transport; ?></td>
			<td style="text-align: center;"><?php if ($key->food == 1){ echo "&#x2714"; $mkn+=1; }?></td>
			<td style="text-align: center;"><?php if ($key->ext_food == 1){ echo "&#x2714"; $efood+=1;} ?></td>
			<td></td>
			<td style="text-align: center"><?php echo $key->final_hour ?></td>
			<td></td>
		</tr>
		<?php 
		if ($key->transport == "B")
			$b+=1;
		if ($key->transport == "P")
			$p+=1;
		$jml += (float) $key->final_hour; 
		$no++;
	} ?>
	<tr class="foot">
		<td colspan="5" style="text-align: left;">B = Bangil <br> P = Pasuruan</td>
		<td style="text-align: center;">B = <?php echo $b ?><br>P = <?php echo $p ?></td>
		<td style="text-align: center;"><?php echo $mkn ?></td>
		<td style="text-align: center;"><?php echo $efood ?></td>
		<td style="text-align: right;">Total = &nbsp;</td>
		<td style="text-align: center;"><?php echo $jml; ?></td>
		<td>&nbsp; Jam</td>
	</tr>
</table>	

<table border="0" width="100%">
	<tr>
		<tr>
			<td colspan="2">Catatan :</td>
		</tr>
		<td width="50%" style="padding-right: 4px">
			<div class="div" style="height: 157px; margin: 0; width: 100%"><?php echo $datas[0]->remark ?></div>
		</td>
		<td style="padding-left: 4px">

			<table width="100%" id="tb-collapse" style="margin: 0;padding: 0; background-color: #dddddd">
				<tr>
					<td width="25%" cellpading="0" cellspacing="0">Diusulkan,</td>
					<td width="25%">Disetujui,</td>
					<td width="25%">Diketahui,</td>
					<td width="25%">Diterima,</td>
				</tr>
				<tr>
					<td>
						Staff / Leader
					</td>
					<td>
						Chief / Foreman
					</td>
					<td>Dept. Manager</td>
					<td>HR Dept.</td>
				</tr>
				<tr>
					<td height="92px"></td><td></td><td></td><td></td>
				</tr>
				<tr><td style="text-align: left;">tgl. </td><td style="text-align: left;">tgl. </td><td style="text-align: left;">tgl. </td><td style="text-align: left;">tgl. </td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="100%" height="100%" id="tb-collapse" style="background-color: #dddddd; margin-top: 10px; text-align: center">
				<tr><td width="34%">BUDGET ACCUMULATIVE</td><td width="33%">ACTUAL ACCUMULATIVE</td><td width="33%">DIFF</td></tr>
				<tr>
					<td height="20px"><d id="target" style="font-size: 25pt"></d></td>
					<td><d style="font-size: 25pt" id="aktual"></d></td>
					<td><d style="font-size: 25pt" id="diff"></d></td>
				</tr>
				<tr><td colspan="3" height="150px">
					<p id="cc" hidden><?php echo $datas[0]->cost_center ?></p>
					<div id="container" style = "height: 148px; margin: 0 auto"></div>
				</td></tr>
			</table>
		</td>
	</tr>
</table>

<script src="{{ url("js/highcharts.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$(document).ready(function() {
		var tgl = $('#tgl').text();
		var cc = $('#cc').text();
		var url = '{{ url("fetch/report/overtime_graph") }}';

		var json_act = new Array();
		var json_budget = new Array();
		var cat = new Array();
		var act = 0, bdg = 0;

		$.ajax({
			type: "POST",
			url: url,
			data: {
				tanggal: '{{$datas[0]->overtime_date}}',
				cc: cc
			},
			success: function(data) {
				for (i = 0; i < data.datas.length; i++){
					act += parseFloat(data.datas[i].final);
					bdg += parseFloat(data.datas[i].day_bdg);
					json_act.push(act);
					json_budget.push(Math.round(bdg * 100) / 100);
					cat.push(data.datas[i].week_date);
				}

				console.log(json_budget);
				// var target = s[0][2];

				var charts = $('#container').highcharts({

					chart:
					{
						backgroundColor : "rgba(255, 255, 255, 0.0)",
					},
					title: {
						text: ''
					},

					yAxis: {
						softMax: 50,
						allowDecimals: true,
						min: 4,
						title: {
							text: 'Overtime (hours)'
						},
						gridLineColor: '#fff'
					},

					xAxis: {
						lineColor: '#fff',
						categories : cat
					},

					legend: {
						enabled: false
					},

					plotOptions: {

						line: {
							animation: false,
							dataLabels: {
								enabled: true
							},
							enableMouseTracking: false,
						},
						spline: {
							animation: false
						}
					},

					series: [{
						name: 'Act',
						color: '#000',
						data: json_act
					},
					{
						name: 'Target',
						type: 'spline',
						color: 'red',
						marker: {
							enabled: false
						},
						data: json_budget,
						dashStyle: 'dash'
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					},
					credits: {
						enabled: false
					},
				})

				$('#target').text(bdg.toFixed(2));
				$('#aktual').text(act);
				$('#diff').text((bdg-act).toFixed(2));

				window.print();
			}
		});
	})

</script>

</body>
</html>