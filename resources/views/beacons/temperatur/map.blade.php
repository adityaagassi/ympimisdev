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
 margin-left: auto;
 margin-right: auto; 
 border: solid 0px red; 
 font-size: 24px; 
 text-align: center; 
}

#server { 
 position: absolute; 
 right: 594px; 
 top: 582px; 
 width: 70px;
 height: 90px; 
 border: solid 0px red; 
 font-size: 24px; 
 text-align: center; 
}

#office { 
 position: absolute; 
 right: 596px; 
 top: 152px; 
 width: 450px;
 height: 400px; 
 border: solid 0px red; 
 font-size: 24px; 
 text-align: center; 
}

#filling { 
 position: absolute; 
 right: 755px; 
 top: 8px; 
 width: 175px;
 height: 148px; 
 border: solid 0px red; 
 font-size: 24px; 
 text-align: center; 
}

#utility { 
 position: absolute; 
 right: 675px; 
 top: 8px; 
 width: 78px;
 height: 148px; 
 border: solid 0px red; 
 font-size: 24px; 
 text-align: center; 
}


#toilet { 
 position: absolute; 
 right: 450px; 
 top: 8px; 
 width: 220px;
 height: 148px; 
 border: solid 0px red; 
 font-size: 24px; 
 text-align: center; 
}

#tr1 { 
 position: absolute; 
 right: 640px; 
 top: 610px; 
 width: 400px;
 height: 118px; 
 border: solid 3px red; 
 font-size: 24px; 
 text-align: center; 
}

a{
  color: white;
}


  .merah {
    /*width: 50px;
    height: 50px;*/
    -webkit-animation: merah 1s infinite;  /* Safari 4+ */
    -moz-animation: merah 1s infinite;  /* Fx 5+ */
    -o-animation: merah 1s infinite;  /* Opera 12+ */
    animation: merah 1s infinite;  /* IE 10+, Fx 29+ */
  }

  @-webkit-keyframes merah {
    0%, 49% {
      background: rgba(0, 0, 0, 0);
    }
    50%, 100% {
      background-color: rgb(189, 30, 19);
    }
  }



  .biru {
    /*width: 50px;
    height: 50px;*/
    -webkit-animation: biru 1s infinite;  /* Safari 4+ */
    -moz-animation: biru 1s infinite;  /* Fx 5+ */
    -o-animation: biru 1s infinite;  /* Opera 12+ */
    animation: biru 1s infinite;  /* IE 10+, Fx 29+ */
  }

  @-webkit-keyframes biru {
    0%, 49% {
      background: rgba(0, 0, 0, 0);
    }
    50%, 100% {
      background-color: rgb(14, 198, 240);
    }
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
						<div class="col-md-12" style="height: 900px">
							<h2 style="text-align: center;">Temperature Map (温度分布)</h2>
							<!-- <h3>温度分布</h3> -->
							<div id="parent">
                <center><img src="{{ url("images/maps_office.png") }}" width="900"></center>
                <div id="server" class="square">
                  <tr>
                      <!-- <div>Suhu Server</div> -->
                      <a href="{{ url("index/grafikServer") }}">
                        <div id="suhuServer" style=" width:47px;height:30px"></div> 
                      </a>       
                  </tr> 

                </div>


								<div id="office" class="square">
                  <tr>
                    <!-- <div>Suhu office</div> -->
                    <a href="{{ url("index/grafikOffice") }}">
                      <div id="suhuOffice" style=" width:47px;height:30px"></div>
                    </a>
                  </tr>
                </div>
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
<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    maps(); //function
    setInterval(maps, 6000);
  });

  function maps() {
    $.get('{{ url("index/log_map_office") }}', function(result, status, xhr) { 
          if(xhr.status == 200){
            if(result.status){
              console.log(result.datas);
              
              $.each(result.datas, function(key, value) {

                if (value.lokasi == "Office") {
                  $('#suhuOffice').html(parseFloat(value.temperature)+'°C');
                  // console.log(value.temperature);
                  if (parseFloat(value.temperature) > value.upper_limit ) {
                    $('#suhuOffice').css('background-color','red');
                  }
                  else if (parseFloat(value.temperature) < value.lower_limit){
                   $('#suhuOffice').css('background-color','blue'); 
                  }
                  else {
                    
                    $('#suhuOffice').css('background-color','green'); 
                  }
                }

                // else if (value.lokasi == "Server") {
                //   $('#suhuServer').html(parseFloat(value.temperature)+'°C');
                //   // console.log(value.temperature);
                //   if (parseFloat(value.temperature) > value.upper_limit ) {
                //     $('#suhuServer').css('background-color','red');
                //   }
                //   else if (parseFloat(value.temperature) < value.lower_limit){
                //     $('#suhuServer').css('background-color','blue'); 
                //  }
                //  else {
                //   $('#suhuServer').css('background-color','olive');
                // }


                // }

              })
            }
          }
        });

    $.get('{{ url("index/log_map_server") }}', function(result, status, xhr) { 
          if(xhr.status == 200){
            if(result.status){
              console.log(result.datas);
              
              $.each(result.datas, function(key, value) {

                if (value.lokasi == "Server") {
                  $('#suhuServer').html(parseFloat(value.temperature)+'°C');
                  
                  if (parseFloat(value.temperature) > value.upper_limit ) {
                    $('#suhuServer').css('background-color','red');
                  }
                  else if (parseFloat(value.temperature) < value.lower_limit){
                   $('#suhuServer').css('background-color','blue'); 
                  }
                  else {
                    
                    $('#suhuServer').css('background-color','green'); 
                  }
                }

              })
            }
          }
        });

  }
</script>



@endsection