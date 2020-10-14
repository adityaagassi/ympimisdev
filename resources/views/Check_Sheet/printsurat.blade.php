<!DOCTYPE html>
<html>
<head>
  <title>YMPI 情報システム</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style type="text/css">
    body{
      font-size: 10px;
      font-family: Calibri, sans-serif; 
    }

    #isi > thead > tr > td {
      text-align: center;
    }

    #isi > tbody > tr > td {
/*      text-align: left;
padding-left: 5px;*/
text-align: center
}

.centera{
  text-align: center;
  vertical-align: middle !important;
}

.line{
 width: 100%; 
 text-align: center; 
 border-bottom: 1px solid #000; 
 line-height: 0.1em;
 margin: 10px 0 20px;  
}

.line span{
 background:#fff; 
 padding:0 10px;
}

@page { }
.footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
.footer .pagenum:before { content: counter(page); }
</style>
</head>
<body>
  <header>
    <table style="width: 100%; border-collapse: collapse; text-align: left;" >
      <thead>
       <tr>
        <td colspan="5" style="font-weight: bold;font-size: 13px; width: 50%">
          <b>
            PT.YAMAHA MUSICAL PRODUCTS INDONESIA
          </b>
        </td>
        <td colspan="4" style="font-size: 12px; width: 50%;">
          Pasuruan, {{date('d M Y', strtotime($checksheet->Stuffing_date))}} 
        </td>
      </tr>
      <tr>
        <td colspan="5" style="font-size: 12px;">Jl. Rembang Industri I/36</td>
        <td colspan="4" style="font-size: 12px;">Kepada :</td>
      </tr>
      <tr>
        <td colspan="5" style="font-size: 12px; vertical-align: top;">Kawasan Industri PIER Pasuruan<br>
          Phone: (0343) 740290<br>
          Fax: (0343) 740291
        </td>
        <td colspan="4" style="font-size: 12px;">Yth. <br>
          <b>
            <?php $towards = explode('-', $checksheet->toward) ?>
            @foreach($towards as $toward)
            {{ $toward }}<br>
            @endforeach
          </b>
        </td>
      </tr>
    </thead>
  </table>
</header>
<main>
  <table style="width: 100%;border-collapse: collapse;">
    <thead>
      <tr>
        <th colspan="2" style="border: none"><h1><center>SURAT JALAN EKSPOR</center></h1></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width: 1%;border: none;font-size: 11px">Surat Jalan No. </td>
        <td style="border: none;font-size: 11px">: {{ $checksheet->id_checkSheet }}</td>  
      </tr>
      <tr>
        <td style="width: 1%;border: none;font-size: 11px">Kendaraan/No.Pol </td> 
        <td style="border: none;font-size: 11px">: {{ $checksheet->no_pol }}</td>               
      </tr>
      <tr>
        <td style="width: 1%;border: none;font-size: 11px">No.Container/Size </td>              
        <td style="border: none;font-size: 11px">: {{$checksheet->countainer_number}} / {{ $checksheet->ct_size }}</td>  
      </tr>
      <tr>
        <td style="width: 1%;border: none;font-size: 11px">No. Segel </td>        
        <td style="border: none;font-size: 11px">: {{ $checksheet->seal_number }}</td>
      </tr>
    </tbody>
  </table>
  <br><br>
  <table border="1" width="99%" style="width: 100%; border-collapse: collapse;" id="isi" >
    <thead>
      <tr id="cargo">
        <th style="border: 1px solid"><center>No</center></th>
        <th style="border: 1px solid"><center>No. Inv</center></th>
        <th style="border: 1px solid"><center>Kode</center></th>
        <th style="border: 1px solid"><center>Nama Barang</center></th>
        <th colspan="2" style="border: 1px solid"><center>PKG</center></th>
        <th colspan="2" style="border: 1px solid"><center>Jumlah</center></th>
        <th style="border: 1px solid"><center>Keterangan</center></th>
      </tr>
    </thead>
    <tbody>
      <?php $count = 1; ?>
      @foreach($checksheet_details as $checksheet_detail)
      <tr>
        <td style="border-right: 1px solid; border-left: 1px solid" align="center">{{ $count }}</td>
        <td style="border-right: 1px solid; border-left: 1px solid">{{ substr($checksheet_detail->no_invoice, 0, 6) }}</td>
        <td style="border-right: 1px solid; border-left: 1px solid">{{$checksheet_detail->material_number}}</td>
        <td style="border-right: 1px solid; border-left: 1px solid">{{$checksheet_detail->material_description}}</td>
        <td style="border-right: 1px solid; border-left: 1px solid" align="RIGHT">{{$checksheet_detail->no_package}}</td>
        <td style="border-right: 1px solid; border-left: 1px solid">{{$checksheet_detail->package}}</td>
        <td style="border-right: 1px solid; border-left: 1px solid" align="RIGHT">{{$checksheet_detail->quantity}}</td>
        <td style="border-right: 1px solid; border-left: 1px solid">{{$checksheet_detail->uom}}</td>
        <td style="border-right: 1px solid; border-left: 1px solid" align="center">
          BY @if($checksheet->shipment_condition_name)
          {{$checksheet->shipment_condition_name}}
          @else
          -
          @endif
        </td>
      </tr>
      <?php $count++ ?>
      @endforeach
    </tbody>
  </table>        
  <br>
  <table width="100%"> 
    <tr>
      <td style="border: none;font-size: 12px; font-weight: bold; text-align: left;">Penerima</td>
      <td style="border: none;font-size: 12px; font-weight: bold; text-align: center;">Pengirim</td>
    </tr>
  </table>
</main>
</body>
</html>