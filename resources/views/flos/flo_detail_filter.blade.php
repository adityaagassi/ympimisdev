@extends('layouts.master')
@section('stylesheets')
@stop

@section('header')
<section class="content-header">
  <h1>
    Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
    <small>Details <span class="text-purple">??????</span></small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li>
      <button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
        <i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
      </button>
    </li> --}}
  </ol>
</section>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">FLO Filters <span class="text-purple">----???</span></span></h3>
        </div>
        <form class="form-horizontal" role="form" method="post" action="{{url('filter/flo_detail')}}">
          <input type="hidden" value="{{csrf_token()}}" name="_token" />
          <div class="box-body">
            <div class="col-md-12 col-md-offset-3">
              <div class="col-md-3">
                <div class="form-group">
                  <label>From</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="datefrom" nama="datefrom">
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>To</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="dateto" nama="dateto">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 col-md-offset-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Origin Group</label>
                  <select class="form-control select2" data-placeholder="Select Origin Group" name="origin_group" id="origin_group" style="width: 100%;">
                    @foreach($flos as $flo)
                    <option value="{{ $flo->shipmentschedule->material->origin_group_code }}">{{ $flo->shipmentschedule->material->origingroup->origin_group_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>Material Number</label>
                  <select class="form-control select2" data-placeholder="Select Material Number" name="material_number" id="material_number" style="width: 100%;">
                    <option value=""></option>
                    @foreach($flos as $flo)
                    <option value="{{ $flo->shipmentschedule->material_number }}">{{ $flo->shipmentschedule->material->material_number }} - {{ $flo->shipmentschedule->material->material_description }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>FLO Number</label>
                  <select class="form-control select2" data-placeholder="Select FLO Number" name="flo_number" id="flo_number" style="width: 100%;">
                    <option value=""></option>
                    @foreach($flos as $flo)
                    <option value="{{ $flo->flo_number }}">{{ $flo->flo_number }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group pull-right">
                  <br>
                  <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</section>

@endsection


@section('scripts')

@endsection