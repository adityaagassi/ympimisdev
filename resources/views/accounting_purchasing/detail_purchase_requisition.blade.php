@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  input[type=checkbox] {
    transform: scale(1.25);
  }
  thead>tr>th{
    /*text-align:center;*/
    background-color: #7e5686;
    color: white;
    border: none;
    border:1px solid black;
    border-bottom: 1px solid black !important;
  }
  tbody>tr>td{
    /*text-align:center;*/
    border: 1px solid black;
  }
  tfoot>tr>th{
    /*text-align:center;*/
  }
  td:hover {
    overflow: visible;
  }
  table.table-hover > tbody > tr > td{
    border:1px solid #eeeeee;
  }

  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <small>Detail Form PR</small>
  </h1>
  <ol class="breadcrumb">
    <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('purchase_requisition/report', $pr['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report PDF PR</a>
 </ol>
</section>
@endsection
@section('content')
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">

        <?php $user = STRTOUPPER(Auth::user()->username)?>

        <table class="table" style="border: 1px solid black;">
            <thead>
            <tr>
              <th colspan="8" style="text-align: center; vertical-align: middle;font-size: 22px;">Form Purchase Requisition</th>
              <th colspan="2" style="text-align: center; vertical-align: middle;"><span style="font-size: 18px">No :</span><b style="font-size: 22px;"> <?= $pr->no_pr ?> </b></td>
            </tr>
          </thead>
          <tbody>
            <form role="form" method="post" action="{{url('purchase_requisition/approval/'.$pr->id)}}">
            <tr>
              <td colspan="1" style="border:none">Department</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b><?= $pr->department ?></b></td>
            </tr>
            <tr>
              <td colspan="1" style="border:none">Sub Section</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b><?= $pr->group ?></b></td>
            </tr>
            <tr>
              <td colspan="1"style="border:none">Date Of Submission</td>
              <td colspan="1" style="text-align: right;border:none">:</td>
              <td colspan="8" style="border:none"><b><?= date('d F Y', strtotime($pr->submission_date)) ?></b></td>
            </tr>

            <tr>
              <td colspan="1" style="border:none">Note</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b><?= $pr->note ?> </b></td>
            </tr>

            <tr>
              <td colspan="1" style="border:none">Lampiran</td> 
              <td colspan="1" style="text-align: right;border:none">:</td>
              <td colspan="8" style="border:none">

                <?php if ($pr->file != null){ ?>
                  <?php $data = json_decode($pr->file);
                    for ($i = 0; $i < count($data); $i++) { ?>
                      <a href="{{ url('/files/pr/'.$data[$i]) }}" class="fa fa-paperclip" target="_blank"> {{$data[$i]}}</a> &nbsp;
                  <?php } ?>
                <?php } else { ?>

                  -

                <?php } ?>
              </td>             
            </tr>
            <?php
              $jumlahitem = count($items);
              $noitem = 1;
            ?>
            
            <tr>
              <td colspan="1" style="border-top: 1px solid black">No</td>
              <td colspan="1" style="border-top: 1px solid black">Item Code</td>
              <td colspan="2" style="border-top: 1px solid black">Description</td>
              <td colspan="2" style="border-top: 1px solid black">Spesification</td>
              <td colspan="1" style="border-top: 1px solid black">Stock</td>
              <td colspan="1" style="border-top: 1px solid black">QTY</td>
              <td colspan="1" style="border-top: 1px solid black">Unit Price</td>
              <td colspan="1" style="border-top: 1px solid black">Total</td>
            </tr>       

            @foreach($items as $item)
            <tr>
              <td colspan="1"><b>{{ $noitem }}</b></td>
              <td colspan="1"><b>{{$item->item_code}}</b></td>
              <td colspan="2"><b>{{$item->item_desc}}</b></td>
              <td colspan="2"><b>{{$item->item_spec}}</b></td>
              <td colspan="1"><b>{{$item->item_stock}} {{$item->item_uom}}</b></td>
              <td colspan="1"><b>{{$item->item_qty}} {{$item->item_uom}}</b></td>
              <td colspan="1"><b>({{$item->item_currency}}) 
                <?php 
                if($item->item_currency == "USD") { echo "$"; } 
                else if($item->item_currency == "JPY") { echo "¥"; } 
                else if($item->item_currency == "IDR") { echo "Rp."; } 
                ?> 
                <?= number_format($item->item_price,2,',','.') ?></b></td>
              <td colspan="1"><b>({{$item->item_currency}}) 
                <?php 
                if($item->item_currency == "USD") { echo "$"; } 
                else if($item->item_currency == "JPY") { echo "¥"; } 
                else if($item->item_currency == "IDR") { echo "Rp."; } 
                ?> 
                <?= number_format($item->item_amount,2,',','.') ?></b></td>
            </tr>

            <?php $noitem++; ?>
            @endforeach
            
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Budget</b></td>  
            </tr>
            <tr>
              <td colspan="1" style="border:none">No Budget</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b><?= $pr->no_budget ?></b></td>
            </tr>
            <tr>
              <td colspan="1" style="border:none">Description</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b>{{ $pr->budget->description }}</b></td>
            </tr>
            <tr>
              <td colspan="1" style="border:none">Total Amount</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b>${{ $pr->budget->amount }}</b></td>
            </tr>
            <tr>
              <td colspan="1" style="border:none">Account Name</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b>{{ $pr->budget->account_name }}</b></td>
            </tr>
            <tr>
              <td colspan="1" style="border:none">Category</td>
              <td colspan="1" style="text-align: right;border:none">:</td> 
              <td colspan="8" style="border:none"><b>{{ $pr->budget->category }}</b></td>
            </tr>
            <tr>
              <td colspan="6" rowspan="3" style="border-top: 1px solid black">&nbsp;</td>
              <td style="border-top: 1px solid black">Applied By</td>
              <td style="border-top: 1px solid black">Acknowledge By</td>
              <td style="border-top: 1px solid black">Acknowledge By</td>
              <td style="border-top: 1px solid black">Approve By</td> 
              
            </tr>
            <tr>
              <td style="vertical-align: middle;">
                <?= $pr->emp_name ?>
              </td>
              </td>
              <td style="vertical-align: middle;">
                
                <?php if ($pr->approvalm != "") { ?>
                  <?= $pr->manager ?>
                <?php } ?>

              </td>
              <td style="vertical-align: middle;">

                <?php if ($pr->approvaldgm != "") { ?>
                  <?= $pr->dgm ?>
                <?php } ?>
               
              </td>
              <td style="vertical-align: middle;">
               
                <?php if ($pr->approvalgm != "") { ?>
                  <?= $pr->gm ?>
                <?php } ?>

              </td>
            </tr>
            <tr>
              <td>User</td>
              <td>Manager</td>
              <td>DGM</td>
              <td>GM</td>
            </tr>
          </tbody>
       </table>
      </div>
    </form>
  </div>

@endsection


@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
    $(document).ready(function() {

      $("body").on("click",".btn-danger",function(){ 
        $(this).parents(".control-group").remove();
      });
    });


    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function sendemail(id) {
      var data = {
        id: id,
      };

      if (!confirm("Apakah anda yakin ingin mengirim PR ini?")) {
        return false;
      }
    }

    function openSuccessGritter(title, message){
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '3000'
      });
    }

    function openErrorGritter(title, message) {
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-danger',
        image: '{{ url("images/image-stop.png") }}',
        sticky: false,
        time: '3000'
      });
    }


  </script>
  @stop