  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">

        @if(isset($page) && $page == "Dashboard")<li class="active">@else<li>@endif
          <a href="{{ url("/home") }}"><i class="fa fa-industry"></i> <span>Dashboard</span></a>
        </li>

        <li class="header">Administration Menu</li>

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Batch Setting")<li class="active">@else<li>@endif
          <a href="{{ url("/index/batch_setting") }}"><i class="fa fa-clock-o"></i> <span>Batch Setting</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Code Generator")<li class="active">@else<li>@endif
          <a href="{{ url("/index/code_generator") }}"><i class="fa fa-barcode"></i> <span>Code Generator</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Department")<li class="active">@else<li>@endif
          <a href="{{ url("/index/department") }}"><i class="fa fa-sitemap"></i> <span>Department</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Level")<li class="active">@else<li>@endif
          <a href="{{ url("/index/level") }}"><i class="fa fa-map-marker"></i> <span>Level</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Status")<li class="active">@else<li>@endif
          <a href="{{ url("/index/status") }}"><i class="fa fa-feed"></i> <span>Status</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "User")<li class="active">@else<li>@endif
          <a href="{{ url("/index/user") }}"><i class="fa fa-users"></i> <span>User</span></a>
        </li>
        @endif

        <li class="header">Master Menu</li>

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Container")<li class="active">@else<li>@endif
          <a href="{{ url("/index/container") }}"><i class="fa fa-truck"></i> <span>Container</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Container Schedule")<li class="active">@else<li>@endif
          <a href="{{ url("/index/container_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Container Schedule</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Destination")<li class="active">@else<li>@endif
          <a href="{{ url("/index/destination") }}"><i class="fa fa-arrows-alt"></i> <span>Destination</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Material")<li class="active">@else<li>@endif
          <a href="{{ url("/index/material") }}"><i class="fa fa-cube"></i> <span>Material</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Material Volume")<li class="active">@else<li>@endif
          <a href="{{ url("/index/material_volume") }}"><i class="fa fa-cubes"></i> <span>Material Volume</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Origin Group")<li class="active">@else<li>@endif
          <a href="{{ url("/index/origin_group") }}"><i class="fa fa-bookmark"></i> <span>Origin Gorup</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Production Schedule")<li class="active">@else<li>@endif
          <a href="{{ url("/index/production_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Production Schedule</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Shipment Condition")<li class="active">@else<li>@endif
          <a href="{{ url("/index/shipment_condition") }}"><i class="fa fa-ship"></i> <span>Shipment Condition</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Shipment Schedule")<li class="active">@else<li>@endif
          <a href="{{ url("/index/shipment_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Shipment Schedule</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Weekly Calendar")<li class="active">@else<li>@endif
          <a href="{{ url("/index/weekly_calendar") }}"><i class="fa fa-calendar-plus-o"></i> <span>Weekly Calendar</span></a>
        </li>
        @endif

        <li class="header">Service Menu</li>

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Serial Number")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/sn") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Production Date")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/pd") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Delivery")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/delivery") }}"><i class="fa fa-shopping-cart"></i> <span>FLO  <i class="fa fa-angle-right"></i> Delivery</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Stuffing")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/stuffing") }}"><i class="fa fa-truck"></i> <span>FLO  <i class="fa fa-angle-right"></i> Stuffing</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Shipment")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/shipment") }}"><i class="fa fa-picture-o"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> Shipment</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Lading")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/lading") }}"><i class="fa fa-ship"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> Lading</span></a>
        </li>
        @endif

        <li class="header">Report Menu</li>

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "FLO Detail")<li class="active">@else<li>@endif
          <a href="{{ url("/index/flo_view/detail") }}"><i class="fa fa-info-circle"></i> <span>FLO  <i class="fa fa-angle-right"></i> Detail</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($page) && $page == "Location Stock")<li class="active">@else<li>@endif
          <a href="{{ url("/index/inventory") }}"><i class="fa fa-cubes"></i> <span>Location Stock</span></a>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
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

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($head) && $head == "Chorei")<li class="treeview active">@else<li class="treeview">@endif
          <a href="#">
            <i class="fa fa-tv"></i> <span>Chorei</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(isset($page) && $page == "Chorei Production Result")<li class="active">@else<li>@endif
              <a href="{{ url("/index/ch_daily_production_result") }}"><i class="fa fa-line-chart"></i> Daily Production Result</a>
            </li>
            <li>
              <a href=""><i class="fa fa-line-chart"></i> Accuracy of Production</a>
            </li>
            <li>
              <a href=""><i class="fa fa-line-chart"></i> Weekly Shipment</a>
            </li>
          </ul>
        </li>
        @endif

        @if(in_array(Auth::user()->level_id, [1]))
        @if(isset($head) && $head == "Display")<li class="treeview active">@else<li class="treeview">@endif
          <a href="#">
            <i class="fa fa-tv"></i> <span>Display</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            @if(isset($page) && $page == "Display Production Result")<li class="active">@else<li>@endif
              <a href="{{ url("/index/dp_production_result") }}"><i class="fa fa-line-chart"></i> Display Production Result</a>
            </li>
          </ul>
        </li>
        @endif                                                      

        <li class="header">Trial Menu</li>
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
        </li>
      </ul>
    </section>
  </aside>

