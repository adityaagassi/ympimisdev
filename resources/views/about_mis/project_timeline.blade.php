@extends('layouts.master')
@section('stylesheets')
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
     <h1>
          Project Timeline<span class="text-purple"> ???</span>
     </h1>
</section>
@stop
@section('content')
<section class="content">
     <div class="row">
          <div class="col-xs-12">
               <ul class="timeline">
                    <li class="time-label">
                         <span class="bg-olive">
                              03 Jan. 2019
                         </span>
                    </li>
                    <li>
                         <i class="fa fa-user-plus bg-green"></i>
                         <div class="timeline-item">
                              <h3 class="timeline-header" style="color: green; font-weight: bold;">MIS New Member</h3>
                              <div class="timeline-body">
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/C99020314.jpg" height="100px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/R14122906.jpg" height="100px" style="margin-bottom: 2px;"></span>
                              </div>
                         </div>
                    </li>
                    <li class="time-label">
                         <span class="bg-blue">
                              01 Nov. 2018
                         </span>
                    </li>
                    <li>
                         <i class="fa fa-check bg-blue"></i>
                         <div class="timeline-item">
                              <h3 class="timeline-header" style="color: #0073b7; font-weight: bold;">CDM Digital Check Material Dimension</h3>
                              <div class="timeline-body">
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo1.jpg" height="150px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo2.jpg" height="150px" style="margin-bottom: 2px;"></span>
                              </div>
                         </div>
                    </li>
                    <li class="time-label">
                         <span class="bg-blue">
                              30 Aug. 2018
                         </span>
                    </li>
                    <li>
                         <i class="fa fa-check bg-blue"></i>
                         <div class="timeline-item">
                              <h3 class="timeline-header" style="color: #0073b7; font-weight: bold;">XIBO Digital Sign Board</h3>
                              <div class="timeline-body">
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo1.jpg" height="150px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo2.jpg" height="150px" style="margin-bottom: 2px;"></span>
                              </div>
                         </div>
                    </li>
                    <li class="time-label">
                         <span class="bg-olive">
                              01 Aug. 2018
                         </span>
                    </li>
                    <li>
                         <i class="fa fa-user-plus bg-green"></i>
                         <div class="timeline-item">
                              <h3 class="timeline-header" style="color: green; font-weight: bold;">Management Information System Departement</h3>
                              <div class="timeline-body">
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/C99020314.jpg" height="100px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/R14122906.jpg" height="100px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/E01030740.jpg" height="100px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/M09061339.jpg" height="100px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/J06021069.jpg" height="100px" style="margin-bottom: 2px;"></span>
                              </div>
                         </div>
                    </li>
                    <li class="time-label">
                         <span class="bg-blue">
                              01 Mar. 2018
                         </span>
                    </li>
                    <li>
                         <i class="fa fa-check bg-blue"></i>
                         <div class="timeline-item">
                              <h3 class="timeline-header" style="color: #0073b7; font-weight: bold;">OEE Overall Equipment Efficiency</h3>
                              <div class="timeline-body">
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo1.jpg" height="150px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo2.jpg" height="150px" style="margin-bottom: 2px;"></span>
                              </div>
                         </div>
                    </li>
                    <li class="time-label">
                         <span class="bg-blue">
                              15 Aug. 2017
                         </span>
                    </li>
                    <li>
                         <i class="fa fa-check bg-blue"></i>
                         <div class="timeline-item">
                              <h3 class="timeline-header" style="color: #0073b7; font-weight: bold;">KITTO Kanban Information Transaction and Turnover</h3>
                              <div class="timeline-body">
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo1.jpg" height="150px" style="margin-bottom: 2px;"></span>
                                   <span class="logo-mini"><img src="http://172.17.128.87/miraidev/public/dist/img/xibo2.jpg" height="150px" style="margin-bottom: 2px;"></span>
                              </div>
                         </div>
                    </li>
                    <li>
                         <i class="fa fa-dot-circle-o bg-gray"></i>
                    </li>
               </ul>
          </div>
     </div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     jQuery(document).ready(function() {

     });

</script>
@endsection