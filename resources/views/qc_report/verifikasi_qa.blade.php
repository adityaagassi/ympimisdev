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
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
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
    {{ $page }}
    <small>Verifikasi Quality Assurance</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
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
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New CPAR</h3> --}}
    </div>

    <?php $user = STRTOUPPER(Auth::user()->username)?>

    @if(Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS" || $user == $cpars->staff || $user == $cpars->leader || $user == $cpars->chief || $user == $cpars->foreman || $user == $cpars->manager || Auth::user()->role_code == "QA" || Auth::user()->role_code == "QA-SPL")
    
    @if($user == $cpars->staff || $user == $cpars->leader)
    <form role="form" method="post" enctype='multipart/form-data' action="{{url('index/qc_report/close1', $cpars->id)}}">
    @elseif($user == $cpars->manager)
    <form role="form" method="post" enctype='multipart/form-data' action="{{url('index/qc_report/close2', $cpars->id)}}">
    @endif 

      <div class="box-body">

        <input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1" style="font-size: 14px">No CPAR<span class="text-red">* </span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="cpar_no" placeholder="Nasukkan Nomor CPAR" value="{{ $cpars->cpar_no }}" readonly="">
          </div>

          @if($cpars->posisi == "QA" && $user == $cpars->staff || $user == $cpars->leader)

          <a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain" target="_blank">CPAR Report</a>

          <a href="{{url('index/qc_car/print_car_new', $cars[0]->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank" >CAR Report</a>

          <a class="btn btn-md btn-primary" data-toggle="tooltip" title="Send Email Ke Chief / Foreman" onclick="sendemail({{ $cpars->id }})" style="margin-right: 5px">Send Email</a>

         @endif
        </div>

        @foreach($cars as $cars)

        <?php if ($cpars->file != null){ ?>
          <div class="box box-primary box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">File CPAR Yang Telah Diupload</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php $data = json_decode($cpars->file);
                for ($i = 0; $i < count($data); $i++) { ?>
                <div class="col-md-4">
                  <div class="isi">
                    <?= $data[$i] ?>
                  </div>
                </div>
                <div  class="col-md-2">
                    <a href="{{ url('/files/'.$data[$i]) }}" class="btn btn-primary">Download / Preview</a>
                </div>
              <?php } ?>                       
            </div>
          </div>    
        <?php } ?>

        <?php if ($cars->file != null){ ?>
          <div class="box box-success box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">File CAR Yang Telah Diupload</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php $data = json_decode($cars->file);
                for ($i = 0; $i < count($data); $i++) { ?>
                <div class="col-md-4">
                  <div class="isi">
                    <?= $data[$i] ?>
                  </div>
                </div>
                <div  class="col-md-2">
                    <a href="{{ url('/files/car/'.$data[$i]) }}" class="btn btn-primary">Download / Preview</a>
                </div>
              <?php } ?>                       
            </div>
          </div>    
        <?php } ?>
          @if($cpars->posisi == "QA" && $user == $cpars->staff || $user == $cpars->leader || Auth::user()->role_code == "MIS")
 
            @if(count($verifikasi) == 0)
            <div class="form-group row" align="left">
              <label class="col-sm-12 text-center">
                <span class="col-sm-12 text-center" style="font-size: 20px;background-color: #ddd;padding-right:10px;padding-left: 10px">
                  Verifikasi
                </span>
              </label>
              <div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;">
                <div class="col-xs-1" style="padding: 0px;">
                  <label style="font-weight: bold; font-size: 18px;">
                    <span>Tanggal</span>
                  </label>
                </div>
                
                <div class="col-xs-2">
                    <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s') ?>" disabled>
                    <input type="hidden" class="form-control" id="tanggal1" name="tanggal1" value="<?= date('Y-m-d H:i:s') ?>"  >
                </div>
               
              </div>

              <div class="col-xs-12">
                <div class="col-xs-1" style="padding: 0px;">
                  <label style="font-weight: bold; font-size: 18px;">
                    <span>Status</span>
                  </label>
                </div>

                <div class="col-xs-2">
                    <select class="form-control select2" id="status1" name="status1" style="width: 100%;" data-placeholder="Pilih Status">
                      <option value="Close">Close</option>
                      <option value="Open">Open</option>
                    </select>
                </div>
              </div>

              <div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;">
                <div class="col-xs-2" style="padding: 0px;">
                  <label style="font-weight: bold; font-size: 18px;">
                    <span>Verification Result</span>
                  </label>
                </div>
                <div class="col-xs-12" style="padding: 0px;">
                  <textarea type="text" class="form-control" name="verifikasi1" id="verifikasi1"></textarea>
                </div>
              </div>

              <div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;">

              <div class="col-xs-1" style="padding: 0px;">
                  <a class="btn btn-success" onclick='addVeri();'><i class='fa fa-plus' ></i> Add Verifikasi</a>
                  <input type="hidden" id="jumlahVerif" name="jumlahVerif" value="1">
                </div>
              </div>

              <div id='verif'></div>
              
              <!-- <div class="col-xs-12" style="margin-top: 3%; margin-bottom: 1%;">
                <div class="col-xs-4" style="padding: 0px;">
                  <label style="font-weight: bold; font-size: 18px;">
                    <span><i class="fa fa-photo"></i> Verifikasi</span>
                  </label>
                </div>
                <div class="col-xs-1" style="padding: 0px;">
                  <a class="btn btn-success" onclick='addVerifikasi();'><i class='fa fa-plus' ></i></a>
                  <input type="hidden" id="jumlahVerif" name="jumlahVerif">
                </div>
              </div>
              <div id='verif'></div> -->
            </div>

            @elseif(count($verifikasi) > 0)

            <div class="form-group row" align="left">
            <?php for ($i = 0; $i < count($verifikasi); $i++) { ?>
                  <label class="col-sm-12 text-center">
                    <span class="col-sm-12 text-center" style="font-size: 20px;background-color: #ddd;padding-right:10px;padding-left: 10px">
                      Verifikasi
                    </span>
                  </label>
                  <div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;">
                    <div class="col-xs-1" style="padding: 0px;">
                      <label style="font-weight: bold; font-size: 18px;">
                        <span>Tanggal</span>
                      </label>
                    </div>
                    
                    <div class="col-xs-2">
                        <input type="text" class="form-control" id="tanggal<?= $i+1 ?>" name="tanggal<?= $i+1 ?>" value="<?= $verifikasi[$i]->tanggal ?>" readonly="">
                    </div>
                   
                  </div>

                  <div class="col-xs-12">
                    <div class="col-xs-1" style="padding: 0px;">
                      <label style="font-weight: bold; font-size: 18px;">
                        <span>Status</span>
                      </label>
                    </div>

                    <div class="col-xs-2">
                        <select class="form-control select2" id="status<?= $i+1 ?>" name="status<?= $i+1 ?>" style="width: 100%;" data-placeholder="Pilih Status">
                          @if($verifikasi[$i]->status == "Close")
                          <option value="Close"  selected="">Close</option>
                          <option value="Open">Open</option>
                          @elseif($verifikasi[$i]->status == "Open")
                          <option value="Close">Close</option>
                          <option value="Open" selected="">Open</option>
                          @endif
                        </select>
                    </div>
                  </div>

                  <div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;">
                    <div class="col-xs-2" style="padding: 0px;">
                      <label style="font-weight: bold; font-size: 18px;">
                        <span>Verification Result</span>
                      </label>
                    </div>
                    <div class="col-xs-12" style="padding: 0px;">
                      <textarea type="text" class="form-control" name="verifikasi<?= $i+1 ?>" id="verifikasi<?= $i+1 ?>"><?= $verifikasi[$i]->keterangan ?></textarea>
                    </div>
                  </div>
          
              
              <?php } ?>
                <div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;">

                <div class="col-xs-1" style="padding: 0px;">
                    <a class="btn btn-success" onclick='addVeri();'><i class='fa fa-plus' ></i> Add Verifikasi</a>
                    <input type="hidden" id="jumlahVerif" name="jumlahVerif" value="<?= count($verifikasi) ?>">
                  </div>
                </div>

                <div id='verif'></div>
            </div>
            @endif

            

            <div class="form-group row" align="left">
              <label class="col-sm-12 text-center">
                <span class="col-sm-12" style="font-size: 20px;background-color: #ddd;padding-right:10px;padding-left: 10px">
                  Cost Estimation (optional)
                </span>
              </label>
              <div class="col-sm-12">
                <textarea type="text" class="form-control" name="cost">{{$cpars->cost}}</textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-success col-md-14" style="width: 100%">Simpan</button>
          @endif

          @if($cpars->posisi == "QA2" && $user == $cpars->chief || $user == $cpars->foreman)

          <table class="table table-striped">
            <tr>
              <td style="font-size: 20px">
                Laporan CPAR
              </td>
              <td>
                <a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain" target="_blank">CPAR Report</a>
              </td>
            </tr>
            <tr>
              <td style="font-size: 20px">
                Laporan CAR
              </td>
              <td>
                <a href="{{url('index/qc_car/print_car_new', $cars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank" >CAR Report</a>
              </td>
            </tr>
          </table>
          <br><br>
          <div class="col-sm-12">
            <div class="col-sm-3"></div>
            <a class="btn btn-primary col-sm-12" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cpars->id }})"  style="width: 50%; font-weight: bold; font-size: 20px;margin-top: 10px;">Verifikasi & Send Email Manager</a>
          </div>
          <div class="col-sm-12">
            <div class="col-sm-3"></div>
            <a data-toggle="modal" data-target="#notapproved{{$cpars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 50%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject </a> 
          </div>

          @endif

          @if($cpars->posisi == "QAmanager" && $user == $cpars->manager)
          <table class="table table-striped">
            <tr>
              <td style="font-size: 20px">
                Laporan CPAR
              </td>
              <td>
                <a href="{{url('index/qc_report/print_cpar', $cpars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" title="Lihat Komplain" target="_blank">CPAR Report</a>
              </td>
            </tr>
            <tr>
              <td style="font-size: 20px">
                Laporan CAR
              </td>
              <td>
                <a href="{{url('index/qc_car/print_car_new', $cars->id)}}" data-toggle="tooltip" class="btn btn-warning btn-md" target="_blank" >CAR Report</a>
              </td>
            </tr>
          </table>
          <br><br>
          <div class="col-sm-12">
            <button type="submit" class="btn btn-success col-sm-12"  style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px;float: center">CLOSE CPAR {{$cpars->cpar_no}} 
            </button> 
          </div>
          <div class="col-sm-12">
            <div class="col-sm-3"></div>
            <a data-toggle="modal" data-target="#notapproved{{$cpars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject </a> 
          </div>
          @endif

        <!-- <table class="table table-striped table-bordered " style="border: 1px solid #f4f4f4">
          <thead>
            <tr style="background-color: #ff9800;border: none">
              <td width="75" style="text-align: center;border: none">Nomor</td>
              <td width="600" style="text-align: center;border: none">CAR</td>
              <td width="400">Upload Foto untuk Verifikasi</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td style="text-align: left">Deskripsi : <br><?= $cars->deskripsi ?></td>
              <td style="vertical-align: middle;">
              </td>
            </tr>

            <tr>
              <td>2</td>
              <td style="text-align: left">Immediately Action : <br><?= $cars->tindakan ?></td>
              <td style="vertical-align: middle;">
                <input type='file' onchange="readURL(this);" />
                <img id="blah" src="#" alt=" " />
              </td>
            </tr>

            <tr>
              <td>3</td>
              <td style="text-align: left">Possibilty Cause : <br><?= $cars->penyebab ?></td>
              <td style="vertical-align: middle;">
                <input type='file' onchange="readURL(this);" />
                <img id="blah" src="#" alt=" " />
              </td>
            </tr>

            <tr>
              <td>4</td>
              <td style="text-align: left">Corrective Action : <br><?= $cars->perbaikan ?></td>
              <td style="vertical-align: middle;">
                <input type='file' onchange="readURL(this);" />
                <img id="blah" src="#" alt=" " />
              </td>
            </tr>

          </tbody>
        </table> -->
        @endforeach
      </div>
    </form>
    @endif
  </div>

   <div class="modal modal-danger fade" id="notapproved{{$cpars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('index/qc_report/uncheckedqa/'.$cpars->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui verifikasi ini</h4>
                <textarea class="form-control" required="" name="alasan"></textarea> 
                *Verifikasi yang tidak disetujui akan dikirim ke staff/leader QA
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
  <script>
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(function () {
      $('.select2').select2()
    })

    CKEDITOR.replace('cost' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('verifikasi1' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('verifikasi2' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    CKEDITOR.replace('verifikasi3' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah')
                    .attr('src', e.target.result)
                    .width(200)
                    .height(200);
            };

            reader.readAsDataURL(input.files[0]);
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

    function sendemail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_report/emailverification/$cpars->id") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

    var ver = 1;
    var jumlahVerif = document.getElementById('jumlahVerif').value;

    function addVeri() {
      ++jumlahVerif;

      $add = '<label class="col-sm-12 text-center"> <span class="col-sm-12 text-center" style="font-size: 20px;background-color: #ddd;padding-right:10px;padding-left: 10px"> Verifikasi '+ jumlahVerif +' </span></label><div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;"> <div class="col-xs-1" style="padding: 0px;"> <label style="font-weight: bold; font-size: 18px;"> <span>Tanggal</span> </label> </div><div class="col-xs-2"> <input type="text" class="form-control" id="tanggal'+ jumlahVerif +'" name="tanggal'+ jumlahVerif +'" value="<?= date('Y-m-d H:i:s') ?>"> </div></div><div class="col-xs-12"> <div class="col-xs-1" style="padding: 0px;"> <label style="font-weight: bold; font-size: 18px;"> <span>Status</span> </label> </div><div class="col-xs-2"> <select class="form-control select2" name="status'+ jumlahVerif +'" style="width: 100%;" data-placeholder="Pilih Status"> <option value="Close">Close</option> <option value="Open">Open</option> </select> </div></div><div class="col-xs-12" style="margin-top: 1%; margin-bottom: 1%;"> <div class="col-xs-2" style="padding: 0px;"> <label style="font-weight: bold; font-size: 18px;"> <span>Verification Result</span> </label> </div><div class="col-xs-12" style="padding: 0px;"> <textarea type="text" class="form-control" name="verifikasi'+ jumlahVerif +'"></textarea> </div></div>';

      $('#verif').append($add);

      $('#jumlahVerif').val(jumlahVerif);
      ver++;

      CKEDITOR.replace('verifikasi'+ jumlahVerif +'' ,{
          filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
      });

    }

    // function addVerifikasi() {
    //   ++jumlahVerif;  
    //   $add = '<div class="col-xs-12" id="add_ver_'+ jumlahVerif +'"> <div class="col-xs-3" style="color: black; padding: 0px; padding-right: 1%;"> <input type="file" id="gambar_'+ jumlahVerif +'" name="gambar_'+ jumlahVerif +'" data-placeholder="Upload File" style="width: 100%; height: 33px; font-size: 15px; text-align: center;"> </div>    <div class="col-xs-1" style="color: black; padding: 0px; padding-right: 1%;">Keterangan</div><div class="col-xs-4" style="color: black; padding: 0px; padding-right: 1%;"> <div class="form-group"> <input type="text" id="ket_'+ jumlahVerif +'" name="ket_'+ jumlahVerif +'" data-placeholder="Keterangan" style="width: 100%; height: 33px; font-size: 15px; text-align: center;" class="form-control"> </div></div><div class="col-xs-1" style="padding: 0px;"> <a class="btn btn-danger" onclick="remove('+jumlahVerif+')"><i class="fa fa-close"></i></a> </div></div>';

    //   $('#verif').append($add);

    //   $('#jumlahVerif').val(jumlahVerif);
    //   ver++;
    // }

    function remove(id) {
      
      jumlahVerif--;
      $('#jumlahVerif').val(jumlahVerif);
      $("#add_ver_"+id).remove();

      if(ver != id){
        for (var i = id; i < ver; i++) {
          document.getElementById("add_ver_"+ (i+1)).id = "add_ver_"+ i;
          document.getElementById("gambar_"+ (i+1)).id = "gambar_"+ i;
          document.getElementById("ket"+ (i+1)).id = "ket"+ i;
        }   
      }
      ver--;
    }

    function hapus(id){
        var data = {
          id : id,
        }
        $.post('{{ url("index/qc_report/deleteVerifikasi") }}', data, function(result, status, xhr){
          if(result.status){
            openSuccessGritter('Success Hapus Verifikasi', result.message);
            location.reload();
          }
          else{
            openErrorGritter('Error!', result.message);
          }
        });
      }

  </script>
@stop








