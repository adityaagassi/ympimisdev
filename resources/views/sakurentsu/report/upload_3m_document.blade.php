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
  {{ $title }} <span class="text-purple"> {{ $title_jp }}</span>
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
     <h3 class="box-title">Detail 3M</h3>
   </div>
   <div class="box-body">
     <div class="row">
      <div class="col-xs-12">
       <div class="form-group">
        <div class="col-xs-12" style="padding: 0">

          <input type="hidden" value="{{csrf_token()}}" name="_token" />

          <div class="col-xs-4" style="padding: 1px">
            <div class="form-group">
              <label for="input">Sakuretsu Number</label>
              <input type="text" name="sakurentsu_number" id="sakurentsu_number" class="form-control" readonly="" value="{{ $tiga_m->sakurentsu_number }}">
            </div>
          </div>
          <div class="col-xs-12" style="padding: 1px">
            <div class="form-group">
              <label for="input">3M Title</label>
              <input type="text" name="three_m_title" id="three_m_title" class="form-control" readonly="" value="{{ $tiga_m->title }}">
            </div>
          </div>

          <div class="col-xs-12" style="padding: 1px">
            <div class="form-group">
              <label for="input">Product Name</label>
              <input type="text" name="prod_name" id="prod_name" class="form-control" readonly="" value="{{ $tiga_m->product_name }}">
            </div>
          </div>

          <div class="col-xs-12" style="padding: 1px">
            <div class="form-group">
              <label for="input">Proccess Name</label>
              <input type="text" name="proc_name" id="proc_name" class="form-control" readonly="" value="{{ $tiga_m->proccess_name }}">
            </div>
          </div>

          <div class="col-xs-12" style="padding: 1px">
            <div class="form-group">
              <label for="input">Unit</label>
              <input type="text" name="unit_name" id="unit_name" class="form-control" readonly="" value="{{ $tiga_m->unit }}">
            </div>
          </div>

          <div class="col-xs-12" style="padding: 1px">
            <div class="form-group">
              <label for="input">3M Category</label>
              <input type="text" name="ctg" id="ctg" class="form-control" readonly="" value="{{ $tiga_m->category }}">
            </div>
          </div>

          <div class="col-xs-12" style="padding: 1px">
            <div class="form-group">
              <label for="input">Related Department</label>
              <input type="text" name="department" id="department" class="form-control" readonly="" value="{{ $tiga_m->related_department }}">
            </div>
          </div>
        </div>
        <div>
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
     <h3 class="box-title">Upload Document</h3><br>
   </div>
   <div class="box-body">
    <form action="/" enctype="multipart/form-data" method="POST">
     <div class="col-xs-12" style="padding: 1px">
      <div class="form-group">
        <label for="input">Document Name</label>
        <input type="hidden" name="id" id="id" value="{{ $tiga_m->id }}">
        <select class="select2" style="width: 100%" data-placeholder="Select Document Name" name="doc_name" id="doc_name">
          <option value=""></option>
          @foreach($doc_tiga_m as $docs)
          <option value="{{ $docs->document_name }}">{{ $docs->document_name }}</option>
          @endforeach
        </select> 
        <input type="text" id="doc_note" class="form-control" disabled>
      </div>
    </div>

    <div class="col-xs-12" style="padding: 0">
      <div class="form-group">
        <label>Sakurentsu File</label>
        <div class="dropzone" id="my-dropzone" name="mainFileUploader">
         <div class="fallback">
           <input name="file" type="file" multiple />
         </div>
       </div>
     </div>
   </div>
 </form>
 
 <button class="btn btn-success btn-lg" id="submit-all" style="width: 100%"><i class="fa fa-upload"></i>&nbsp; UPLOAD</button>
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
  $('body').toggleClass("sidebar-collapse");

  $(".select2").select2();
});

 Dropzone.options.myDropzone = {
  headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 },

 url: "{{ url('upload/sakurentsu/3m/document/upload')}}",
 autoProcessQueue: false,
 uploadMultiple: true,
 parallelUploads: 100,
 maxFiles: 100,

 init: function () {

  var submitButton = document.querySelector("#submit-all");
  var wrapperThis = this;

  submitButton.addEventListener("click", function () {
    if (!confirm("Are you sure want to upload 3M document?")) {
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
    formData.append("id", $("#id").val());
    formData.append("doc_name", $("#doc_name").val());
  });
}

};

$('#doc_name').on('select2:select', function (e) {
  var arr_docs = <?php echo json_encode($doc_tiga_m); ?>;
  var data = $("#doc_name option:selected").val();

  $.each(arr_docs, function(index,value){
    if (value.document_name == data) {
      $("#doc_note").val(value.document_description);
    }
  })
});

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


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
