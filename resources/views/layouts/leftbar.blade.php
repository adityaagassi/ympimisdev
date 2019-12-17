  <aside class="main-sidebar">
  	<section class="sidebar">
  		<ul class="sidebar-menu" data-widget="tree">

  			@foreach(Auth::user()->role->permissions as $perm)
  			@php
  			$navs[] = $perm->navigation_code;
  			@endphp
  			@endforeach

  			@if(in_array('Dashboard', $navs))
  			@if(isset($page) && $page == "Dashboard")<li class="active">@else<li>@endif
  				<a href="{{ url("/home") }}"><i class="fa fa-industry"></i> <span>Dashboard</span></a>
  			</li>
  			@endif

        @if(isset($page) && $page == "About MIS")<li class="active">@else<li>@endif
          <a href="{{ url("/about_mis") }}"><i class="fa fa-info"></i> <span>About MIS</span></a>
        </li>

        @if(in_array('A0', $navs))
        <li class="header">Administration Menu</li>
        @endif

        @if(in_array('A1', $navs))
        @if(isset($page) && $page == "Batch Setting")<li class="active">@else<li>@endif
          <a href="{{ url("/index/batch_setting") }}"><i class="fa fa-clock-o"></i> <span>Batch Setting</span></a>
        </li>
        @endif

        @if(in_array('A2', $navs))
        @if(isset($page) && $page == "Code Generator")<li class="active">@else<li>@endif
          <a href="{{ url("/index/code_generator") }}"><i class="fa fa-barcode"></i> <span>Code Generator</span></a>
        </li>
        @endif

        @if(in_array('A7', $navs))
        @if(isset($page) && $page == "Daily Report")<li class="active">@else<li>@endif
          <a href="{{ url("/index/daily_report") }}"><i class="fa fa-file-code-o"></i> <span>Daily Report</span></a>
        </li>
        @endif

        @if(in_array('A3', $navs))
        @if(isset($page) && $page == "Navigation")<li class="active">@else<li>@endif
          <a href="{{ url("/index/navigation") }}"><i class="fa fa-arrows"></i> <span>Navigation</span></a>
        </li>
        @endif

        @if(in_array('A4', $navs))
        @if(isset($page) && $page == "Role")<li class="active">@else<li>@endif
          <a href="{{ url("/index/role") }}"><i class="fa fa-cogs"></i> <span>Role</span></a>
        </li>
        @endif

        @if(in_array('A5', $navs))
        @if(isset($page) && $page == "Status")<li class="active">@else<li>@endif
          <a href="{{ url("/index/status") }}"><i class="fa fa-feed"></i> <span>Status</span></a>
        </li>
        @endif

        @if(in_array('A6', $navs))
        @if(isset($page) && $page == "User")<li class="active">@else<li>@endif
          <a href="{{ url("/index/user") }}"><i class="fa fa-users"></i> <span>User</span></a>
        </li>
        @endif

        @if(in_array('A8', $navs))
        @if(isset($head) && $head == "Middle Process Adjustment")<li class="treeview active">@else<li class="treeview">@endif
          <a href="#">
           <i class="fa fa-credit-card"></i> <span>Kanban Adjustment</span>
           <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
         @if(isset($page) && $page == "queue")<li class="active">@else<li>@endif
          <a href="{{ url("/index/middle/barrel_adjustment") }}"><i class="fa fa-exchange"></i> <span>Barrel Queue</span></a>
        </li>

        @if(isset($page) && $page == "queue")<li class="active">@else<li>@endif
          <a href="{{ url("/index/middle/buffing_adjustment") }}"><i class="fa fa-exchange"></i> <span>Buffing Queue</span></a>
        </li>

        @if(isset($page) && $page == "queue")<li class="active">@else<li>@endif
          <a href="{{ url("/index/middle/buffing_canceled") }}"><i class="fa fa-exchange"></i> <span>Buffing Cancel</span></a>
        </li>

        @if(isset($page) && $page == "wip")<li class="active">@else<li>@endif
          <a href="{{ url("/index/middle/wip_adjustment") }}"><i class="fa fa-exchange"></i> <span>WIP</span></a>
        </li>

      </ul>
    </li>
    @endif

    @if(in_array('M0', $navs))
    <li class="header">Master Menu</li>
    @endif


    @if(in_array('M13', $navs))
    @if(isset($page) && $page == "Bill Of Material")<li class="active">@else<li>@endif
      <a href="{{ url("/index/bill_of_material") }}"><i class="fa fa-list-ol"></i> <span>Bill Of Material</span></a>
    </li>
    @endif

    @if(in_array('M23', $navs))
    @if(isset($page) && $page == "BOM Output")<li class="active">@else<li>@endif
      <a href="{{ url("/index/bom_output") }}"><i class="fa fa-table"></i> <span>BOM Output</span></a>
    </li>
    @endif

    @if(in_array('M1', $navs))
    @if(isset($page) && $page == "Container")<li class="active">@else<li>@endif
      <a href="{{ url("/index/container") }}"><i class="fa fa-truck"></i> <span>Container</span></a>
    </li>
    @endif

    @if(in_array('M2', $navs))
    @if(isset($page) && $page == "Container Schedule")<li class="active">@else<li>@endif
      <a href="{{ url("/index/container_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Container Schedule</span></a>
    </li>
    @endif

    @if(in_array('M3', $navs))
    @if(isset($page) && $page == "Destination")<li class="active">@else<li>@endif
      <a href="{{ url("/index/destination") }}"><i class="fa fa-arrows-alt"></i> <span>Destination</span></a>
    </li>
    @endif

    @if(in_array('M4', $navs))
    @if(isset($page) && $page == "Material")<li class="active">@else<li>@endif
      <a href="{{ url("/index/material") }}"><i class="fa fa-cube"></i> <span>Material</span></a>
    </li>
    @endif

    @if(in_array('M24', $navs))
    @if(isset($page) && $page == "Material Plant Data List")<li class="active">@else<li>@endif
      <a href="{{ url("/index/material_plant_data_list") }}"><i class="fa fa-list-alt"></i> <span>Material Plant Data List</span></a>
    </li>
    @endif

    @if(in_array('M5', $navs))
    @if(isset($page) && $page == "Material Volume")<li class="active">@else<li>@endif
      <a href="{{ url("/index/material_volume") }}"><i class="fa fa-cubes"></i> <span>Material Volume</span></a>
    </li>
    @endif

    @if(in_array('M14', $navs))
    @if(isset($page) && $page == "NG List")<li class="active">@else<li>@endif
      <a href="{{ url("/index/bill_of_material") }}"><i class="fa fa-chain-broken"></i> <span>NG List</span></a>
    </li>
    @endif

    @if(in_array('M6', $navs))
    @if(isset($page) && $page == "Origin Group")<li class="active">@else<li>@endif
      <a href="{{ url("/index/origin_group") }}"><i class="fa fa-bookmark"></i> <span>Origin Group</span></a>
    </li>
    @endif

    @if(in_array('M7', $navs))
    @if(isset($page) && $page == "Production Schedule")<li class="active">@else<li>@endif
      <a href="{{ url("/index/production_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Production Schedule</span></a>
    </li>
    @endif

    @if(in_array('M17', $navs))
    @if(isset($page) && $page == "Assy Picking Schedule")<li class="active">@else<li>@endif
      <a href="{{ url("/index/assy_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Assy Picking Schedule</span></a>
    </li>
    @endif

    @if(in_array('M19', $navs))
    @if(isset($head) && $head == "Raw Material Monitoring")<li class="treeview active">@else<li class="treeview">@endif
      <a href="#">
        <i class="fa fa-upload"></i> <span>Raw Material Monitoring</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        @if(isset($page) && $page == "Upload SMBMR")<li class="active">@else<li>@endif
          <a href="{{ url("index/material/smbmr") }}"><i class="fa fa-list-ol"></i>Material List By Model</a>
        </li>
        @if(isset($page) && $page == "Material Usage")<li class="active">@else<li>@endif
          <a href="{{ url("index/material/usage") }}"><i class="fa fa-calendar-plus-o"></i>Material Usage</a>
        </li>
        @if(isset($page) && $page == "Upload Storage")<li class="active">@else<li>@endif
          <a href="{{ url("index/material/storage") }}"><i class="fa fa-cubes"></i>Storage Loc Stock</a>
        </li>
      </ul>
    </li>
    @endif

    @if(in_array('M8', $navs))
    @if(isset($page) && $page == "Shipment Condition")<li class="active">@else<li>@endif
      <a href="{{ url("/index/shipment_condition") }}"><i class="fa fa-ship"></i> <span>Shipment Condition</span></a>
    </li>
    @endif

    @if(in_array('M9', $navs))
    @if(isset($page) && $page == "Shipment Schedule")<li class="active">@else<li>@endif
      <a href="{{ url("/index/shipment_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Shipment Schedule</span></a>
    </li>
    @endif

    @if(in_array('M10', $navs))
    @if(isset($page) && $page == "Weekly Calendar")<li class="active">@else<li>@endif
      <a href="{{ url("/index/weekly_calendar") }}"><i class="fa fa-calendar-plus-o"></i> <span>Weekly Calendar</span></a>
    </li>
    @endif

    @if(in_array('M18', $navs))
    @if(isset($page) && $page == "Safety Stock")<li class="active">@else<li>@endif
      <a href="{{ url("/index/safety_stock") }}"><i class="fa fa-cubes"></i> <span>Initial Safety Stock</span></a>
    </li>
    @endif

    
    @if(in_array('M20', $navs))
    @if(isset($page) && $page == "User Document")<li class="active">@else<li>@endif
      <a href="{{ url("/index/user_document") }}"><i class="fa fa-book"></i> <span>User Document</span></a>
    </li>
    @endif

    @if(in_array('M16', $navs))
    @if(isset($head) && $head == "Employees Data")<li class="treeview active">@else<li class="treeview">@endif
      <a href="#">
        <i class="fa fa-users"></i> <span>Master Employee</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
       @if(isset($page) && $page == "Master Employee")<li class="active">@else<li>@endif
        <a href="{{ url("index/MasterKaryawan") }}"><i class="fa fa-list-ol"></i>Employee Data</a>
      </li>

      @if(isset($page) && $page == "Mutation")<li class="active">@else<li>@endif
        <a href="{{ url("/index/mutation") }}"><i class="fa fa-clock-o"></i><span>Mutation</span></a>
      </li>

      @if(isset($page) && $page == "Promotion")<li class="active">@else<li>@endif
        <a href="{{ url("/index/promotion") }}"><i class="fa fa-clock-o"></i><span>Promotion</span></a>
      </li>

      @if(isset($page) && $page == "Termination")<li class="active">@else<li>@endif
        <a href="{{ url("/index/termination") }}"><i class="fa fa-clock-o"></i><span>Termination</span></a>
      </li>

    </ul>
  </li>
  @endif

  @if(in_array('M22', $navs))
  @if(isset($head) && $head == "IoT")<li class="treeview active">@else<li class="treeview">@endif
    <a href="#">
      <i class="fa fa-users"></i> <span>Internet of Things (IoT)</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    </a>
    <ul class="treeview-menu">
     @if(isset($page) && $page == "Standartisasi Suhu")<li class="active">@else<li>@endif
      <a href="{{ url("index/standart_temperature") }}"><i class="fa fa-list-ol"></i>Standarisasi Suhu</a>
    </li>

  </ul>
</li>
@endif


@if(in_array('S0', $navs))
<li class="header">Service Menu</li>
@endif

@if(in_array('S28', $navs))
@if(isset($head) && $head == "Pantry")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
    <i class="fa fa-coffee"></i> <span>Pantry</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    @if(isset($page) && $page == "Pantry")<li class="active">@else<li>@endif
      <a href="{{ url("/index/pantry/pesanmenu") }}"><i class="fa fa-coffee"></i><span>Pesan Minuman</span></a>
    </li>
    @if(isset($page) && $page == "Pantry Menu")<li class="active">@else<li>@endif
      <a href="{{ url("index/pantry/menu") }}"><i class="fa fa-calendar-plus-o"></i>Pantry Menu</a>
    </li>
    @if(isset($page) && $page == "Pantry Orders")<li class="active">@else<li>@endif
      <a href="{{ url("index/pantry/pesanan") }}"><i class="fa fa-cubes"></i>Daftar Pesanan</a>
    </li>
  </ul>
</li>
@endif

  <!-- @if(in_array('S28', $navs))
  @if(isset($page) && $page == "Pantry")<li class="active">@else<li>@endif
    <a href="{{ url("/index/pantry") }}"><i class="fa fa-coffee"></i> <span>Pantry</span></a>

  </li>
  @endif -->

  @if(in_array('S11', $navs))
  @if(isset($page) && $page == "Check Sheet")<li class="active">@else<li>@endif
    <a href="{{ url("/index/CheckSheet") }}"><i class="fa fa-calendar-check-o"></i> <span>Check Sheet</span></a>
  </li>
  @endif

  @if(in_array('S1', $navs))
  @if(isset($page) && $page == "FLO Band Instrument")<li class="active">@else<li>@endif
    <a href="{{ secure_url("/index/flo_view/bi") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
  </li>
  @endif

  @if(in_array('S2', $navs))
  @if(isset($page) && $page == "FLO Educational Instrument")<li class="active">@else<li>@endif
    <a href="{{ url("/index/flo_view/ei") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
  </li>
  @endif

  @if(in_array('S7', $navs))
  @if(isset($page) && $page == "FLO Maedaoshi BI")<li class="active">@else<li>@endif
    <a href="{{ url("/index/maedaoshi_bi") }}"><i class="fa fa-forward"></i> <span>Maedaoshi  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
  </li>
  @endif

  @if(in_array('S8', $navs))
  @if(isset($page) && $page == "FLO Maedaoshi EI")<li class="active">@else<li>@endif
    <a href="{{ url("/index/maedaoshi_ei") }}"><i class="fa fa-forward"></i> <span>Maedaoshi  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
  </li>
  @endif

  @if(in_array('S3', $navs))
  @if(isset($page) && $page == "FLO Delivery")<li class="active">@else<li>@endif
    <a href="{{ url("/index/flo_view/delivery") }}"><i class="fa fa-shopping-cart"></i> <span>FLO  <i class="fa fa-angle-right"></i> Delivery</span></a>
  </li>
  @endif

  @if(in_array('S4', $navs))
  @if(isset($page) && $page == "FLO Stuffing")<li class="active">@else<li>@endif
    <a href="{{ url("/index/flo_view/stuffing") }}"><i class="fa fa-truck"></i> <span>FLO  <i class="fa fa-angle-right"></i> Stuffing</span></a>
  </li>
  @endif

  @if(in_array('S5', $navs))
  @if(isset($page) && $page == "FLO Shipment")<li class="active">@else<li>@endif
    <a href="{{ url("/index/flo_view/shipment") }}"><i class="fa fa-picture-o"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> Shipment</span></a>
  </li>
  @endif

  @if(in_array('S6', $navs))
  @if(isset($page) && $page == "FLO Lading")<li class="active">@else<li>@endif
    <a href="{{ url("/index/flo_view/lading") }}"><i class="fa fa-ship"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> On Board</span></a>
  </li>
  @endif

  @if(in_array('S9', $navs))
  @if(isset($page) && $page == "FLO Deletion")<li class="active">@else<li>@endif
    <a href="{{ url("/index/flo_view/deletion") }}"><i class="fa fa-ban"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> Deletion</span></a>
  </li>
  @endif

  @if(in_array('S24', $navs))
  @if(isset($page) && $page == "KD Z-PRO")<li class="active">@else<li>@endif
    <a href="{{ url("index/kd_zpro/"."z-pro") }}"><i class="fa fa-pencil-square-o"></i> <span>KD  <i class="fa fa-angle-right"></i> Z-PRO</span></a>
  </li>
  @endif

  @if(in_array('S25', $navs))
  @if(isset($page) && $page == "KD Sub Assy SX")<li class="active">@else<li>@endif
    <a href="{{ url("index/kd_subassy_sx/"."sub-assy-sx") }}"><i class="fa fa-pencil-square-o"></i> <span>KD  <i class="fa fa-angle-right"></i> Sub Assy SX</span></a>
  </li>
  @endif

  @if(in_array('S26', $navs))
  @if(isset($page) && $page == "KD Sub Assy FL")<li class="active">@else<li>@endif
    <a href="{{ url("index/kd_subassy_fl/"."sub-assy-fl") }}"><i class="fa fa-pencil-square-o"></i> <span>KD  <i class="fa fa-angle-right"></i> Sub Assy FL</span></a>
  </li>
  @endif

  @if(in_array('S27', $navs))
  @if(isset($page) && $page == "KD Sub Assy CL")<li class="active">@else<li>@endif
    <a href="{{ url("index/kd_subassy_cl/"."sub-assy-cl") }}"><i class="fa fa-pencil-square-o"></i> <span>KD  <i class="fa fa-angle-right"></i> Sub Assy CL</span></a>
  </li>
  @endif

  @if(in_array('S29', $navs))
  @if(isset($page) && $page == "KD Delivery")<li class="active">@else<li>@endif
    <a href="{{ url("index/kd_delivery") }}"><i class="fa fa-shopping-cart"></i> <span>KD  <i class="fa fa-angle-right"></i> Delivery</span></a>
  </li>
  @endif

   @if(in_array('S29', $navs))
  @if(isset($page) && $page == "KD Stuffing")<li class="active">@else<li>@endif
    <a href="{{ url("index/kd_stuffing") }}"><i class="fa fa-truck"></i> <span>KD  <i class="fa fa-angle-right"></i> Stuffing</span></a>
  </li>
  @endif

  @if(in_array('S14', $navs))
  @if(isset($page) && $page == "Overtime Form")<li class="active">@else<li>@endif
    <a href="{{ url("/index/overtime/overtime_form") }}"><i class="fa fa-clock-o"></i> <span> <span>Overtime Form</span></a>
  </li>
  @endif

  @if(in_array('S17', $navs))
  @if(isset($page) && $page == "overtimeDouble")<li class="active">@else<li>@endif
    <a href="{{ url("/index/double") }}"><i class="fa fa-clock-o"></i> <span>Double Overtime</span></a>
  </li>
  @endif

  @if(in_array('S20', $navs))
  @if(isset($page) && $page == "qna")<li class="active">@else<li>@endif
    <a href="{{ url("/index/qnaHR") }}"><i class="fa fa-comments-o"></i> <span>Q & A HR</span>
      @if(isset($notif)) 
      <span class="pull-right-container">
        <span class="label label-danger pull-right">{{$notif}}</span>
      </span>
      @endif
    </a>
  </li>
  @endif

  @if(in_array('S18', $navs))
  @if(isset($head) && $head == "Pianica")<li class="treeview active">@else<li class="treeview">@endif
    <a href="#">
      <i class="fa fa-tv"></i> <span>NG-Rate</span>
      <span class="pull-right-container">
        <i class="fa fa-angle-left pull-right"></i>
      </span>
    </a>
    <ul class="treeview-menu">
     @if(isset($page) && $page == "Bensuki")<li class="active">@else<li>@endif
      <a href="{{ url("/index/Pianica") }}"><i class="fa fa-list-ol"></i>Pianica</a>
    </li>

  </ul>
</li>
@endif

@if(in_array('S13', $navs))
@if(isset($head) && $head == "Purchase Order")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
    <i class="fa fa-list-alt"></i> <span>Purchase Order Material</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    @if(isset($page) && $page == "Purchase Order Archive")<li class="active">@else<li>@endif
      <a href="{{ url("/index/purchase_order/po_archive") }}"><i class="fa fa-list-alt"></i> Archives</a>
    </li>
    @if(isset($page) && $page == "Purchase Order List")<li class="active">@else<li>@endif
      <a href="{{ url("/index/purchase_order/po_list") }}"><i class="fa fa-list-alt"></i> Purchase Order List</a>
    </li>
    @if(isset($page) && $page == "Purchase Order Create")<li class="treeview active">@else<li class="treeview">@endif
      <a href="#"><i class="fa fa-print"></i> Purchase Order Create
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
      <ul class="treeview-menu">
        <li><a href="{{ url("index/purchase_order/po_create") }}"><i class="fa fa-circle-o"></i> Create Purchase Order</a></li>
        <li><a href="{{ url("index/purchase_order/po_revise") }}"><i class="fa fa-circle-o"></i> Revise Purchase Order</a></li>
      </ul>
    </li>
  </ul>
</li>
@endif

@if(in_array('S10', $navs))
@if(isset($head) && $head == "Assembly Process")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-tv"></i> <span>Assembly Process</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
  @if(isset($page) && $page == "Process Stamp CL")<li class="active">@else<li>@endif
    <a href="{{ url("/index/process_stamp_cl") }}"><i class="fa fa-list-ol"></i> Clarinet</a>
  </li>

  @if(isset($page) && $page == "Process Assy FL")<li class="active">@else<li>@endif
    <a href="{{ url("/index/process_assy_fl") }}"><i class="fa fa-list-ol"></i> Flute</a>
  </li>


  @if(isset($page) && $page == "Process Stamp SX")<li class="active">@else<li>@endif
    <a href="{{ url("/index/process_stamp_sx_assy") }}"><i class="fa fa-list-ol"></i> Saxophone</a>
  </li>
</ul>
</li>
@endif

@if(in_array('S12', $navs))
@if(isset($head) && $head == "Middle Process")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-tv"></i> <span>Middle Process</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
  @if(isset($page) && $page == "Middle Process CL")<li class="active">@else<li>@endif
    <a href="{{ url("/index/process_middle_cl") }}"><i class="fa fa-list-ol"></i> Clarinet</a>
  </li>
  @if(isset($page) && $page == "Middle Process FL")<li class="active">@else<li>@endif
    <a href="{{ url("/index/process_middle_fl") }}"><i class="fa fa-list-ol"></i> Flute</a>
  </li>
  @if(isset($page) && $page == "Middle Process SX")<li class="active">@else<li>@endif
    <a href="{{ url("/index/process_middle_sx") }}"><i class="fa fa-list-ol"></i> Saxophone</a>
  </li>
</ul>
</li>
@endif

@if(in_array('S21', $navs))
@if(isset($head) && $head == "Kaizen")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-credit-card"></i> <span>e-Kaizen Teian</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
 @if(isset($page) && $page == "Assess")<li class="active">@else<li>@endif
  <a href="{{ url("/index/kaizen") }}"><i class="fa fa-edit"></i> <span>Assessment List</span></a>
</li>

@if(isset($page) && $page == "Applied")<li class="active">@else<li>@endif
  <a href="{{ url("/index/kaizen/applied") }}"><i class="fa fa-rocket"></i> <span>Applied List</span></a>
</li>

</ul>
</li>
@endif

@if(in_array('S23', $navs))
@if(isset($head) && $head == "Clinic")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-hospital-o"></i> <span>Clinic</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
 @if(isset($page) && $page == "Diagnose")<li class="active">@else<li>@endif
  <a href="{{ url("index/diagnose") }}"><i class="fa fa-stethoscope"></i> <span>Diagnose</span></a>
</li>

@if(isset($page) && $page == "Medicines")<li class="active">@else<li>@endif
  <a href=""><i class="fa fa-medkit"></i> <span>Medicines</span></a>
</li>


</ul>
</li>
@endif

@if(in_array('R0', $navs))
<li class="header">Report Menu</li>
@endif

@if(in_array('R8', $navs))
@if(isset($head) && $head == "Employees")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-users"></i> <span>Employees</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
 @if(isset($page) && $page == "Manpower by Status Kerja")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/stat") }}" target="_blank"><i class="fa fa-line-chart"></i> Manpower by Status Kerja</a>
</li>
@if(isset($page) && $page == "Manpower by Gender")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/gender") }}" target="_blank"><i class="fa fa-line-chart"></i> Manpower by Gender</a>
</li>
@if(isset($page) && $page == "Manpower by Grade")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/grade") }}" target="_blank"><i class="fa fa-line-chart"></i> Manpower by Grade</a>
</li>
@if(isset($page) && $page == "Manpower by Department")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/department") }}" target="_blank"><i class="fa fa-line-chart"></i> Manpower by Department</a>
</li>
@if(isset($page) && $page == "Manpower by Jabatan")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/jabatan") }}" target="_blank"><i class="fa fa-line-chart"></i> Manpower by Jabatan</a>
</li>
@if(isset($page) && $page == "Leave Control")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/leave_control") }}"><i class="fa fa-line-chart"></i> Leave Control</a>
</li>
</ul>
</li>
@endif

@if(in_array('R1', $navs))
@if(isset($page) && $page == "FLO Detail")<li class="active">@else<li>@endif
  <a href="{{ url("/index/flo_view/detail") }}"><i class="fa fa-info-circle"></i> <span>FLO  <i class="fa fa-angle-right"></i> Detail</span></a>
</li>
@endif

@if(in_array('R2', $navs))
@if(isset($page) && $page == "Location Stock")<li class="active">@else<li>@endif
  <a href="{{ url("/index/inventory") }}"><i class="fa fa-cubes"></i> <span>Location Stock</span></a>
</li>
@endif

@if(in_array('R6', $navs))
@if(isset($head) && $head == "Transaction")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-tv"></i> <span>Transaction</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
 @if(isset($page) && $page == "Completion Transaction")<li class="active">@else<li>@endif
  <a href="{{ url("/index/tr_completion") }}"><i class="fa fa-table"></i> Completion</a>
</li>
@if(isset($page) && $page == "Transfer Transaction")<li class="active">@else<li>@endif
  <a href="{{ url("/index/tr_transfer") }}"><i class="fa fa-table"></i> Transfer</a>
</li>
@if(isset($page) && $page == "History Transaction")<li class="active">@else<li>@endif
  <a href="{{ url("/index/tr_history") }}"><i class="fa fa-table"></i> Transaction History</a>
</li>
</ul>
</li>
@endif

@if(in_array('R9', $navs))
@if(isset($head) && $head == "Overtime Report")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-users"></i> <span>Overtimes</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
 @if(isset($page) && $page == "Overtime Control")<li class="active">@else<li>@endif
  <a href="{{ url("/index/report/overtime_control") }}"><i class="fa fa-line-chart"></i> Overtime Control</a>
</li>
</ul>
</li>
@endif

@if(in_array('R7', $navs))
@if(isset($page) && $page == "Overtime Confirmation")<li class="active">@else<li>@endif
  <a href="{{ url("/index/overtime_confirmation") }}"><i class="fa fa-check-square-o"></i> <span>Overtime Confirmation</span></a>
</li>
@endif

@if(in_array('R3', $navs))
@if(isset($head) && $head == "Finished Goods")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
   <i class="fa fa-music"></i> <span>Finished Goods</span>
   <span class="pull-right-container">
    <i class="fa fa-angle-left pull-right"></i>
  </span>
</a>
<ul class="treeview-menu">
 @if(isset($page) && $page == "FG Production")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_production") }}"><i class="fa fa-line-chart"></i> Production</a>
</li>
@if(isset($page) && $page == "FG Stock")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_stock") }}"><i class="fa fa-line-chart"></i> Stock</a>
</li>
@if(isset($page) && $page == "FG Container Departure")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_container_departure") }}"><i class="fa fa-line-chart"></i> Container Departure</a>
</li>
@if(isset($page) && $page == "FG Shipment Result")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_shipment_result") }}"><i class="fa fa-line-chart"></i> Shipment Result</a>
</li>
@if(isset($page) && $page == "FG Shipment Schedule")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_shipment_schedule") }}"><i class="fa fa-table"></i> Shipment Schedule Data</a>
</li>
@if(isset($page) && $page == "FG Weekly Summary")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_weekly_summary") }}"><i class="fa fa-table"></i> Weekly Summary</a>
</li>
@if(isset($page) && $page == "FG Monthly Summary")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_monthly_summary") }}"><i class="fa fa-table"></i> Monthly Summary</a>
</li>
@if(isset($page) && $page == "FG Traceability")<li class="active">@else<li>@endif
  <a href="{{ url("/index/fg_traceability") }}"><i class="fa fa-table"></i> Traceability</a>
</li>
</ul>
</li>
@endif

{{-- @if(in_array('R4', $navs))
@if(isset($head) && $head == "Chorei")<li class="treeview active">@else<li class="treeview">@endif
  <a href="#">
    <i class="fa fa-tv"></i> <span>Chorei</span>
    <span class="pull-right-container">
      <i class="fa fa-angle-left pull-right"></i>
    </span>
  </a>
  <ul class="treeview-menu">
    @if(isset($page) && $page == "Chorei Production Result")<li class="active">@else<li>@endif
      <a href="{{ url("/index/ch_daily_production_result") }}"><i class="fa fa-line-chart"></i> Production Result</a>
    </li>
  </ul>
</li>
@endif --}}

@if(in_array('R5', $navs))
@if(isset($head) && $head == "Display")<li class="treeview active">@else<li class="treeview">@endif
 <a href="#">
  <i class="fa fa-tv"></i> <span>Display</span>
  <span class="pull-right-container">
   <i class="fa fa-angle-left pull-right"></i>
 </span>
</a>
<ul class="treeview-menu">
  @if(isset($page) && $page == "Display Finished Goods Accuracy")<li class="active">@else<li>@endif
    <a href="{{ url("/index/dp_fg_accuracy") }}"><i class="fa fa-line-chart"></i> FG Accuracy</a>
  </li>
  @if(isset($page) && $page == "Display Production Result")<li class="active">@else<li>@endif
   <a href="{{ url("/index/dp_production_result") }}"><i class="fa fa-line-chart"></i> FG Production Result</a>
 </li>
 @if(isset($page) && $page == "Display Stuffing Progress")<li class="active">@else<li>@endif
  <a href="{{ url("/index/display/stuffing_progress") }}"><i class="fa fa-line-chart"></i> Stuffing Progress</a>
</li>
</ul>
</li>
@endif

@if(in_array('R4', $navs))
@if(isset($page) && $page == "Chorei Production Result")<li class="active">@else<li>@endif
 <a href="{{ url("/index/ch_daily_production_result") }}"><i class="fa fa-tv"></i> <span>Chorei</span></a>
</li>
@endif
{{-- <li class="header">Trial Menu</li>
<li>
  <a href="{{ url("/index/flo_view/sn") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
</li>
<li>
  <a href="{{ url("/index/flo_view/pd") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
</li>
<li>
  <a href="{{ url("/index/trial_export") }}"><i class="fa fa-download"></i> <span>Export production</span></a>
</li>
<li>
  <a href="{{ url("/index/flo_view/detail") }}"><i class="fa fa-info-circle"></i> <span>FLO  <i class="fa fa-angle-right"></i> Detail</span></a>
</li>
<li>
  <a href="{{ url("/index/dp_production_result") }}"><i class="fa fa-line-chart"></i> Display Production Result</a>
</li> --}}
</ul>
</section>
</aside>
