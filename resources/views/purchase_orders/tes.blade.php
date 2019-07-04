<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>YMPI 情報システム</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
  {{-- <link rel="stylesheet" href="{{ url("fonts/SourceSansPro.css")}}"> --}}
  <style type="text/css">
    @font-face {
      font-family: 'couriernew';
      font-weight: normal;
      font-style: normal;
      font-variant: normal;
      src: url('{{ url("fonts/couriernew.ttf")}}'); 
      font-size: 11px;
    }

    body {
      font-family: 'couriernew';
      font-weight: normal;
      font-style: normal;
      font-variant: normal;
      src: url('{{ url("fonts/couriernew.ttf")}}'); 
      font-size: 11px;
    }


    .head2 {
      font-size:50px;
    }

    #tableHeader>tbody>tr>td{
      padding:5px;
    }
    #tableHeader2>tbody>tr>td{
      padding-left:5px;
      padding-right:5px;
    }
    #tableDetail>thead>tr>th{
      padding-left:5px;
      padding-right:5px;
    }
    p{
      margin:0;
    }
  </style>
</head>
<body>
  <input type="file" accept="image/*" capture="camera" />
  <div class="wrapper">
    <section class="invoice">
      <hr style="border: 1px solid black; margin-bottom: 0;">
      <center><span class="head2">PURCHASE ORDER</span></center>
      <div class="row invoice-info">

        <div class="row" id="hd">
          <div class="col-xs-12" style="margin-bottom: 10px;">
            <div class="col-xs-8 invoice-col">
              <table style="width: 100%;" border="1px" id="tableHeader">
                <tbody>
                  <tr>
                    <td style="width: 15%">Order No.</td>
                    <td>asdas</td>
                    <td style="width: 20%">Revision No.</td>
                    <td>asdas</td>
                  </tr>
                  <tr>
                    <td style="width: 15%">Order Date</td>
                    <td>asdas</td>
                    <td style="width: 20%">Revision Date</td>
                    <td>asdas</td>
                  </tr>
                  <tr>
                    <td style="width: 15%">Buyer</td>
                    <td colspan="3">asdas</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-xs-4 invoice-col">
              <table style="width: 100%;" border="1px" id="tableHeader">
                <tbody>
                  <tr>
                    <td style="width: 30%">Doc. No.</td>
                    <td>asdas</td>
                  </tr>
                  <tr>
                    <td style="width: 30%">Page</td>
                    <td>asdas</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-12" style="margin-bottom: 10px;">
            <div class="col-xs-6 invoice-col">
              <div class="col-xs-12" style="border: 1px solid black; padding-left: 5px; height: 155px;">
                <p>
                  Vendor :<br><br>
                  vendor_code<br>
                  street
                </p>
                <p style="text-align: right;">
                  city<br>
                  postl_code
                </p>
                <p>
                  cty
                </p>
              </div>
            </div>
            <div class="col-xs-6 invoice-col">
              <p>
                Shipped to :<br>
                PT. YAMAHA MUSICAL PRODUCTS INDONESIA<br>
                JL. Rembang Industri I/36, Kawasan Industri PIER,<br>
                Pasuruan-Jawa Timur<br>
                <span>Indonesia</span><span class="pull-right">NPWP : 01.824.283.4-052.000</span>
                <hr style="border: 1px solid black; margin: 0;">
                Invoice to :<br>
                PT. YAMAHA MUSICAL PRODUCTS INDONESIA<br>
                JL. Rembang Industri I/36, Kawasan Industri PIER,<br>
                Pasuruan-Jawa Timur<br>
                <span>Indonesia</span>
              </p>
            </div>
          </div>
          <div class="col-xs-12" style="margin-bottom: 10px;">
            <div class="col-xs-12">
              <table style="width: 100%;" id="tableHeader">
                <tbody>
                  <tr style="border-top: 1px solid black;">
                    <td style="width: 10%;">Confirmed to</td>
                    <td>asdas</td>
                    <td style="width: 10%;">Phone No.</td>
                    <td>asdas</td>
                    <td style="width: 10%;">Fax No.</td>
                    <td>asdas</td>
                  </tr>
                  <tr style="border-top: 1px solid black;">
                    <td style="width: 10%;">Transportation</td>
                    <td>asdas</td>
                    <td style="width: 10%;">Delivery terms</td>
                    <td>asdas</td>
                    <td style="width: 10%;">Currency</td>
                    <td>asdas</td>
                  </tr>
                  <tr style="border-top: 1px solid black; border-bottom: 1px solid black;">
                    <td style="width: 15%;">Payment terms</td>
                    <td colspan="5">asdas</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xs-12" style="margin-bottom: 10px;">
            <span style="font-size: 18px; font-weight: bold;">Order Details</span>
            <table style="width: 100%" id="tableDetail">
              <thead style="border-top: 1px solid black; border-bottom: 1px solid black;">
                <tr>
                  <th style="border-right: 1px solid black;">No.</th>
                  <th style="border-right: 1px solid black;">Material Code<br>Tracking No.</th>
                  <th style="border-right: 1px solid black;">Description<br>Work Order No.</th>
                  <th style="border-right: 1px solid black;">Delivery Date</th>
                  <th style="border-right: 1px solid black; text-align: right;">Quantity</th>
                  <th style="border-right: 1px solid black;">UM</th>
                  <th style="border-right: 1px solid black; text-align: right;">Unit Price</th>
                  <th style="text-align: right;">Amount</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>

        <div class="row" id="bd">
        </div>

        <div class="row" id="ft">
          <div class="col-xs-12 invoice-col">
            <hr style="border: 1px dashed black; margin: 0;">
            <p style="text-align: right; font-size: 18px; font-weight: bold;">Total Amount: </p>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div class="col-xs-3">
              <hr style="border: 1px solid black; margin: 0;">
              <center><span>Manager</span></center>
            </div>
            <div class="col-xs-6">
            </div>
            <div class="col-xs-3">
              <hr style="border: 1px solid black; margin: 0;">
              <center><span>Vendor Confirmation</span></center>
            </div>
          </div>

        </div>
      </section>
    </div>
  </body>
  </html>
