<!DOCTYPE html>
<html>
<head>
  <style type="text/css">
    #tableHead1>tbody>tr>td{
      border:1px solid black;
      padding-top: 8px;
      padding-bottom: 8px;
    }
    #tableHead2>tbody>tr>td{
      padding-top: 8px;
      padding-bottom: 8px;
    }
    #tableBody1>tr>td{
      height: 32px
    }
    p{
      margin:0;
    }
    .page-number:after { content: counter(page) " of " counter(pageTotal); }
    #pageCounter {
      counter-reset: pageTotal;
    }
    #pageCounter span {
      counter-increment: pageTotal; 
    }
    #pageNumbers {
      counter-reset: currentPage;
    }
    #pageNumbers div:before { 
      counter-increment: currentPage; 
      content: "Page " counter(currentPage) " of "; 
    }
    #pageNumbers div:after { 
      content: counter(pageTotal); 
    }
    @page {
      margin: 120px 25px 200px 25px;
    }
    header {
      position: fixed;
      top: -60px;
      left: 0px;
      right: 0px;
      height: 50px;
    }

    footer {
      position: fixed;
      bottom: -195px;
      left: 0px;
      right: 0px;
      height: 215px;
    }
  </style>
</head>
<body>
  <header>
    <table style="width: 100%; font-family: courier;">
      <thead>
        <tr>
          <th colspan="7" style="font-size: 30px; font-weight: bold; text-align: center; border-top:2px solid black; padding-bottom: 10px; padding-top: 15px;">PURCHASE ORDER</th>
        </tr>
      </thead>
    </table>
    <table style="width: 100%; font-family: courier; font-size:9px; border-collapse: collapse; margin-bottom: 10px" id="tableHead1">
      <tbody>
        <tr>
          <td style="width:11%">Order Code</td>
          <td>{{ $purchase_orders[0]->order_no != "" ? $purchase_orders[0]->order_no != null ? $purchase_orders[0]->order_no : '-' : '-' }}</td>
          <td style="width:13%">Revision No.</td>
          <td>{{ $purchase_orders[0]->rev_no != "" ? $purchase_orders[0]->rev_no != null ? $purchase_orders[0]->rev_no : '-' : '-' }}</td>
          <td style="border-top:0; border-bottom: 0;" width="5px">&nbsp;</td>
          <td style="width:9%">Doc No.</td>
          <td></td>
        </tr>
        <tr>
          <td>Order Date</td>
          <td>{{ $purchase_orders[0]->order_date != "" ? $purchase_orders[0]->order_date != null ? date('d.m.Y', strtotime($purchase_orders[0]->order_date)) : '00.00.0000' : '00.00.0000' }}</td>
          <td>Revision Date</td>
          <td>{{ $purchase_orders[0]->rev_date != "" ? $purchase_orders[0]->rev_date != null ? date('d.m.Y', strtotime($purchase_orders[0]->rev_date)) : '00.00.0000' : '00.00.0000' }}</td>
          <td style="border-top:0; border-bottom: 0;" width="5px">&nbsp;</td>
          <td style="width:9%">Page</td>
          <td>
            <div id="pageCounter">
              <?php $pages = 0; $total_amount = 0; ?>
              @foreach($purchase_orders as $purchase_order)
              <?php $pages++; ?>
              <?php $total_amount += $purchase_order->amount; ?>
              @endforeach
              <?php 
              $x = ceil($pages/12);
              for( $i = 0; $i < $x; $i++){
                echo "<span></span>";
              }
              ?>
            </div>
            <div class="page-number"></div>
          </td>
        </tr>
        <tr>
          <td style="width:11%">Buyer</td>
          <td colspan="3">{{ $purchase_orders[0]->pgr != "" ? $purchase_orders[0]->pgr != null ? $purchase_orders[0]->pgr . ' ' . $purchase_orders[0]->pgr_name : '-' : '-' }}</td>
          <td colspan="3" style="border-top:0; border-bottom: 0; border-right: 0;"></td>
        </tr>
      </tbody>
    </table>

    <table style="width: 49.5%; font-family: courier; font-size:9px; border-collapse: collapse; padding-bottom: 10px; float: left; border: 1px solid black;">
      <tbody>
        <tr>
          <td>Vendor :</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td style="padding-left: 5px">{{ $purchase_orders[0]->vendor != "" ? $purchase_orders[0]->vendor != null ? $purchase_orders[0]->vendor : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="padding-left: 5px">{{ $purchase_orders[0]->name != "" ? $purchase_orders[0]->name != null ? $purchase_orders[0]->name : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="padding-left: 5px; height: 30px;" valign="top">{{ $purchase_orders[0]->street != "" ? $purchase_orders[0]->street != null ? $purchase_orders[0]->street : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="text-align: right; padding-right: 100px;">{{ $purchase_orders[0]->city != "" ? $purchase_orders[0]->city != null ? $purchase_orders[0]->city : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="text-align: right; padding-right: 100px;">{{ $purchase_orders[0]->postl_code != "" ? $purchase_orders[0]->postl_code != null ? $purchase_orders[0]->postl_code : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="padding-left: 5px">{{ $purchase_orders[0]->cty != "" ? $purchase_orders[0]->cty != null ? $purchase_orders[0]->cty : '-' : '-' }}</td>
        </tr>
      </tbody>
    </table>

    <table style="width: 49.5%; font-family: courier; font-size:9px; border-collapse: collapse; padding-bottom: 10px; float: right;">
      <tbody>
        <tr>
          <td colspan="2">Shipped to :</td>
        </tr>
        <tr>
          <td colspan="2">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
        </tr>
        <tr>
          <td colspan="2">JL. Rembang Industri I/36, Kawasan Industri PIER</td>
        </tr>
        <tr>
          <td colspan="2">Pasuruan-Jawa Timur</td>
        </tr>
        <tr>
          <td style="text-align: center; width: 25%;">Indonesia</td>
          <td style="text-align: right; width: 25%;">NPWP : 01.824.283.4-052.000</td>
        </tr>
        <tr>
          <td colspan="2"><hr style="border: 1px solid black; margin: 0; margin-bottom: 2px;">Invoice to :</td>
        </tr>
        <tr>
          <td colspan="2">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
        </tr>
        <tr>
          <td colspan="2">JL. Rembang Industri I/36, Kawasan Industri PIER</td>
        </tr>
        <tr>
          <td colspan="2">Pasuruan-Jawa Timur</td>
        </tr>
        <tr>
          <td style="text-align: center; width: 50%;">Indonesia</td>
          <td style="text-align: right; width: 50%;">&nbsp;</td>
        </tr>
      </tbody>
    </table>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

    <table style="width: 100%; font-family: courier; font-size:9px; border-collapse: collapse; padding-bottom: 10px; padding-top: 5px;" id="tableHead2">
      <tbody>
        <tr>
          <td style="width:12%; border-top: 1px solid black;">Confirmed to</td>
          <td style="border-top: 1px solid black;">: {{ $purchase_orders[0]->salesperson != '' ? $purchase_orders[0]->salesperson != null ? $purchase_orders[0]->salesperson : '-' : '-' }}</td>
          <td style="width:12%; border-top: 1px solid black;">Phone No.</td>
          <td style="border-top: 1px solid black;">: {{ $purchase_orders[0]->telephone != '' ? $purchase_orders[0]->telephone != null ? $purchase_orders[0]->telephone : '-' : '-' }}</td>
          <td style="width:7.5%; border-top: 1px solid black;">Fax No.</td>
          <td style="border-top: 1px solid black;">: {{ $purchase_orders[0]->fax_no != '' ? $purchase_orders[0]->fax_no != null ? $purchase_orders[0]->fax_no : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="width:12%; border-top: 1px solid black;">Transportation</td>
          <td style="border-top: 1px solid black;">: {{ $purchase_orders[0]->sc_name != '' ? $purchase_orders[0]->sc_name != null ? $purchase_orders[0]->sc_name : '-' : '-' }}</td>
          <td style="width:12%; border-top: 1px solid black;">Delivery terms</td>
          <td style="border-top: 1px solid black;">: {{ $purchase_orders[0]->incot != '' ? $purchase_orders[0]->incot != null ? $purchase_orders[0]->incot : '-' : '-' }}</td>
          <td style="width:7.5%; border-top: 1px solid black;">Currency</td>
          <td style="border-top: 1px solid black;">: {{ $purchase_orders[0]->curr != '' ? $purchase_orders[0]->curr != null ? $purchase_orders[0]->curr : '-' : '-' }}</td>
        </tr>
        <tr>
          <td style="width:12%; border-top: 1px solid black; border-bottom: 1px solid black;">Payment terms</td>
          <td colspan="5" style="border-top: 1px solid black; border-bottom: 1px solid black;">: {{ $purchase_orders[0]->tpay_name != '' ? $purchase_orders[0]->tpay_name != null ? $purchase_orders[0]->tpay_name : '-' : '-' }}</td>
        </tr>
      </tbody>
    </table>
  </header>

  <footer>
    <table style="width: 100%; font-family: courier; font-size:9px; border-collapse: collapse; padding-bottom: 10px;">
      <tbody>
        <tr>
          <td colspan="3" style="text-align: right; font-size: 14px; border-top: 1px dashed black;">
            {{-- Total Amount : {{ number_format($total_amount, 2) }} --}}
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
          </td>
        </tr>
        <tr>
          <td style="border-top: 1px solid black; font-size: 14px; width: 25%; text-align: center;">Manager</td>
          <td>&nbsp;</td>
          <td style="border-top: 1px solid black; font-size: 14px; width: 25%; text-align: center;">Vendor Confirmation</td>
        </tr>
      </tbody>
    </table>
  </footer>

  <main>
    <table style="width: 100%; font-family: courier; font-size:9px; border-collapse: collapse; padding-bottom: 10px;">
      <thead>
        <tr>
          <th colspan="10">
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
          </th>
        </tr>
        <tr>
          <th colspan="10" style="text-align: left; font-size: 14px; border-bottom: 1px solid black;">
            Order Details
          </th>
        </tr>
        <tr>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">No.</th>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Order No.</th>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Item</th>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Material Code<br>Tracking No.</th>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Description<br>Work Order No.</th>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Delivery Date</th>
          <th style="text-align: right; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Quantity</th>
          <th style="text-align: left; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">UM</th>
          <th style="text-align: right; font-weight: normal; border-bottom: 1px solid black; border-right: 1px solid black;">Unit Price</th>
          <th style="text-align: right; font-weight: normal; border-bottom: 1px solid black;">Amount</th>
        </tr> 
      </thead>
      <tbody id="tableBody1">
        <?php $no = 1; ?>
        @foreach($purchase_orders as $purchase_order)
        <tr>
          <td style="width: 3%">{{ $no }}</td>
          <td style="width: 8%">{{ (int)$purchase_order->purchdoc }}</td>
          <td style="width: 5%">{{ nl2br(str_replace(',', ' ', $purchase_order->item)) }}</td>
          <td style="width: 10%">{{ $purchase_order->material }}</td>
          <td style="width: 32%">{{ trim($purchase_order->description, '"') }}</td>
          <td style="width: 10%">{{ date('d.m.Y', strtotime($purchase_order->deliv_date)) }}</td>
          <td style="text-align: right; width: 8%;">{{ number_format($purchase_order->order_qty, 2) }}</td>
          <td style="width: 3%">{{ $purchase_order->base_unit_of_measure }}</td>
          <td style="text-align: right; width: 11%;">{{ number_format($purchase_order->price, 5) }}</td>
          <td style="text-align: right; width: 10%;">{{ number_format($purchase_order->amount, 2) }}</td>
        </tr>
        <?php $no++; ?>
        @endforeach
      </tbody>
    </table>
  </main>

  <div style="position: absolute; bottom: -10px;">
    <table style="width: 100%; font-family: courier; font-size:9px; border-collapse: collapse; padding-bottom: 10px;">
      <tbody>
        <tr>
          <td colspan="3" style="text-align: right; font-size: 14px;">
            Total Amount : {{ number_format($total_amount, 2) }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</body>
</html>