@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor-full/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    Create {{ $activity_name }}
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
    <form role="form" method="post" action="{{url('index/first_product_audit/update/'.$id.'/'.$first_product_audit->id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Department<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="department" id="department" class="form-control" value="{{ $departments }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Sub Section<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="subsection" style="width: 100%;" data-placeholder="Choose a Sub Section..." required id="subsection">
                  <option value=""></option>
                  @foreach($subsection as $subsection)
                    @if($first_product_audit->subsection == $subsection->sub_section_name)
                      <option value="{{ $subsection->sub_section_name }}" selected>{{ $subsection->sub_section_name }}</option>
                    @else
                      <option value="{{ $subsection->sub_section_name }}">{{ $subsection->sub_section_name }}</option>
                    @endif
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses / Nama Mesin<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="proses" id="proses" class="form-control" required placeholder="Enter Proses" value="{{ $first_product_audit->proses }}">
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Jenis<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="jenis" id="jenis" class="form-control" required placeholder="Enter Jenis" value="{{ $first_product_audit->jenis }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Standar Kualitas<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="standar_kualitas" id="standar_kualitas" class="form-control" required placeholder="Enter Standar Kualitas" value="{{ $first_product_audit->standar_kualitas }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Tool Check<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="tool_check" id="tool_check" class="form-control" required placeholder="Enter Tool Check" value="{{ $first_product_audit->tool_check }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Jumlah Cek<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="number" name="jumlah_cek" id="jumlah_cek" class="form-control" required placeholder="Enter Jumlah Cek" value="{{ $first_product_audit->jumlah_cek }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Leader<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="leader" id="leader" class="form-control" value="{{ $leader }}" readonly>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Foreman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="foreman" id="foreman" class="form-control" value="{{ $foreman }}" readonly>
            </div>
          </div>
        </div>
          <div class="col-sm-4 col-sm-offset-5">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('index/first_product_audit/list_proses/'.$id) }}">Cancel</a>
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
        // $("#proses").change(function(){
          // $("#form_point_check").show();
          // console.log($(this).val());
          // console.log($("#product").val());
            $.ajax({
                url: "{{ route('admin.cities.get_by_country') }}?proses=" + $("#proses").val()+"&product="+ $("#product").val(),
                method: 'GET',
                success: function(data) {
                    $('#point_check').html(data.html);
                }
            });
        // });
    </script>
  <script>
    
    // var product = document.getElementById("product");
    // var proses = document.getElementById("proses");

    // var productselected= product.options[product.selectedIndex];
    // var prosesselected= proses.options[proses.selectedIndex];

    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('#email').val('');
      $('#password').val('');
    });
    CKEDITOR.replace('editor1' ,{
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
        function readURL2(input) {
              if (input.files && input.files[0]) {
                  var reader = new FileReader();

                  reader.onload = function (e) {
                    $('#blah2').show();
                      $('#blah2')
                          .attr('src', e.target.result);
                  };

                  reader.readAsDataURL(input.files[0]);
              }
          }
    </script>
  @stop

