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
          border:1px solid rgb(211,211,211);
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
          Upload Sakurentsu <span class="text-purple"> {{ $title_jp }}</span>
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
                         <h3 class="box-title"><span class="text-purple">Sakurentsu</span> File</h3>
                    </div>
                    <div class="box-body">
                         <div class="row">
                              <div class="col-xs-12">
                                   <div class="form-group">
                                            <input type="hidden" value="{{csrf_token()}}" name="_token" />

                                            <div class="col-xs-12" style="padding: 0">
                                               <div class="form-group">
                                                  <label for="input">Sakuretsu Number</label>
                                                  <input type="text" name="sakurentsu_number" id="sakurentsu_number" placeholder="Sakurentsu Number" class="form-control" value="{{$sakurentsu->sakurentsu_number}}" readonly="">
                                               </div>

                                               <div class="form-group">
                                                  <label for="input">Applicant</label>
                                                  <input type="text" name="applicant" id="applicant" placeholder="Applicant" class="form-control" value="{{$sakurentsu->applicant}}" readonly="">
                                               </div>
                                             </div>

                                              <div class="col-xs-12" style="padding: 0">
                                                <?php if ($sakurentsu->file != null){ ?>
                                                  <div class="box box-warning box-solid">
                                                    <div class="box-header with-border">
                                                      <h3 class="box-title">Uploaded File</h3>

                                                      <div class="box-tools pull-right">
                                                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                        </button>
                                                      </div>
                                                      <!-- /.box-tools -->
                                                    </div>
                                                    <!-- /.box-header -->
                                                    <div class="box-body">
                                                      <?php $data = json_decode($sakurentsu->file);
                                                        for ($i = 0; $i < count($data); $i++) { ?>
                                                          <div class="col-md-12" style="padding: 5px">
                                                            <div class="isi">
                                                              <?= $data[$i] ?>
                                                              <div class="pull-right">
                                                                <a href="{{ url('/uploads/sakurentsu/'.$data[$i]) }}" target="_blank" class="btn btn-warning"><i class="fa fa-file-pdf-o"></i> Download / Preview</a>                                              
                                                              </div>
                                                            </div>
                                                          </div>
                                                        <?php } ?>    
                                                        </div>                   
                                                    </div>

                                                  <?php } ?>
                                              </div> 
                                            <div>
                                       </div>
                                   </div>
                              </div>
                              
                         </div>
                    </div>
               </div>
          </div>
        <div class="col-xs-6" style="padding-right: 0">
             <div class="box box-solid">
                  <div class="box-header">
                       <h3 class="box-title">Upload File<span class="text-purple"> Sakurentsu</span> (Translated)</h3>
                  </div>
                  <div class="box-body">
                       <div class="row">
                            <div class="col-xs-12">
                                 <div class="form-group">

                                      <?php if ($sakurentsu->file_translate == null) { ?>

                                      <form action="/" enctype="multipart/form-data" method="POST">

                                           <input type="hidden" value="{{csrf_token()}}" name="_token" />

                                           <div class="form-group">
                                              <label for="input">Judul Kasus</label>
                                              <input type="text" name="judul" id="judul" placeholder="Masukkan Perihal Sakurentsu" class="form-control" value="">
                                           </div>

                                           <div class="form-group">
                                              <label for="input">Translator</label>
                                              <input type="text" name="translator" id="translator" placeholder="Translator" class="form-control" value="{{$employee->name}}" readonly="">
                                           </div>

                                         <div class="dropzone" id="my-dropzone" name="mainFileUploader">
                                             <div class="fallback">
                                                 <input name="file" type="file" multiple />
                                             </div>
                                         </div>
                                     </form>



                                     <div class="col-xs-12" style="padding: 0">
                                         <!-- <button type="submit" id="submit-all" class="btn btn-success pull-right" style="margin-top: 10px" onclick="location.reload()">Upload</button> -->
                                         <button type="submit" id="submit-all" class="btn btn-success pull-right" style="margin-top: 10px;width: 100%">Upload</button>
                                     </div>

                                     <?php } else { ?>

                                      <div class="col-xs-12" style="padding: 0">
                                         <div class="form-group">
                                            <label for="input">Sakuretsu Number</label>
                                            <input type="text" name="sakurentsu_number" id="sakurentsu_number" placeholder="Sakurentsu Number" class="form-control" value="{{$sakurentsu->sakurentsu_number}}" readonly="">
                                         </div>
                                         
                                         <div class="form-group">
                                              <label for="input">Translator</label>
                                              <input type="text" name="translator" id="translator" placeholder="Translator" class="form-control" value="{{$sakurentsu->translator}}" readonly="">
                                           </div>
                                       </div>

                                      <div class="col-xs-12" style="padding: 0">
                                        <?php if ($sakurentsu->file_translate != null){ ?>
                                          <div class="box box-success box-solid">
                                            <div class="box-header with-border">
                                              <h3 class="box-title">File Yang Telah Diupload</h3>

                                              <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                                </button>
                                              </div>
                                              <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                              <?php $data = json_decode($sakurentsu->file_translate);
                                                for ($i = 0; $i < count($data); $i++) { ?>
                                                  <div class="col-md-12" style="padding: 5px">
                                                    <div class="isi">
                                                      <?= $data[$i] ?>
                                                      <div class="pull-right">
                                                        <a href="{{ url('/uploads/sakurentsu/'.$data[$i]) }}" target="_blank" class="btn btn-success"><i class="fa fa-file-pdf-o"></i> Download / Preview</a>                                              
                                                      </div>
                                                    </div>
                                                  </div>
                                                <?php } ?>    
                                                </div>                   
                                            </div>

                                          <?php } ?>
                                      </div> 

                                     <?php } ?>
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

          // fetchTable();  

     });

     Dropzone.options.myDropzone = {
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },

          url: "{{ url('index/sakurentsu/upload_sakurentsu_translate')}}/{{$sakurentsu->id}}",
          autoProcessQueue: false,
          uploadMultiple: true,
          parallelUploads: 100,
          maxFiles: 100,
          // acceptedFiles: "image/*",

          init: function () {

                var submitButton = document.querySelector("#submit-all");
                var wrapperThis = this;

                submitButton.addEventListener("click", function () {
                    if (!confirm("Are you sure want to submit this translated File And Send to PC?")) {
                         return false;
                    } else {
                         wrapperThis.processQueue();
                         setTimeout(function(){ location.reload() }, 3000);
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
                    formData.append("translator", $("#translator").val());
                });
            }

        };

     var audio_error = new Audio('{{ url("sounds/error.mp3") }}');



     // function fetchTable(){

     //      var data = {
     //      }

     //     $.get('{{ url("fetch/sakurentsu") }}', data, function(result, status, xhr){
     //       if(xhr.status == 200){
     //         if(result.status){

     //           $('#sakurentsuTable').DataTable().clear();
     //           $('#sakurentsuTable').DataTable().destroy();

     //           $("#tableSakurentsu").find("td").remove();  
     //           $('#tableSakurentsu').html("");

     //           var table = "";


     //           $.each(result.datas, function(key, value) {

     //                var obj = JSON.parse(value.file);
     //                var app = "";
     //                for (var i = 0; i < obj.length; i++) {
     //                     app += "<a href='../../../uploads/sakurentsu/"+obj[i]+"' target='_blank'><i class='fa fa-file-pdf-o'></i> </a>";
     //                }

     //               table += '<tr>';
     //               table += '<td width="1%">'+value.applicant+'</td>';
     //               table += '<td width="1%">'+value.sakurentsu_number+'</td>';
     //               table += '<td width="1%">'+app+'</td>';
     //               if (value.status == "translate") {
     //                     table += '<td width="1%" style="background-color:yellow">Translating</td>';                    
     //               }else{
     //                     table += '<td width="1%" style="background-color:green">Finish Translating</td>';
     //               }

     //               table += '</tr>';
     //           })

     //           $('#tableSakurentsu').append(table);

     //                 var table = $('#sakurentsuTable').DataTable({
     //                   'responsive':true,
     //                   'paging': false,
     //                   'searching': true,
     //                   'ordering': false,
     //                   'order': [],
     //                   'info': true,
     //                   'autoWidth': true,
     //                   "sPaginationType": "full_numbers",
     //                   "bJQueryUI": true,
     //                   "bAutoWidth": false,
     //                   "processing": true
     //                 });
     //         }
     //         else{
     //            alert('Attempt to retrieve data failed');
     //          }
     //       }
     //     });
     //   }

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