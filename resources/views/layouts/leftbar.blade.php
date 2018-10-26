  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">

        @if(isset($page) && $page == "Dashboard")
        <li class="active">
          @else
          <li>
            @endif
            <a href="{{ url("/home") }}"><i class="fa fa-industry"></i> <span>Dashboard</span></a>
          </li>
          <li class="header">Administration Menu</li>

          @if(in_array(Auth::user()->level_id, [1]))
          @if(isset($page) && $page == "Batch Setting")
          <li class="active">
            @else
            <li>
              @endif
              <a href="{{ url("/index/batch_setting") }}"><i class="fa fa-clock-o"></i> <span>Batch Setting</span></a>
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

              @if(in_array(Auth::user()->level_id, [1]))
              @if(isset($page) && $page == "Department")
              <li class="active">
                @else
                <li>
                  @endif
                  <a href="{{ url("/index/department") }}"><i class="fa fa-sitemap"></i> <span>Department</span></a>
                </li>
                @endif

                @if(in_array(Auth::user()->level_id, [1]))
                @if(isset($page) && $page == "Level")
                <li class="active">
                  @else
                  <li>
                    @endif
                    <a href="{{ url("/index/level") }}"><i class="fa fa-map-marker"></i> <span>Level</span></a>
                  </li>
                  @endif

                  @if(in_array(Auth::user()->level_id, [1]))
                  @if(isset($page) && $page == "Status")
                  <li class="active">
                    @else
                    <li>
                      @endif
                      <a href="{{ url("/index/status") }}"><i class="fa fa-feed"></i> <span>Status</span></a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->level_id, [1]))
                    @if(isset($page) && $page == "User")
                    <li class="active">
                      @else
                      <li>
                        @endif
                        <a href="{{ url("/index/user") }}"><i class="fa fa-users"></i> <span>User</span></a>
                      </li>
                      @endif

                      <li class="header">Master Menu</li>
                      @if(in_array(Auth::user()->level_id, [1]))
                      @if(isset($page) && $page == "Container")
                      <li class="active">
                        @else
                        <li>
                          @endif
                          <a href="{{ url("/index/container") }}"><i class="fa fa-truck"></i> <span>Container</span></a>
                        </li>
                        @endif

                        @if(in_array(Auth::user()->level_id, [1]))
                        @if(isset($page) && $page == "Container Schedule")
                        <li class="active">
                          @else
                          <li>
                            @endif
                            <a href="{{ url("/index/container_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Container Schedule</span></a>
                          </li>
                          @endif

                          @if(in_array(Auth::user()->level_id, [1]))
                          @if(isset($page) && $page == "Destination")
                          <li class="active">
                            @else
                            <li>
                              @endif
                              <a href="{{ url("/index/destination") }}"><i class="fa fa-arrows-alt"></i> <span>Destination</span></a>
                            </li>
                            @endif

                            @if(in_array(Auth::user()->level_id, [1]))
                            @if(isset($page) && $page == "Material")
                            <li class="active">
                              @else
                              <li>
                                @endif
                                <a href="{{ url("/index/material") }}"><i class="fa fa-cube"></i> <span>Material</span></a>
                              </li>
                              @endif

                              @if(in_array(Auth::user()->level_id, [1]))
                              @if(isset($page) && $page == "Material Volume")
                              <li class="active">
                                @else
                                <li>
                                  @endif
                                  <a href="{{ url("/index/material_volume") }}"><i class="fa fa-cubes"></i> <span>Material Volume</span></a>
                                </li>
                                @endif

                                @if(in_array(Auth::user()->level_id, [1]))
                                @if(isset($page) && $page == "Origin Group")
                                <li class="active">
                                  @else
                                  <li>
                                    @endif
                                    <a href="{{ url("/index/origin_group") }}"><i class="fa fa-bookmark"></i> <span>Origin Gorup</span></a>
                                  </li>
                                  @endif

                                  @if(in_array(Auth::user()->level_id, [1]))
                                  @if(isset($page) && $page == "Production Schedule")
                                  <li class="active">
                                    @else
                                    <li>
                                      @endif
                                      <a href="{{ url("/index/production_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Production Schedule</span></a>
                                    </li>
                                    @endif

                                    @if(in_array(Auth::user()->level_id, [1]))
                                    @if(isset($page) && $page == "Shipment Condition")
                                    <li class="active">
                                      @else
                                      <li>
                                        @endif
                                        <a href="{{ url("/index/shipment_condition") }}"><i class="fa fa-ship"></i> <span>Shipment Condition</span></a>
                                      </li>
                                      @endif

                                      @if(in_array(Auth::user()->level_id, [1]))
                                      @if(isset($page) && $page == "Shipment Schedule")
                                      <li class="active">
                                        @else
                                        <li>
                                          @endif
                                          <a href="{{ url("/index/shipment_schedule") }}"><i class="fa fa-calendar-check-o"></i> <span>Shipment Schedule</span></a>
                                        </li>
                                        @endif

                                        @if(in_array(Auth::user()->level_id, [1]))
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
                                              <a href="{{ url("/index/flo_view/sn") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
                                            </li>
                                            @endif

                                            @if(in_array(Auth::user()->level_id, [1]))
                                            @if(isset($page) && $page == "FLO Production Date")
                                            <li class="active">
                                              @else
                                              <li>
                                                @endif
                                                <a href="{{ url("/index/flo_view/pd") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
                                              </li>
                                              @endif

                                              @if(in_array(Auth::user()->level_id, [1]))
                                              @if(isset($page) && $page == "FLO Delivery")
                                              <li class="active">
                                                @else
                                                <li>
                                                  @endif
                                                  <a href="{{ url("/index/flo_view/delivery") }}"><i class="fa fa-shopping-cart"></i> <span>FLO  <i class="fa fa-angle-right"></i> Delivery</span></a>
                                                </li>
                                                @endif

                                                @if(in_array(Auth::user()->level_id, [1]))
                                                @if(isset($page) && $page == "FLO Stuffing")
                                                <li class="active">
                                                  @else
                                                  <li>
                                                    @endif
                                                    <a href="{{ url("/index/flo_view/stuffing") }}"><i class="fa fa-truck"></i> <span>FLO  <i class="fa fa-angle-right"></i> Stuffing</span></a>
                                                  </li>
                                                  @endif

                                                  @if(in_array(Auth::user()->level_id, [1]))
                                                  @if(isset($page) && $page == "FLO Shipment")
                                                  <li class="active">
                                                    @else
                                                    <li>
                                                      @endif
                                                      <a href="{{ url("/index/flo_view/shipment") }}"><i class="fa fa-picture-o"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> Shipment</span></a>
                                                    </li>
                                                    @endif

                                                    @if(in_array(Auth::user()->level_id, [1]))
                                                    @if(isset($page) && $page == "FLO Lading")
                                                    <li class="active">
                                                      @else
                                                      <li>
                                                        @endif
                                                        <a href="{{ url("/index/flo_view/lading") }}"><i class="fa fa-ship"></i> <span> <span>FLO  <i class="fa fa-angle-right"></i> Lading</span></a>
                                                      </li>
                                                      @endif

                                                      <li class="header">Report Menu</li>

                                                      @if(in_array(Auth::user()->level_id, [1]))
                                                      @if(isset($page) && $page == "FLO Detail")
                                                      <li class="active">
                                                        @else
                                                        <li>
                                                          @endif
                                                          <a href="{{ url("/index/flo_view/detail") }}"><i class="fa fa-info-circle"></i> <span>FLO  <i class="fa fa-angle-right"></i> Detail</span></a>
                                                        </li>
                                                        @endif

                                                        @if(in_array(Auth::user()->level_id, [1]))
                                                        @if(isset($page) && $page == "Location Stock")
                                                        <li class="active">
                                                          @else
                                                          <li>
                                                            @endif
                                                            <a href="{{ url("/index/inventory") }}"><i class="fa fa-cubes"></i> <span>Location Stock</span></a>
                                                          </li>
                                                          @endif

                                                          @if(in_array(Auth::user()->level_id, [1]))
                                                          @if(isset($head) && $head == "Finished Goods")
                                                          <li class="treeview active">
                                                            @else
                                                            <li class="treeview">
                                                              @endif
                                                              <a href="#">
                                                                <i class="fa fa-music"></i> <span>Finished Goods</span>
                                                                <span class="pull-right-container">
                                                                  <i class="fa fa-angle-left pull-right"></i>
                                                                </span>
                                                              </a>
                                                              <ul class="treeview-menu">
                                                                @if(isset($page) && $page == "FG Production")
                                                                <li class="active">
                                                                  @else
                                                                  <li>
                                                                    @endif
                                                                    <a href="{{ url("/index/fg_production") }}"><i class="fa fa-line-chart"></i> Production</a>
                                                                  </li>
                                                                  @if(isset($page) && $page == "FG Stock")
                                                                  <li class="active">
                                                                    @else
                                                                    <li>
                                                                      @endif
                                                                      <a href="{{ url("/index/fg_stock") }}"><i class="fa fa-line-chart"></i> Stock</a>
                                                                    </li>
                                                                    <li>
                                                                      <a href=""><i class="fa fa-line-chart"></i> Ship. Container</a>
                                                                    </li>
                                                                    <li>
                                                                      <a href=""><i class="fa fa-line-chart"></i> Shipment</a>
                                                                    </li>
                                                                    <li>
                                                                      <a href=""><i class="fa fa-line-chart"></i> Summary</a>
                                                                    </li>
                                                                  </ul>
                                                                </li>
                                                                @endif                                                         

                                                                <li class="header">Trial Menu</li>
                                                                <li>
                                                                  <a href="{{ url("/index/flo_view/sn") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Band Inst.</span></a>
                                                                </li>


                                                                @if(isset($page) && $page == "FLO Production Date")
                                                                <li class="active">
                                                                  @else
                                                                  <li>
                                                                    @endif
                                                                    <a href="{{ url("/index/flo_view/pd") }}"><i class="fa fa-pencil-square-o"></i> <span>FLO  <i class="fa fa-angle-right"></i> Educational Inst.</span></a>
                                                                  </li>

                                                                  <li>
                                                                    <a href="{{ url("/index/trial_export") }}"><i class="fa fa-download"></i> <span>Export production</span></a>
                                                                  </li>
                                                                  <li>
                                                                    <a href="{{ url("/index/flo_view/detail") }}"><i class="fa fa-info-circle"></i> <span>FLO  <i class="fa fa-angle-right"></i> Detail</span></a>
                                                                  </li>
                                                                </ul>
                                                              </section>
                                                            </aside>

