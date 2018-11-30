<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YMPI Information System</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
    <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
    <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">
    <link rel="stylesheet" href="{{ asset("dist/css/AdminLTE.min.css")}}">
    <link rel="stylesheet" href="{{ asset("plugins/iCheck/square/blue.css")}}">
    <link rel="stylesheet" href="{{ asset("fonts/SourceSansPro.css")}}">
</head>
<body class="hold-transition register-page">
    <div class="register-box">
        <div class="register-logo">
            <a href="#"><b>YMPI</b><br>Information System</a>
        </div>
        <div class="register-box-body">
            <p class="login-box-msg">Register a new account</p>
            <form action="{{ route("register")}}" method="post">
                {{ csrf_field() }}
                <div class="form-group has-feedback {{ $errors->has('name') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Full name">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username">
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                    @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group has-feedback">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-5 pull-left">
                        <a href="{{ url("/") }}" class="pull-left"><span class="fa fa-caret-left"></span> Back to sign in</a>
                    </div>
                    <div class="col-xs-4 pull-right">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset("bower_components/jquery/dist/jquery.min.js")}}"></script>
    <script src="{{ asset("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
    <script src="{{ asset("plugins/iCheck/icheck.min.js")}}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        });

        jQuery(document).ready(function() {
            $('#username').val('');
            $('#password').val('');
        });
    </script>
</body>
</html>
]