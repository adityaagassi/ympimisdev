@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
  }

  input[type=number] {
    -moz-appearance:textfield; /* Firefox */
  }
  input[type="radio"] {
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Check & Verifikasi {{ $page }}
    <small>Form Investment</small>
  </h1>
  <ol class="breadcrumb">
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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
      </p>
  </div>
  
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">

        <?php $user = STRTOUPPER(Auth::user()->username)?>

        @if($invest->posisi == "acc_budget" && ($user == "PI0902001" || Auth::user()->role_code == "MIS") )

        <form role="form" id="myForm" method="post" action="{{url('investment/check_budget/'.$invest->id)}}" enctype="multipart/form-data">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />  
          <table class="table table-bordered">
            <tr id="show-att">
              <td colspan="7" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 60%" colspan="2" id="text_attach_1">
              </td>
              <td colspan="5" style="font-size: 16px;width: 40%;padding-left: 20px">
                <a style="margin-top: 20px" href="{{url('investment/detail/'.$invest->id)}}" class="btn btn-warning btn-md" target="_blank"><i class="fa fa-edit"></i> Edit Investment</a>
                <br><br>
                <b>Poin Verifikasi</b>
                <br><br>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">Approval YCJ<span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <select class="form-control select2" data-placeholder="Pilih" name="ycj_approval" id="ycj_approval" style="width: 100% height: 35px;" required>
                      <option value="">&nbsp;</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row" align="left">
                  <label class="col-sm-4">Upload Quotation</label>
                  <div class="col-sm-5">
                    <input type="file" id="attachment" name="attachment[]" multiple="">
                  </div>
                </div>

                <div class="col-sm-6" style="margin-top: 20px;padding: 0">
                    <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
                </div>

                <div class="col-sm-6" style="margin-top: 20px">
                  <a data-toggle="modal" data-target="#notapproved{{$invest->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px">Reject</a>
                    <!-- <button type="button" class="btn btn-danger col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Reject</button> -->
                </div>

              </td>

            </tr>
          </table>
        </form>

        @elseif($invest->posisi == "acc_pajak" && ($user == "PI9802001" || Auth::user()->role_code == "MIS"))
        <form role="form" id="myForm" method="post" action="{{url('investment/check_budget/'.$invest->id)}}" enctype="multipart/form-data">
          <input type="hidden" value="{{csrf_token()}}" name="_token" /> 
          <table class="table table-bordered">
            <tr id="show-att">
              <td colspan="7" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 60%" colspan="2" id="text_attach_1">
              </td>
              <td colspan="5" style="font-size: 16px;width: 40%;padding-left: 20px">
                <a style="margin-top: 20px" href="{{url('investment/detail/'.$invest->id)}}" class="btn btn-warning btn-md" target="_blank"><i class="fa fa-edit"></i> Edit Investment</a>
                <br><br>
                <b>Poin Verifikasi</b>
                <br><br>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">Company Name<span class="text-red">*</span></label>
                  <div class="col-sm-8">
                      <input type="text" class="form-control" id="company_name" name="company_name" value="{{$invest->supplier_code}} - {{$invest->supplier_name}}" readonly="">
                  </div>
                </div>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">PKP Status<span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <select class="form-control select2" data-placeholder="Pilih PKP" name="pkp" id="pkp" style="width: 100% height: 35px;" required>
                      <option value="">&nbsp;</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">NPWP<span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <select class="form-control select2" data-placeholder="Pilih NPWP" name="npwp" id="npwp" style="width: 100% height: 35px;" required>
                      <option value="">&nbsp;</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">Constructor Certificate<span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <select class="form-control select2" data-placeholder="Pilih Certificate" name="certificate" id="certificate" style="width: 100% height: 35px;" required>
                      <option value="">&nbsp;</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">Total</label>
                  <div class="col-sm-8">
                    <div class="input-group">
                      <input type="text" class="form-control" id="total" name="total" placeholder="Total (%)">
                      <span class="input-group-addon">%</span>
                    </div>
                  </div>
                </div>

                <div class="form-group row" align="left">
                  <label class="col-sm-4">Service</label>
                  <div class="col-sm-8">
                    <div class="input-group">
                      <input type="text" class="form-control" id="service" name="service" placeholder="Service (%)">
                      <span class="input-group-addon">%</span>
                    </div>
                  </div>
                </div>

                <hr>

                <div class="form-group row" align="left">
                  <label class="col-sm-12">VAT Item Detail</label>

                <?php $no = 1; ?>
                @foreach($invest_item as $item)
                  <div class="col-sm-6" style="margin-top: 10px">
                    {{$item->detail}}
                  </div>

                  <input type="hidden" name="id_item<?= $no ?>" id="id_item<?= $no ?>" value="<?= $item->id ?>">

                  <div class="col-sm-6" style="margin-top: 10px">
                    <select class="form-control select2" data-placeholder="VAT Status" name="vat_item<?= $no ?>" id="vat_item<?= $no ?>" style="width: 100% height: 35px;" required>
                      <option value="No">No</option>
                      <option value="Yes">Yes</option>
                    </select>
                  </div>


                  <?php $no++; ?>
                @endforeach

                <input type="hidden" id="jumlahitem" name="jumlahitem" value="{{$no}}">

                </div>

                <div class="col-sm-6" style="margin-top: 20px;padding: 0">
                    <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
                </div>

                <div class="col-sm-6" style="margin-top: 20px"> 
                    <a data-toggle="modal" data-target="#notapproved{{$invest->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px">Reject</a>
                    <!-- <button type="button" class="btn btn-danger col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Reject</button> -->
                </div>



              </td>

            </tr>
          </table>
        </form>

        @else

        <table class="table table-bordered">
          <tr id="show-att">
            <td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" colspan="2" id="text_attach_1">
            </td>
          </tr>
        </table>

        @endif

      </div>
    </form>
  </div>

  <div class="modal modal-danger fade" id="notapproved{{$invest->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('investment/reject_acc/'.$invest->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui Investment ini</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Investment Akan Dikirim kembali lagi ke Applicant
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

      $('body').toggleClass("sidebar-collapse");

      var showAtt = "{{$invest->pdf}}";
      var path = "{{$file_path}}";

      // console.log(path);
      
      if(showAtt.includes('.pdf')){
        $('#text_attach_1').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
      }

      if(showAtt.includes('.png') || showAtt.includes('.PNG')){
        $('#text_attach_1').append("<embed src='"+ path +"' width='100%' height='800px'>");
      }

      if(showAtt.includes('.jp') || showAtt.includes('.JP')){
        $('#text_attach_1').append("<embed src='"+ path +"' width='100%' height='800px'>");
      }
    });

    function loading(){
      $("#loading").show();
    }


    document.getElementById("myForm").addEventListener("submit", loading);

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    CKEDITOR.replace('alasan' ,{
      filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

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

    $('.select2').select2();
  </script>
  @stop