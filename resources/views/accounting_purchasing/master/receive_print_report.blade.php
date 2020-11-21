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
        <td colspan="6" style="font-weight: bold;font-size: 13px; width: 50%">
          <b>
            PT.YAMAHA MUSICAL PRODUCTS INDONESIA
          </b>
        </td>
        <td colspan="6" style="font-size: 12px; width: 50%;text-align: right;">
          Daftar Distribusi Barang Dari Warehouse
        </td>
      </tr>
    
    </thead>
  </table>
</header>
<main>
  <table style="width: 100%;border-collapse: collapse;">
    <thead>
      <tr>
        <th colspan="2" style="border: none;padding: 0"><h1><center>Penerimaan Barang Warehouse</center></h1></th>
      </tr>
    </thead>
  </table>
  <br>
  <table border="1" width="99%" style="width: 100%; border-collapse: collapse;" id="isi" >
    <thead>
      <tr id="cargo">
        <th style="border: 1px solid;padding: 5px;width: 5%"><center>No</center></th>
        <th style="border: 1px solid;padding: 5px;width: 15%"><center>No PO</center></th>
        <th style="border: 1px solid;padding: 5px;width: 15%"><center>Description</center></th>
        <th style="border: 1px solid;padding: 5px;width: 5%"><center>Qty</center></th>
        <th style="border: 1px solid;padding: 5px;width: 10%"><center>Tanggal Terima</center></th>   
        <th style="border: 1px solid;padding: 5px;width: 5%"><center>TTD</center></th>
      </tr>
    </thead>
    <tbody>
      <?php $count = 1; ?>
      @foreach($receives as $receive)
      <tr>
        <td style="border-right: 1px solid;padding: 5px;border-left: 1px solid" align="center">{{ $count }}</td>
        <td style="border-right: 1px solid;padding: 5px;border-left: 1px solid;text-align: left">{{$receive->no_po }}<br>{{$receive->supplier_name }}</td>
        <td style="border-right: 1px solid;padding: 5px;border-left: 1px solid;text-align: left;" align="left">{{$receive->nama_item}}</td>
        <td style="border-right: 1px solid;padding: 5px;border-left: 1px solid;" align="left">{{$receive->qty_receive}}</td>
        <td style="border-right: 1px solid;padding: 5px;border-left: 1px solid;" align="left">{{date('d-M-Y', strtotime($receive->date_receive))}} </td>
        <td style="border-right: 1px solid;padding: 5px;border-left: 1px solid;text-align: left" align="left">&nbsp;</td>
      </tr>
      <?php $count++ ?>
      @endforeach
    </tbody>
  </table>        
  <br>
  <table width="100%"> 
    <tr>
      <td style="border: none;font-size: 12px; font-weight: bold; text-align: left;">Warehouse</td>
      <td style="border: none;font-size: 12px; font-weight: bold; text-align: center;">Penerima</td>
    </tr>
  </table>
</main>
</body>
</html>