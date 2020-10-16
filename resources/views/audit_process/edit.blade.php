@extends('layouts.master')
@section('header')
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<section class="content-header">
  <h1>
    Edit Audit Proses
  </h1>
  <ol class="breadcrumb">
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
  <div class="box box-solid">
    <form role="form" method="post" action="{{url('index/audit_process/update/'.$id.'/'.$audit_process->id)}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <input type="hidden" name="department" id="department" class="form-control" value="{{ $audit_process->department }}" readonly>
          <input type="hidden" name="date" id="date" class="form-control" value="{{ $audit_process->date }}" readonly>
          <input type="hidden" name="section" id="section" class="form-control" value="{{ $audit_process->section }}" readonly>
          <input type="hidden" name="periode" id="periode" class="form-control" value="{{ $audit_process->periode }}" readonly>
          
          <div class="form-group row" align="right">
            <label class="col-sm-4">Product<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="product" style="width: 100%;" data-placeholder="Choose a Product..." required id="product">
                  <option value=""></option>
                  @foreach($product as $product)
                  @if($audit_process->product == $product)
                    <option value="{{ $product }}" selected>{{ $product }}</option>
                  @else
                    <option value="{{ $product }}">{{ $product }}</option>
                  @endif
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="proses" id="proses" class="form-control" required placeholder="Enter Proses" value="{{ $audit_process->proses }}">
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Operator<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="operator" style="width: 100%;" data-placeholder="Choose a Operator..." required id="operator">
                  <option value=""></option>
                  @foreach($operator as $operator)
                  @if($audit_process->operator == $operator->name)
                    <option value="{{ $operator->name }}" selected>{{ $operator->name }}</option>
                  @else
                    <option value="{{ $operator->name }}">{{ $operator->name }}</option>
                  @endif
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Auditor<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <select class="form-control select2" name="auditor" style="width: 100%;" data-placeholder="Choose a Auditor..." required id="auditor">
                  <option value=""></option>
                  @foreach($auditor as $auditor)
                  @if($audit_process->auditor == $auditor->name)
                    <option value="{{ $auditor->name }}" selected>{{ $auditor->name }}</option>
                  @else
                    <option value="{{ $auditor->name }}">{{ $auditor->name }}</option>
                  @endif
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Cara Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor1" class="form-control" style="height: 200px;" name="cara_proses">{{ $audit_process->cara_proses }}</textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kondisi Cara Proses<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="kondisi_cara_proses" @if($audit_process->kondisi_cara_proses == 'Good') checked @endif value="Good">OK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="kondisi_cara_proses" value="Not Good" @if($audit_process->kondisi_cara_proses == 'Not Good') checked @endif>NG</label>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
          <div class="form-group row" align="right">
            <label class="col-sm-4">Pemahaman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <textarea id="editor2" class="form-control" style="height: 200px;" name="pemahaman">{{ $audit_process->pemahaman }}</textarea>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Kondisi Pemahaman<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <div class="radio">
                <label><input type="radio" name="kondisi_pemahaman" @if($audit_process->kondisi_pemahaman == 'Good') checked @endif value="Good">OK</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="kondisi_pemahaman" @if($audit_process->kondisi_pemahaman == 'Not Good') checked @endif value="Not Good">NG</label>
              </div>
            </div>
          </div>
          <div class="form-group row" align="right">
            <label class="col-sm-4">Keterangan<span class="text-red">*</span></label>
            <div class="col-sm-8" align="left">
              <input type="text" name="keterangan" id="keterangan" class="form-control" required placeholder="Enter Keterangan" value="{{ $audit_process->keterangan }}">
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
              <a class="btn btn-danger" href="{{ url('index/audit_process/index/'.$id) }}">Cancel</a>
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
  <script>
    
    // var product = document.getElementById("product");
    // var proses = document.getElementById("proses");

    // var productselected= product.options[product.selectedIndex];
    // var prosesselected= proses.options[proses.selectedIndex];

    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
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
  @stop

