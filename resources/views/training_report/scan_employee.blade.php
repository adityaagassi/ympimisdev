@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Scan Employee
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    {{-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
  <style>
        /* In order to place the tracking correctly */
        canvas.drawing, canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
</section>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
{{-- <script src="https://cdn.rawgit.com/serratus/quaggaJS/0420d5e0/dist/quagga.min.js"></script> --}}
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Detail User</h3> --}}
    </div>  
      <div class="box-body">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">Arahkan Kode QR Ke Kamera!</h3>
            </div>
            <div class="panel-body text-center" >
              <video width="300px" id="preview"></video>
            </div>
            <div class="panel-footer">
                {{-- <center><a class="btn btn-danger" href="{{ url('index/training_report/details/') }}">Kembali</a></center> --}}
            </div>
          </div>
        </div>
    </div>
  </div>
  {{-- <script type="text/javascript">
    let opts = {
      // Whether to scan continuously for QR codes. If false, use scanner.scan() to manually scan.
      // If true, the scanner emits the "scan" event when a QR code is scanned. Default true.
      continuous: true,
      
      // The HTML element to use for the camera's video preview. Must be a <video> element.
      // When the camera is active, this element will have the "active" CSS class, otherwise,
      // it will have the "inactive" class. By default, an invisible element will be created to
      // host the video.
      video: document.getElementById('preview'),
      
      // Whether to horizontally mirror the video preview. This is helpful when trying to
      // scan a QR code with a user-facing camera. Default true.
      mirror: false,
      
      // Whether to include the scanned image data as part of the scan result. See the "scan" event
      // for image format details. Default false.
      captureImage: false,
      
      // Only applies to continuous mode. Whether to actively scan when the tab is not active.
      // When false, this reduces CPU usage when the tab is not active. Default true.
      backgroundScan: true,
      
      // Only applies to continuous mode. The period, in milliseconds, before the same QR code
      // will be recognized in succession. Default 5000 (5 seconds).
      refractoryPeriod: 5000,
      
      // Only applies to continuous mode. The period, in rendered frames, between scans. A lower scan period
      // increases CPU usage but makes scan response faster. Default 1 (i.e. analyze every frame).
      scanPeriod: 1
    };
      let scanner = new Instascan.Scanner(opts);

      scanner.addListener('scan', function (content) {

        // alert(content);
        window.location.href = "https://172.17.128.87/miraidev/public/index/training_report/cek_employee/"+content+"/{{ $id }}";
        window.close();

      });

      Instascan.Camera.getCameras().then(function (cameras) {

        if (cameras.length > 0) {

          scanner.start(cameras[1]);

        } else {

          console.error('No cameras found.');

        }

      }).catch(function (e) {

        console.error(e);

      });

    </script> --}}
    <script>
        var _scannerIsRunning = false;

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        width: 480,
                        height: 320,
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "code_39_vin_reader",
                        "codabar_reader",
                        "upc_reader",
                        "upc_e_reader",
                        "i2of5_reader"
                    ],
                    debug: {
                        showCanvas: true,
                        showPatches: true,
                        showFoundPatches: true,
                        showSkeleton: true,
                        showLabels: true,
                        showPatchLabels: true,
                        showRemainingPatchLabels: true,
                        boxFromPatches: {
                            showTransformed: true,
                            showTransformedBox: true,
                            showBB: true
                        }
                    }
                },

            }, function (err) {
                if (err) {
                    console.log(err);
                    return
                }

                console.log("Initialization finished. Ready to start");
                Quagga.start();

                // Set flag to is running
                _scannerIsRunning = true;
            });

            Quagga.onProcessed(function (result) {
                var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

                if (result) {
                    if (result.boxes) {
                        drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                        result.boxes.filter(function (box) {
                            return box !== result.box;
                        }).forEach(function (box) {
                            Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
                        });
                    }

                    if (result.box) {
                        Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "#00F", lineWidth: 2 });
                    }

                    if (result.codeResult && result.codeResult.code) {
                        Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: 'red', lineWidth: 3 });
                    }
                }
            });


            Quagga.onDetected(function (result) {
                console.log("Barcode detected and processed : [" + result.codeResult.code + "]", result);
            });
        }


        // Start/stop scanner
        document.getElementById("btn").addEventListener("click", function () {
            if (_scannerIsRunning) {
                Quagga.stop();
            } else {
                startScanner();
            }
        }, false);
    </script>
  @endsection
