  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <!-- Optionally, you can add icons to the links -->

        @if(isset($page) && $page == "Dashboard")
        <li class="active">
          @else
          <li>
            @endif
            <a href="{{ url("/home") }}"><i class="fa fa-industry"></i> <span>Dashboard</span></a>
          </li>
          <li class="header">Adiministration Menu</li>

          @if(in_array(Auth::user()->level_id, [1,2]))
          @if(isset($page) && $page == "User")
          <li class="active">
            @else
            <li>
              @endif
              <a href="{{ url("/index/user") }}"><i class="fa fa-users"></i> <span>User</span></a>
            </li>
            @endif

            @if(in_array(Auth::user()->level_id, [1,2]))
            @if(isset($page) && $page == "Level")
            <li class="active">
              @else
              <li>
                @endif
                <a href="{{ url("/index/level") }}"><i class="fa fa-map-marker"></i> <span>Level</span></a>
              </li>
              @endif

              @if(in_array(Auth::user()->level_id, [1]))
              @if(isset($page) && $page == "Code Generator")
              <li class="active">
                @else
                <li>
                  @endif
                  <a href="{{ url("/index/code_generator") }}"><i class="fa fa-barcode"></i> <span>Code Generator</span></a>
                </li>
                @endif

                <li class="header">Master Menu</li>
                @if(in_array(Auth::user()->level_id, [1,4]))
                @if(isset($page) && $page == "Container")
                <li class="active">
                  @else
                  <li>
                    @endif
                    <a href="{{ url("/index/container") }}"><i class="fa fa-truck"></i> <span>Container</span></a>
                  </li>
                  @endif

                  @if(in_array(Auth::user()->level_id, [1,4]))
                  @if(isset($page) && $page == "Container Schedule")
                  <li class="active">
                    @else
                    <li>
                      @endif
                      <a href="{{ url("/index/container_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Container Schedule</span></a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->level_id, [1,4]))
                    @if(isset($page) && $page == "Destination")
                    <li class="active">
                      @else
                      <li>
                        @endif
                        <a href="{{ url("/index/destination") }}"><i class="fa fa-arrows-alt"></i> <span>Destination</span></a>
                      </li>
                      @endif

                      @if(in_array(Auth::user()->level_id, [1,5]))
                      @if(isset($page) && $page == "Material")
                      <li class="active">
                        @else
                        <li>
                          @endif
                          <a href="{{ url("/index/material") }}"><i class="fa fa-cube"></i> <span>Material</span></a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->level_id, [1,4]))
                        @if(isset($page) && $page == "Material Volume")
                        <li class="active">
                          @else
                          <li>
                            @endif
                            <a href="{{ url("/index/material_volume") }}"><i class="fa fa-cubes"></i> <span>Material Volume</span></a>
                          </li>
                          @endif

                          @if(in_array(Auth::user()->level_id, [1,5]))
                          @if(isset($page) && $page == "Origin Group")
                          <li class="active">
                            @else
                            <li>
                              @endif
                              <a href="{{ url("/index/origin_group") }}"><i class="fa fa-bookmark"></i> <span>Origin Gorup</span></a>
                            </li>
                            @endif

                            @if(in_array(Auth::user()->level_id, [1,5,6]))
                            @if(isset($page) && $page == "Production Schedule")
                            <li class="active">
                              @else
                              <li>
                                @endif
                                <a href="{{ url("/index/production_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Production Schedule</span></a>
                              </li>
                              @endif



                              {{-- <li><a href="{{ url("/index/sales_price") }}"><i class="fa fa-dollar"></i> <span>Sales Price</span></a></li> --}}
                              {{-- <li><a href="{{ url("/index/sales_budget") }}"><i class="fa fa-line-chart"></i> <span>Sales Budget</span></a></li> --}}
                              {{-- <li><a href="{{ url("/index/sales_forecast") }}"><i class="fa fa-line-chart"></i> <span>Sales Forecast</span></a></li> --}}



                              @if(in_array(Auth::user()->level_id, [1,4]))
                              @if(isset($page) && $page == "Shipment Condition")
                              <li class="active">
                                @else
                                <li>
                                  @endif
                                  <a href="{{ url("/index/shipment_condition") }}"><i class="fa fa-ship"></i> <span>Shipment Condition</span></a>
                                </li>
                                @endif

                                @if(in_array(Auth::user()->level_id, [1,4,5]))
                                @if(isset($page) && $page == "Shipment Schedule")
                                <li class="active">
                                  @else
                                  <li>
                                    @endif
                                    <a href="{{ url("/index/shipment_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Shipment Schedule</span></a>
                                  </li>
                                  @endif

                                  @if(in_array(Auth::user()->level_id, [1,5]))
                                  @if(isset($page) && $page == "Weekly Calendar")
                                  <li class="active">
                                    @else
                                    <li>
                                      @endif
                                      <a href="{{ url("/index/weekly_calendar") }}"><i class="fa fa-calendar-plus-o"></i> <span>Weekly Calendar</span></a>
                                    </li>
                                    @endif

                                    <li class="header">Service Menu</li>

                                    @if(in_array(Auth::user()->level_id, [1]))
                                    @if(isset($page) && $page == "FLO Serial Number")
                                    <li class="active">
                                      @else
                                      <li>
                                        @endif
                                        <a href="{{ url("/index/flo_sn") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
                                      </li>
                                      @endif

                                      @if(in_array(Auth::user()->level_id, [1]))
                                      @if(isset($page) && $page == "FLO Production Date")
                                      <li class="active">
                                        @else
                                        <li>
                                          @endif
                                          <a href="{{ url("/index/flo_pd") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
                                        </li>
                                        @endif

                                          @if(in_array(Auth::user()->level_id, [1]))
                                          @if(isset($page) && $page == "Print FLO")
                                          <li class="active">
                                            @else
                                            <li>
                                              @endif
                                              <a href="{{ url("/index/flo") }}"><i class="fa fa-info-circle"></i> <span>FLO  <i class="fa fa-angle-right"></i> Detail</span></a>
                                            </li>
                                            @endif

                                            <li class="header">Report Menu</li>
                                         {{--  @if(in_array(Auth::user()->level_id, [1]))
                                          <li class="treeview">
                                            <a href="#"><i class="fa fa-info-circle"></i> <span>FLO Informations</span>
                                              <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                              </span>
                                            </a>
                                            <ul class="treeview-menu">
                                              <li><a href="#">Outstanding FLO</a></li>
                                              <li><a href="#">Link in level 2</a></li>
                                            </ul>
                                          </li>
                                          @endif --}}


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

  