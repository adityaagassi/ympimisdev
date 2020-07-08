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

#title {
    position: absolute;
    top: 10px;
    left: 980px;
    font-size: 1.5em;
    border: 1px solid black;
    border-radius: 5px;
    padding: 10px;
    background-color: rgba(255, 255, 0, 0.9);
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
     /*width: 720px; 
     height:500px;*/
     margin-right: auto;
     margin-left: auto; 
     /*border: solid 1px red; */
     font-size: 24px; 
     text-align: center; 
   }

   .square {
    opacity: 0.8;
  }

  .squarex {
    border-radius: 4px;
    overflow: auto;
    border: 1px solid white;
    font-size: 0.75em;
    width: 35px;
    letter-spacing: 1.1px;
  }

</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top:0px">
  <div class="row">
    <div class="col-xs-12" style="margin-top: 0px;">
      <div class="col-xs-12" style="padding: 0">
        <div class="box-body" style="padding: 0">
          <div class="col-md-6" style="padding: 0">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />

            <button class="btn btn-warning btn-lg" type="button" onclick="temp()" style="display: inline-block;"><span>Temperature</span></button>
            <button class="btn btn-warning btn-lg" type="button" onclick="hum()" style="display: inline-block;"><span>Humidity</span></button>

            <div id="parent" style="margin-top: 5px">
              <img src="{{ url("images/ympi.jpg") }}" width="1200px">

                  <div id="temperature">
                    
                    <div id="title" class="text-center">Temperature<br>Monitoring</div>

                    <div class="squarex text-center" style="position: absolute; top: 665px; left: 540px;">
                      <div id="temp_Tanpo" class="temperature" style="padding: 0px 4px;">1</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 550px; left: 560px;">
                      <div id="temp_3D"class="temperature"  style="padding: 0px 4px;">2</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 460px; left: 268px;">
                      <div id="temp_Assembly_Utara" class="temperature" style="padding: 0px 4px;">3</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 460px; left:358px;">
                      <div id="temp_Assembly_Selatan" class="temperature" style="padding: 0px 4px;">4</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 390px; left:242px;">
                      <div id="temp_Stock_Room" class="temperature" style="padding: 0px 4px;">5</div>
                    </div>  

                    <div class="squarex text-center" style="position: absolute; top: 472px; left:92px;">
                      <div id="temp_Clean_Room" class="temperature" style="padding: 0px 4px;">6</div>
                    </div>
                  </div>


                  
                  <div id="humidity">

                    <div id="title" class="text-center">Humidity<br>Monitoring</div>

                    <div class="squarex text-center" style="position: absolute; top: 665px; left: 540px;">
                      <div id="hum_Tanpo" class="humidity" style="padding: 0px 4px;">1</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 550px; left: 560px;">
                      <div id="hum_3D" class="humidity" style="padding: 0px 4px;">2</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 460px; left: 268px;">
                      <div id="hum_Assembly_Utara" class="humidity" style="padding: 0px 4px;">3</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 460px; left:358px;">
                      <div id="hum_Assembly_Selatan" class="humidity" style="padding: 0px 4px;">4</div>
                    </div>

                    <div class="squarex text-center" style="position: absolute; top: 390px; left:242px;">
                      <div id="hum_Stock_Room" class="humidity" style="padding: 0px 4px;">5</div>
                    </div>  

                    <div class="squarex text-center" style="position: absolute; top: 472px; left:92px;">
                      <div id="hum_Clean_Room" class="humidity" style="padding: 0px 4px;">6</div>
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
    <script src="{{ url("js/jquery.gritter.min.js") }}"></script>


    <script>
      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

      jQuery(document).ready(function() {
        fetch_data();
        setInterval(fetch_data, 30000);
        $('#humidity').hide();

      });  

      function temp(){
        $('#humidity').hide();
        $('#temperature').show();
      }

      function hum(){
        $('#humidity').show();
        $('#temperature').hide();
      }

      function fetch_data(){

        var data = {

        };

        $.get('{{ url("fetch/temperature/room_temperature") }}', data, function(result, status, xhr) {
          if(result.status){
            // console.log(result.lists);

            $.each(result.lists, function(key, value) {

              if(value.remark == "temperature"){

                $.each($(".temperature"), function(key2, value2) {

                  var loc = value.location.replace(" ","_")
                  
                  if (value2.id == "temp_"+loc) {

                    if (value.upper_limit == null && value.lower_limit == null) {
                        $("#"+value2.id).parent().addClass('bg-green-active');
                    }

                    else if (value.upper_limit != null && value.lower_limit != null) {
                        if (value.value > value.upper_limit || value.value < value.lower_limit ) {
                          $("#"+value2.id).parent().addClass('bg-red-active');
                        }
                        else{
                          $("#"+value2.id).parent().addClass('bg-green-active');
                        }
                    }

                    else if (value.upper_limit != null ) {
                      if (value.value > value.upper_limit ) {
                        $("#"+value2.id).parent().addClass('bg-red-active');
                      }
                      else{
                        $("#"+value2.id).parent().addClass('bg-green-active');
                      }
                    }

                    else if (value.lower_limit != null){
                      if (value.value > value.lower_limit ) {
                        $("#"+value2.id).parent().addClass('bg-green-active');
                      }
                      else{
                        $("#"+value2.id).parent().addClass('bg-red-active');
                      }
                    }


                    $('#'+value2.id).html(value.value);
                  }

                })        
              }

              if(value.remark == "humidity"){
                $.each($(".humidity"), function(key3, value3) {

                  var loc = value.location.replace(" ","_")
                  
                  if (value3.id == "hum_"+loc) {

                    if (value.upper_limit == null && value.lower_limit == null) {
                        $("#"+value3.id).parent().addClass('bg-green-active');
                    }

                    else if (value.upper_limit != null && value.lower_limit != null) {
                        if (value.value > value.upper_limit || value.value < value.lower_limit ) {
                          $("#"+value3.id).parent().addClass('bg-red-active');
                        }
                        else{
                          $("#"+value3.id).parent().addClass('bg-green-active');
                        }
                    }

                    else if (value.upper_limit != null ) {
                      if (value.value > value.upper_limit ) {
                        $("#"+value3.id).parent().addClass('bg-red-active');
                      }
                      else{
                        $("#"+value3.id).parent().addClass('bg-green-active');
                      }
                    }

                    else if (value.lower_limit != null){
                      if (value.value > value.lower_limit ) {
                        $("#"+value3.id).parent().addClass('bg-green-active');
                      }
                      else{
                        $("#"+value3.id).parent().addClass('bg-red-active');
                      }
                    }

                    $('#'+value3.id).html(value.value);
                  }
                }) 
              }
            });
          }
          else{
            alert('Attempt to retrieve data failed');
          }
        });
      }


    </script>



    @endsection