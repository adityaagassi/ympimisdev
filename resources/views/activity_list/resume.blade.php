@extends('layouts.master')
@section('stylesheets')
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
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Resume of {{ $page }}s {{ strtoupper($dept_name) }}
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="{{ url("index/activity_list/create")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li> --}}
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
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-xs-12">
            <div class="box-header">
              <h3 class="box-title">Tasks of {{ $leader_dept }} on {{ $frequency_dept }}<span class="text-purple"></span></h3>
            </div>
            <form role="form" method="post" action="{{url('index/activity_list/resume_filter/'.$id)}}">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="col-md-12 col-md-offset-3">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Frequency</label>
                  <select class="form-control select2" name="frequency" style="width: 100%;" data-placeholder="Choose a Frequency...">
                    <option value=""></option>
                    @foreach($frequency as $frequency)
                      <option value="{{ $frequency }}">{{ $frequency }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Leader</label>
                  <select class="form-control select2" name="leader" style="width: 100%;" data-placeholder="Choose a Leader...">
                    <option value=""></option>
                    @foreach($leader as $leader)
                      <option value="{{ $leader->name }}">{{ $leader->employee_id }} - {{ $leader->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-12 col-md-offset-4">
              <div class="col-md-3">
                <div class="form-group pull-right">
                  <button onclick="window.close();" class="btn btn-warning">Back</button>
                  <a href="{{ url('index/activity_list/resume/'.$id) }}" class="btn btn-danger">Clear</a>
                  <button type="submit" class="btn btn-primary col-sm-14">Search</button>
                </div>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <table id="example1" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th >Activity Name</th>
                <th>D</th>
                <th>W</th>
                <th>M</th>
                <th>C</th>
                <?php $jumlah_td = 5 ?>
                @foreach($leader2 as $leader)
                  <th >{{ $leader->name }}</th>
                  <?php $jumlah_td = $jumlah_td + 1 ?>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($activity_list as $activity_list)
                <tr>
                  <td>{{ $activity_list->activity_name }}</td>
                  @if($activity_list->frequency == "Daily")
                    <td style="background-color: #75b3ff"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  @elseif($activity_list->frequency == "Weekly")
                    <td></td>
                    <td style="background-color: #75b3ff"></td>
                    <td></td>
                    <td></td>
                  @elseif($activity_list->frequency == "Monthly")
                    <td></td>
                    <td></td>
                    <td style="background-color: #75b3ff"></td>
                    <td></td>
                  @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="background-color: #75b3ff"></td>
                  @endif
                  @foreach($leader3 as $leader)
                    @if($leader->name == $activity_list->leader_dept)
                      <td style="background-color: #4aff77">
                        Plan Time : <?php 
                          $timesplit=explode(':',$activity_list->plan_time);
                          $min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
                        {{$min.' Min'}} <br>
                        Plan Item : {{ $activity_list->plan_item }}</td>
                    @else
                      <td></td>
                    @endif
                  @endforeach
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <?php for($i = 0;$i<$jumlah_td;$i++){ ?>
                <th></th>
                <?php } ?>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  </section>

  <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
        </div>
        <div class="modal-body">
          Are you sure delete?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>

  @stop

  @section('scripts')
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
      $('.select2').select2();
    });
    jQuery(document).ready(function() {
      $('#example1 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );
      var table = $('#example1').DataTable({
        "order": [],
        'dom': 'Bfrtip',
        'responsive': true,
        'lengthMenu': [
        [ 10, 25, 50, -1 ],
        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        'buttons': {
          buttons:[
          {
            extend: 'pageLength',
            className: 'btn btn-default',
          },
          {
            extend: 'copy',
            className: 'btn btn-success',
            text: '<i class="fa fa-copy"></i> Copy',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'excel',
            className: 'btn btn-info',
            text: '<i class="fa fa-file-excel-o"></i> Excel',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          {
            extend: 'print',
            className: 'btn btn-warning',
            text: '<i class="fa fa-print"></i> Print',
            exportOptions: {
              columns: ':not(.notexport)'
            }
          },
          ]
        }
      });

      table.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#example1 tfoot tr').appendTo('#example1 thead');

    });
    $(function () {

      $('#example2').DataTable({
        'paging'      : true,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : true,
        'autoWidth'   : false
      })
    })
    function deleteConfirmation(url, name, id) {
      jQuery('.modal-body').text("Are you sure want to delete '" + name + "'?");
      jQuery('#modalDeleteButton').attr("href", url+'/'+id);
    }
  </script>

  @stop