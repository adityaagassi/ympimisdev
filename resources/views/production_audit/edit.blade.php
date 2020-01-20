@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-ckeditor-full">
  <h1>
    Edit {{ $activity_name }}
    <small>it all starts here</small>
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
      {{-- <h3 class="box-title">Create New User</h3> --}}
    </div>  
    <form role="form" method="post" action="{{url('index/production_audit/update/'.$id.'/'.$production_audit->id.'/'.$product.'/'.$proses)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Product<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="product" id="product" class="form-control" value="{{ $product }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="proses" id="proses" class="form-control" value="{{ $proses }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right" id="form_point_check">
            <label class="col-sm-4">Point Check<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="point_check" style="width: 100%;" data-placeholder="Choose a Point Check..." required id="point_check">
                <option value=""></option>
                @foreach($pointcheck as $pointcheck)
                  @if($production_audit->point_check_audit->point_check == $pointcheck->point_check)
                    <option value="<?php echo $pointcheck->id ?>" selected><?php echo $pointcheck->point_check ?> - <?php echo $pointcheck->cara_cek ?></option>
                  @else
                    <option value="<?php echo $pointcheck->id ?>"><?php echo $pointcheck->point_check ?> - <?php echo $pointcheck->cara_cek ?></option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Date<span class="text-red">*</span></label>
            <div class="col-sm-8">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="date" name="date" value="{{ $production_audit->date }}">
              </div>
            </div>
          </div>
          {{-- <div class="form-group row" align="right">
            <label class="col-sm-4">Point Pengecekan<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor1" class="form-control" style="height: 250px;" name="point_check" readonly>{{ $production_audit->point_check_audit->point_check }}</textarea>
            </div>
          </div> --}}
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          {{-- <div class="form-group row" align="right">
            <label class="col-sm-4">Cara Cek<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor2" class="form-control" style="height: 250px;" name="cara_cek">{{ $production_audit->cara_cek }}</textarea>
            </div>
          </div> --}}
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foto Kondis Aktual<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <img width="100px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}">
              <input type="file" name="file" id="inputFile" class="form-control" value="" onchange="readURL(this);">
              <input type="hidden" name="foto_kondisi_aktual" id="inputFoto_kondisi_aktual" class="form-control" value="{{ $production_audit->foto_kondisi_aktual }}">
              <br>
              <img width="200px" id="blah" src="" style="display: none" alt="your image" />
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kondisi<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              {{-- <select class="form-control select2" name="kondisi" style="width: 100%;" data-placeholder="Choose a Kondisi..." required>
                <option value=""></option>
                <option value="Good" @if($production_audit->kondisi == "Good") selected @endif>Good</option>
                <option value="Not Good" @if($production_audit->kondisi == "Not Good") selected @endif>Not Good</option>
              </select> --}}
              <div class="radio">
                <label><input type="radio" name="kondisi" value="Good" @if($production_audit->kondisi == "Good") checked @endif>OK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="kondisi" value="Not Good" @if($production_audit->kondisi == "Not Good") checked @endif>NG</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">PIC<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="pic" style="width: 100%;" data-placeholder="Choose a PIC..." required>
                <option value=""></option>
                @foreach($pic as $pic)
                  @if($pic->employee_id == $production_audit->pic)
                    <option value="{{ $pic->employee_id }}" selected>{{ $pic->employee_id }} - {{ $pic->name }}</option>
                  @else
                    <option value="{{ $pic->employee_id }}">{{ $pic->employee_id }} - {{ $pic->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Auditor<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              {{-- <select class="form-control select2" name="auditor" style="width: 100%;" data-placeholder="Choose a Auditor..." required>
                <option value=""></option>
                @foreach($leaderForeman2 as $leaderForeman2)
                  @if($leaderForeman2->employee_id == $production_audit->auditor)
                    <option value="{{ $leaderForeman2->employee_id }}" selected>{{ $leaderForeman2->employee_id }} - {{ $leaderForeman2->name }}</option>
                  @else
                    <option value="{{ $leaderForeman2->employee_id }}">{{ $leaderForeman2->employee_id }} - {{ $leaderForeman2->name }}</option>
                  @endif
                @endforeach
              </select> --}}
              <input type="text" name="auditor" id="auditor_contoh" class="form-control" value="{{ $empid_leader }} - {{ $leader }}" readonly>
              <input type="hidden" name="auditor" id="auditor" class="form-control" value="{{ $empid_leader }}">
            </div>
          </div>
        </div>
          <div class="col-sm-4 col-sm-offset-5">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/production_audit/index/'.$id.'/'.$product.'/'.$proses) }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="submit" class="btn btn-primary col-sm-14">Update</button>
            </div>
          </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script type="text/javascript">
        // $("#form_point_check").hide();
        $("#proses").change(function(){
          // $("#form_point_check").show();
            $.ajax({
                url: "{{ route('admin.cities.get_by_country') }}?proses=" + $(this).val()+"&product="+ $("#product").val(),
                method: 'GET',
                success: function(data) {
                    $('#point_check').html(data.html);
                }
            });
        });
    </script>
  <script>
    $(function () {
      $('.select2').select2()
    });
    $('#date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      todayHighlight: true
    });

    jQuery(document).ready(function() {
      $('#email').val('');
      $('#password').val('');
    });
    CKEDITOR.replace('editor1' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
    CKEDITOR.replace('editor2' ,{
        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
    });
  </script>
  <script language="JavaScript">
      function readURL(input) {
              if (input.files && input.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                    $('#blah').show();
                      $('#blah')
                          .attr('src', e.target.result);
                  };

                  reader.readAsDataURL(input.files[0]);
              }
          }
    </script>
  @stop

