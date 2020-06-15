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
    Verifikasi {{ $page }}
    <small>Verifikasi Form</small>
  </h1>
  <ol class="breadcrumb">

   <!-- <a class="btn btn-warning btn-sm pull-right" data-toggle="tooltip" title="Lihat Report" href="{{url('index/form_ketidaksesuaian/print', $pr['id'])}}" target="_blank" style="margin-right: 5px;width: 150px">Preview Report Form</a> -->
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
              <!-- <th colspan="2" class="centera" >
                <center><img width="150px" src="{{ asset('images/logo_yamaha3.png') }}" alt="" style="vertical-align: middle !important"></center>
              </th> -->
              <th colspan="10" style="text-align: center; vertical-align: middle;font-size: 22px;font-weight: bold">Form Purchase Requisition</th>
              @if(($user == $pr->manager || Auth::user()->role_code == "MIS") && $pr->approvalm == null && $pr->posisi == "manager")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @elseif(($user == $pr->dgm || Auth::user()->role_code == "MIS") && $pr->approvaldgm == null && $pr->posisi == "dgm")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @elseif(($user == $pr->gm || Auth::user()->role_code == "MIS") && $pr->approvalgm == null && $pr->posisi == "gm")
              <th colspan="2" style="border: 1px solid black;vertical-align: middle;font-size: 20px"><center>Approval</center></th>
              @endif
            </tr>
          </thead>
          <tbody>
            <form role="form" method="post" action="{{url('purchase_requisition/approval/'.$pr->id)}}">
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Keterangan Umum</b></td>
              @if(($user == $pr->manager || Auth::user()->role_code == "MIS") && $pr->approvalm == null && $pr->posisi == "manager")
              <td colspan="2" rowspan="7" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @elseif(($user == $pr->dgm || Auth::user()->role_code == "MIS") && $pr->approvaldgm == null && $pr->posisi == "dgm")
              <td colspan="2" rowspan="7" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $pr->gm || Auth::user()->role_code == "MIS") && $pr->approvalgm == null && $pr->posisi == "gm")
              <td colspan="2" rowspan="7" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @endif
            </tr>
            <tr>
              <td colspan="2" style="border:none;width: 25%">PR Number</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?= $pr->no_pr ?> </b></td>
            </tr>
            <tr>
              <td colspan="2" style="border:none;width: 25%">Applied by</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?= $pr->emp_name ?> </b></td>
            </tr>
            <tr>
              <td colspan="2"style="border:none">Date Of Submission</td>
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none"><b><?= date('d F Y', strtotime($pr->submission_date)) ?></b></td>
            </tr>
            <tr>
              <td colspan="2" style="border:none">Department</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <td colspan="6" style="border:none"><b><?= $pr->department ?></b></td>
            </tr>

            <tr>
              <td colspan="2" style="border:none">Note</td>
              <td colspan="2" style="text-align: right;border:none">:</td> 
              <?php if ($pr->note != null){ ?>
                <td colspan="6" style="border:none"><b><?= $pr->note ?> </b></td>
              <?php } else { ?>
                <td colspan="6" style="border:none"> - </td>
              <?php } ?>
            </tr>

            <tr>
              <td colspan="2" style="border:none">Lampiran</td> 
              <td colspan="2" style="text-align: right;border:none">:</td>
              <td colspan="6" style="border:none">

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
            ?>
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Material / Item</b></td>
              @if(($user == $pr->manager || Auth::user()->role_code == "MIS") && $pr->approvalm == null && $pr->posisi == "manager")
              <td colspan="2" rowspan="{{ 2 + $jumlahitem }}" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @elseif(($user == $pr->dgm || Auth::user()->role_code == "MIS") && $pr->approvaldgm == null && $pr->posisi == "dgm")
              <td colspan="2" rowspan="{{ 2 + $jumlahitem }}" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $pr->gm || Auth::user()->role_code == "MIS") && $pr->approvalgm == null && $pr->posisi == "gm")
              <td colspan="2" rowspan="{{ 2 + $jumlahitem }}" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @endif
            </tr>
            <tr>
              <td colspan="4" style="border-top: 1px solid black">Item Code - Description</td>
              <td colspan="2" style="border-top: 1px solid black">Item Spesification</td>
              <td colspan="1" style="border-top: 1px solid black">Item Qty</td>
              <td colspan="1" style="border-top: 1px solid black">Item Price</td>
              <td colspan="2" style="border-top: 1px solid black">Total</td>
            </tr>       

            @foreach($items as $item)
            <tr>
              <?php if ($item->item_code != "") { ?>
              <td colspan="4"><b>{{$item->item_code}} - {{$item->item_desc}}</b></td>
              <?php } else { ?>
              <td colspan="4"><b>{{$item->item_desc}}</b></td>
              <?php } ?>
              <td colspan="2"><b>{{$item->item_spec}}</b></td>
              <td colspan="1"><b>{{$item->item_qty}} Pcs</b></td>
              <td colspan="1"><b>({{$item->item_currency}}) 
                <?php 
                if($item->item_currency == "USD") { echo "$"; } 
                else if($item->item_currency == "JPN") { echo "¥"; } 
                else if($item->item_currency == "ID") { echo "Rp."; } 
                ?> 
                <?= number_format($item->item_price,2,',','.') ?></b></td>
              <td colspan="2"><b>({{$item->item_currency}}) 
                <?php 
                if($item->item_currency == "USD") { echo "$"; } 
                else if($item->item_currency == "JPN") { echo "¥"; } 
                else if($item->item_currency == "ID") { echo "Rp."; } 
                ?> 
                <?= number_format($item->item_amount,2,',','.') ?></b></td>
            </tr>
            @endforeach
            
            <tr>
              <td colspan="10" style="font-size: 20px;border-top: 1px solid black;background-color: #eeeeee"><b>Budget</b></td>
              @if(($user == $pr->manager || Auth::user()->role_code == "MIS") && $pr->approvalm == null && $pr->posisi == "manager")
              <td colspan="2" rowspan="6" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @elseif(($user == $pr->dgm || Auth::user()->role_code == "MIS") && $pr->approvaldgm == null && $pr->posisi == "dgm")
              <td colspan="2" rowspan="6" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>
              </td>
              @elseif(($user == $pr->gm || Auth::user()->role_code == "MIS") && $pr->approvalgm == null && $pr->posisi == "gm")
              <td colspan="2" rowspan="6" style="border: 1px solid black;vertical-align: middle;">
                <center>
                  <label class="label label-success"  style="font-size: 1.4em">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="checkbox" class="minimal-red" name="approve[]" value="1">   Approve
                  </label>
                </center>         
              </td>
              @endif
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
              @if(($user == $pr->manager || Auth::user()->role_code == "MIS") && $pr->approvalm == null && $pr->posisi == "manager")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 10px">
                <center>
                  <button class="btn btn-success" type="submit" style="font-weight: bold;width: 100%">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Form PR</a>
                </center>
              </td>

              @elseif(($user == $pr->dgm || Auth::user()->role_code == "MIS") && $pr->approvaldgm == null && $pr->posisi == "dgm")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 10px">
                <center>
                  <button class="btn btn-success" type="submit" style="font-weight: bold;width: 100%">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Form</a>
                </center>
              </td>

              @elseif(($user == $pr->gm || Auth::user()->role_code == "MIS") && $pr->approvalgm == null && $pr->posisi == "gm")
              <td colspan="2" rowspan="3" style="border: 1px solid black;vertical-align: middle;padding: 10px">
                <center>
                  <button class="btn btn-success" type="submit" style="font-weight: bold;width: 100%">Approve & Send Email</button>
                  <br><br><a data-toggle="modal" data-target="#notapproved" class="btn btn-danger" href="" style="font-weight: bold; ">Reject Form</a>
                </center>
              </td>


              @endif
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

  <div class="modal modal-danger fade" id="notapproved" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('purchase_requisition/notapprove/'.$pr->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui form PR ini</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Form Akan Dikirim kembali ke Pembuat PR
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-outline">Not Approved</a>
          </div>
        </form>
      </div>
    </div>
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
      // $('body').toggleClass("sidebar-collapse");
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