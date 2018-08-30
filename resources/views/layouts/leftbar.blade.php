  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <!-- Optionally, you can add icons to the links -->
        <li class="active"><a href="{{ url("/home") }}"><i class="fa fa-industry"></i> <span>Dashboard</span></a></li>
        <li class="header">Admin Menu</li>
        <li class="active"><a href="{{ url("/index/user") }}"><i class="fa fa-user"></i> <span>Users</span></a></li>
        <li class="active"><a href="{{ url("/index/level") }}"><i class="fa fa-tag"></i> <span>Levels</span></a></li>
        <li class="header">Main Menu</li>

        <li class="active"><a href="{{ url("/index/container") }}"><i class="fa fa-truck"></i> <span>Container</span></a></li>
        {{-- <li class="active"><a href="{{ url("/index/container_schedule") }}"><i class="fa fa-clock-o"></i> <span>Container Schedule</span></a></li> --}}
        <li class="active"><a href="{{ url("/index/destination") }}"><i class="fa fa-arrows"></i> <span>Destination</span></a></li>
        <li class="active"><a href="{{ url("/index/material") }}"><i class="fa fa-cube"></i> <span>Material</span></a></li>
        <li class="active"><a href="{{ url("/index/material_volume") }}"><i class="fa fa-cubes"></i> <span>Material Volume</span></a></li>
        <li class="active"><a href="{{ url("/index/origin_group") }}"><i class="fa fa-bookmark"></i> <span>Origin Gorup</span></a></li>
        {{-- <li class="active"><a href="{{ url("/index/sales_price") }}"><i class="fa fa-dollar"></i> <span>Sales Price</span></a></li> --}}
        {{-- <li class="active"><a href="{{ url("/index/sales_budget") }}"><i class="fa fa-line-chart"></i> <span>Sales Budget</span></a></li> --}}
        {{-- <li class="active"><a href="{{ url("/index/sales_forecast") }}"><i class="fa fa-line-chart"></i> <span>Sales Forecast</span></a></li> --}}
        <li class="active"><a href="{{ url("/index/shipment_condition") }}"><i class="fa fa-ship"></i> <span>Shipment Condition</span></a></li>
        {{-- <li class="active"><a href="{{ url("/index/weekly_calendar") }}"><i class="fa fa-calendar"></i> <span>Weekly Calendar</span></a></li> --}}
        {{-- <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="#">Link in level 2</a></li>
            <li><a href="#">Link in level 2</a></li>
          </ul>
        </li> --}}
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>