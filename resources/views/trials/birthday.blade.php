@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-wrapper {
    background-color: #00b785 !important;
  }

  .skin-purple .main-header .navbar {
    background-color: #00b785 !important;
  }

  .navbar-header {
    visibility: hidden;
  }

  .navbar-custom-menu {
    visibility: hidden;
  }

  @font-face {
    font-family: YPY;
    src: url("{{ url("fonts/Yippie-Yeah-Sans.ttf")}}");
  }

  @font-face {
    font-family: KMK;
    src: url("{{ url("fonts/Wash Your Hand.ttf")}}");
  }

  h2 {
    font-family: KMK, sans-serif;
    color: white; 
    text-align: center;
    font-size: 120pt;
    margin-top: 220px;
  }

  h1 {
    font-family: YPY, sans-serif;
    color: yellow; 
    text-align: center;
    font-size: 100pt;
    margin-top: 50px;
  }

  .blink{
    animation:blinkingText 1s infinite;
  }
  @keyframes blinkingText{
    0%{     color: yellow;    }
    49%{    color: yellow; }
    60%{    color: transparent; }
    80%{    color: transparent;  }
    100%{   color: yellow;    }
  }

  .blink2{
    animation:blinkingTexts 1s infinite;
  }
  @keyframes blinkingTexts{
    0%{    color: transparent; }
    49%{    color: transparent; }
    60%{     color: yellow;    }
    80%{   color: yellow;    }
    100%{    color: transparent;  }
  }

</style>
@endsection
@section('header')

@endsection
@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <img src="{{ url("images/biru.gif")}}" style="position: absolute; top: 10px; left: 500px;" width="400">
      <h2>Happy Birthday</h2>
      <h1><blink class="blink"><  </blink><blink class="blink2"> < </blink>Hiroshi Ura<blink class="blink2"> > </blink><blink class="blink"> ></blink></h1>
      <img src="{{ url("images/hijau.gif")}}" style="position: absolute; top: 200px; right: 5px">
      <img src="{{ url("images/biru.gif")}}" style="position: absolute; top: 150px; left: 50px;">
      <img src="{{ url("images/merah center.gif")}}" style="position: absolute; top: 350px; right: 20px" width="400">

      <img src="{{ url("images/baru3.gif")}}" style="position: absolute; top: 750px; left: 250px;" width="300">
      <img src="{{ url("images/baru3.gif")}}" style="position: absolute; top: 750px; right: 250px;" width="300">
    </div>
  </section>
  @stop

  @section('scripts')
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script>
    jQuery(document).ready(function() {

    })
  </script>

  @stop