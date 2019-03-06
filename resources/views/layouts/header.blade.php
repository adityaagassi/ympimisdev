  <header class="main-header">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <a href="{{ url("/home") }}" class="logo">
      <span class="logo-mini"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="40px"></span>
      <span class="logo-lg" style="font-size: 37px"><b>M I R A I</b></span>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Service Desk <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
              <li><a href="{{ url("index/wjo_mis") }}">Management Information System <b>(MIS)</b></a></li>
            </ul>
          </li>
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              @php
              $avatar = 'images/avatar/'.Auth::user()->avatar;
              @endphp
              <img src="{{ url($avatar) }}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{Auth::user()->name}}</span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="{{ url($avatar) }}" class="img-circle" alt="User Image">
                <p>
                  {{Auth::user()->name}}
                  <small>{{Auth::user()->email}}</small>
                </p>
              </li>
              <li class="user-footer">
                <div class="row">
                  <div class="col-xs-4 pull-left">
                    <a class="btn btn-info btn-flat" href="{{ url("setting/user") }}">Setting</a>
                  </div>
                  <div class="col-xs-4 pull-right">
                    <a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                    </form>
                  </div>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>