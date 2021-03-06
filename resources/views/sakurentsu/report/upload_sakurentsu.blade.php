@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<link href="{{ url("css/dropzone.min.css") }}" rel="stylesheet">
<link href="{{ url("css/basic.min.css") }}" rel="stylesheet">
<style type="text/css">
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
  border:1px solid green;
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
 <h1>
  Sakurentsu <span class="text-purple"> {{ $title_jp }}</span>
</h1>
<ol class="breadcrumb">
</ol>
</section>
@endsection

@section('content')
<section class="content">
 @if (session('success'))
 <div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
  {{ session('success') }}
</div>
@endif
@if (session('error'))
<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h4><i class="icon fa fa-ban"></i> Error!</h4>
  {{ session('error') }}
</div>
@endif

<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
  <p style="position: absolute; color: White; top: 45%; left: 35%;">
   <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
 </p>
</div>

<div class="row">
  <div class="col-xs-6" style="padding-right: 0">
   <div class="box box-solid">
    <div class="box-header">
      <h3 class="box-title">Upload File Sakurentsu <span class="text-purple">作連通ファイルアップロード</span></h3>
    </div>
    <div class="box-body">
     <div class="row">
      <div class="col-xs-12">
       <div class="form-group">

        <form action="/" enctype="multipart/form-data" method="POST" id="upload_form">
          <div class="col-xs-12" style="padding: 0">

            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <input type="hidden" id="applicant" name="applicant" class="form-control" value="{{$employee->name}}" readonly>
            
            <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="input">Sakuretsu Number <span class="text-purple">作連通番号</span></label>              
                <input type="text" name="sakurentsu_number" id="sakurentsu_number" placeholder="Input Sakurentsu Number or Reff Number" class="form-control">
              </div>
            </div>
            <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="input">Sakurentsu Title <span class="text-purple">作連通の表題</span></label>
                <input type="text" name="title_jp" id="title_jp" placeholder="Input title here" class="form-control">
              </div>
            </div>
            <div class="col-xs-12" style="padding: 1px">
              <div class="form-group">
                <label for="sakurentsu_category">Sakurentsu Category <span class="text-purple">作連通のカテゴリ</span></label><br>
                <select class="select2" name="sakurentsu_category" id="sakurentsu_category" data-placeholder="Select Category" style="width: 100%">
                  <option value=""></option>
                  <option value="3M">3M</option>
                  <option value="Trial">Trial Request 試作依頼</option>
                  <option value="Information">Information 情報</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col-xs-12" style="padding: 0">
            <div class="form-group">
              <label>Target Date <span class="text-purple">締切</span></label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" id="target_date" name="target_date" placeholder="Select Target Date">
              </div>
            </div>

            <div class="form-group">
              <label>Sakurentsu File <span class="text-purple">作連通ファイル</span></label>
              <div class="dropzone" id="my-dropzone" name="mainFileUploader">
               <div class="fallback">
                 <input name="file" type="file" multiple />
               </div>
             </div>
           </div>
         </div>
       </form>
       <div>
         <!-- <button type="submit" id="submit-all" class="btn btn-success pull-right" style="margin-top: 10px" onclick="location.reload()">Upload</button> -->
         <button type="submit" id="submit-all" class="btn btn-success pull-right" style="margin-top: 10px">Upload</button>
       </div>
     </div>
   </div>

 </div>
</div>
</div>
</div>
<div class="col-xs-6" style="padding: 0">
 <div class="col-xs-12">
  <div class="box box-solid">
    <div class="box-header">
     <h3 class="box-title">List Outstanding Sakurentsu <span class="text-purple">未解決作連通のリスト</span></h3><br>
   </div>
   <div class="box-body">
     <div class="col-xs-12">
      <table id="sakurentsuTable" class="table table-bordered" style="width: 100%">
        <thead style="background-color: rgba(126,86,134,.7);">
         <tr>
          <th width="1%">Applicant <br> 申請者</th>
          <th width="1%">Number <br> 作連通の番号</th>
          <th width="1%">Target Date <br> 締切</th>
          <th width="1%">File <br> ファイル</th>
          <th width="1%">Status <br> ステイタス</th>
        </tr>
      </thead>
      <tbody id="tableSakurentsu">
      </tbody>
    </table>
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
<script src="{{ url("js/dropzone.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
 $.ajaxSetup({
  headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});

 jQuery(document).ready(function() {

  fetchTable();  

  $('#target_date').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'yyyy-mm-dd',
    orientation: "bottom auto"
  });

  $(".select2").select2();

});

 Dropzone.options.myDropzone = {
  headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 },

 url: "{{ url('index/sakurentsu/upload_sakurentsu')}}",
 autoProcessQueue: false,
 uploadMultiple: true,
 parallelUploads: 100,
 maxFiles: 100,

 init: function () {

  var submitButton = document.querySelector("#submit-all");
  var wrapperThis = this;

  submitButton.addEventListener("click", function () {
    if (!confirm("Are you sure want to create this sakurentsu and send to interpreter?\n 作連通を作成し、通訳チームに転送しますか？")) {
     return false;
   } else {
     wrapperThis.processQueue();
     $("#loading").show();
     // setTimeout(function(){ location.reload() }, 4000);
   }
 });

  this.on("addedfile", function (file) {

    // Create the remove button
    var removeButton = Dropzone.createElement("<button class='btn btn-lg dark'>Remove File</button>");

    // Listen to the click event
    removeButton.addEventListener("click", function (e) {
      // Make sure the button click doesn't submit the form:
      e.preventDefault();
      e.stopPropagation();

      // Remove the file preview.
      wrapperThis.removeFile(file);
      // If you want to the delete the file on the server as well,
      // you can do the AJAX request here.
    });

    // Add the button to the file preview element.
    file.previewElement.appendChild(removeButton);
  });

  this.on('sendingmultiple', function (data, xhr, formData) {
    formData.append("sakurentsu_number", $("#sakurentsu_number").val());
    formData.append("title_jp", $("#title_jp").val());
    formData.append("sakurentsu_category", $("#sakurentsu_category").val());
    formData.append("applicant", $("#applicant").val());
    formData.append("target_date", $("#target_date").val());
  });

  this.on("complete", function(file) { 
    this.removeAllFiles(true);
  })
}, success: function(file, response) {
  $("#loading").hide();
  openSuccessGritter('Success', 'Sakurentsu has been uploaded & send to Interpreter');

  $( '#upload_form' ).each(function(){
    this.reset();
  });

  $("#sakurentsu_category").select2("val", "");

  fetchTable();
}

};

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


function fetchTable(){

  var data = {
  }

  $.get('{{ url("fetch/sakurentsu") }}', data, function(result, status, xhr){
   if(xhr.status == 200){
     if(result.status){

       $('#sakurentsuTable').DataTable().clear();
       $('#sakurentsuTable').DataTable().destroy();

       $("#tableSakurentsu").find("td").remove();  
       $('#tableSakurentsu').html("");

       var table = "";


       $.each(result.datas, function(key, value) {

        var obj = JSON.parse(value.file);
        var app = "";
        for (var i = 0; i < obj.length; i++) {
         app += "<a href='"+"{{ url('uploads/sakurentsu/original/') }}/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> </a>";
       }

       table += '<tr>';
       table += '<td width="1%">'+value.applicant+'</td>';
       table += '<td width="1%">'+value.sakurentsu_number+'</td>';
       table += '<td width="1%">'+value.target_date+'</td>';
       table += '<td width="1%">'+app+'</td>';
       if (value.status == "translate") {
         table += '<td width="1%" style="background-color:yellow">Translating</td>';                    
       }else{
         table += '<td width="1%" style="background-color:green;color:white">Finish Translating</td>';
       }

       table += '</tr>';
     })

       $('#tableSakurentsu').append(table);

       var table = $('#sakurentsuTable').DataTable({
         'responsive':true,
         'paging': false,
         'searching': true,
         'ordering': false,
         'order': [],
         'info': true,
         'autoWidth': true,
         "sPaginationType": "full_numbers",
         "bJQueryUI": true,
         "bAutoWidth": false,
         "processing": true
       });
     }
     else{
      alert('Attempt to retrieve data failed');
    }
  }
});
}

function openErrorGritter(title, message) {
  jQuery.gritter.add({
   title: title,
   text: message,
   class_name: 'growl-danger',
   image: '{{ url("images/image-stop.png") }}',
   sticky: false,
   time: '2000'
 });
}

function openSuccessGritter(title, message){
  jQuery.gritter.add({
   title: title,
   text: message,
   class_name: 'growl-success',
   image: '{{ url("images/image-screen.png") }}',
   sticky: false,
   time: '2000'
 });
}

</script>

@stop