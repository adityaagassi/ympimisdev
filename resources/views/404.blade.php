<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | 404 Page not found</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/skins/_all-skins.min.css")}}">
  <link rel="stylesheet" href="{{ asset("fonts/SourceSansPro.css")}}">
</head>

<section class="content">
  <div class="error-page">
    <h2 class="headline text-yellow"> 404</h2>
    <div class="error-content">
      <h3><i class="fa fa-warning text-yellow"></i>Oops! Page not found.</h3>
      <p>
        We could not find the page you were looking for.<br> 
        Or the page you looking for is under maintenance.<br><br> 
        Meanwhile, you may <a href="javascript:history.back()">back to previous page</a>.
      </p>
    </div>
  </div>
</section>

<script href="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script href="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
<script href="{{ url("bower_components/fastclick/lib/fastclick.js")}}"></script>
<script href="{{ url("dist/js/adminlte.min.js")}}"></script>
<script href="{{ url("dist/js/demo.js")}}"></script>
</body>
</html>
