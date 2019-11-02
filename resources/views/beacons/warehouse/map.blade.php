@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

  .morecontent span {
   display: none;
 }
 .morelink {
   display: block;
 }

 thead>tr>th{
   text-align:center;
   overflow:hidden;
   padding: 3px;
 }
 tbody>tr>td{
   text-align:center;
 }
 tfoot>tr>th{
   text-align:center;
 }
 th:hover {
   overflow: visible;
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
   border:1px solid black;
   vertical-align: middle;
   padding:0;
 }
 table.table-bordered > tfoot > tr > th{
   border:1px solid black;
   padding:0;
 }
 td{
   overflow:hidden;
   text-overflow: ellipsis;
 }
 .dataTable > thead > tr > th[class*="sort"]:after{
   content: "" !important;
 }
 #queueTable.dataTable {
   margin-top: 0px!important;
 }
 #loading, #error { display: none; }

 #parent { 
   position: relative; 
   width: 1210px; 
   height:800px; 
   border: solid 3px white; 
   font-size: 24px; 
   text-align: center; 
 }

 #fstk { 
   position: absolute; 
   right: 70px; 
   top: 132px; 
   width: 500px;
   height: 300px; 
   border: solid 3px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #DM { 
   position: absolute; 
   right: 320px; 
   top: 500px; 
   width: 150px;
   height: 150px; 
   border: solid 3px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #who { 
   position: absolute; 
   right: 640px; 
   top: 313px; 
   width: 100px;
   height: 100px; 
   border: solid 3px red; 
   font-size: 24px; 
   text-align: center; 
 }


 #inc { 
   position: absolute; 
   right: 640px; 
   top: 500px; 
   width: 100px;
   height: 100px; 
   border: solid 3px red; 
   font-size: 24px; 
   text-align: center; 
 }

 #ind { 
   position: absolute; 
   right: 750px; 
   top: 650px; 
   width: 25px;
   height: 25px; 
   border: solid 3px red; 
   font-size: 24px; 
   text-align: center; 
 }
 #fstk > div, 
 #DM > div,
 #who > div,
 #inc > div,
 #ind > div {
  border-radius: 50%;
}

.square {
  opacity: 0.8;
}


</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<div class="col-md-12" style="height: 1000px;">
							<h3>MAPPING VIEWER CUBEACON</h3>
							<h3>Lantai I</h3>
							<div id="parent">
								<center><img src="{{ url("images/maps_WH.jpg") }}" width="1200"></center>
								<div id="fstk" class="square"></div>
								<div id="DM" class="square"></div>
								<div id="who" class="square">

                </div>
                <div id="inc" class="square">

                </div>
                <div id="ind" class="square"></div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

  var arr_user = [];
  var stat = 0;

  jQuery(document).ready(function() {
    call_data_user();
    setInterval(call_data, 2000);
    // call_data();
  })

  function call_data_user() {
    $.get('{{ url("fetch/user/beacon") }}', function(result, status, xhr){
      if (result.status) {
        arr_user = result.data;
        stat = 1;
      }

    })
  }

  function call_data() {
    if (stat == 1) {
      $.ajax({
        url: 'http://172.17.128.87:82/reader/data',
        type: 'GET',
        data: {_token: CSRF_TOKEN},
        success: function (data) {
          var color = 'black';
          var address = '';
          var name = ''
          $.each(data, function(index, value){
            $.each(arr_user, function(index2, value2){
              if (value.major == value2.major && value.minor == value2.minor) {
                name = value2.kode;
              }
            })
            if (value.major == '111' && value.minor == '1903') {
              color = 'yellow';
              // name = 'YAA';
            } else if (value.major == '111' && value.minor == '1905') {
              color = 'green';
              // name = 'ABS';
            }

            address = value.major+"_"+value.minor;

            if (value.reader == '4c66d0') {
              $( "."+address ).remove();
              $("#who").append('<div style="background-color: '+color+';width: 25px; height: 25px; display:inline-block; font-size:12px; color:black" class="'+address+'">'+name+'</div>');
            } else if(value.reader == '4c67db') {
              $( "."+address ).remove();
              $("#inc").append('<div style="background-color: '+color+';width: 25px; height: 25px; display:inline-block; font-size:12px; color:black" class="'+address+'">'+name+'</div>');
            }
            name = ' ';
          })
        // console.log(data[0]);
      }
    });
    }
  }

</script>
@endsection