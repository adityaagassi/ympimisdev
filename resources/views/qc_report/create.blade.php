@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Create {{ $page }}
    <small>Create CPAR</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
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
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Create New CPAR</h3> --}}
    </div>  
    <form role="form" method="post" action="{{url('index/qc_report/create_action')}}" enctype="multipart/form-data">
      <div class="box-body">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
        <div class="form-group row" align="left">
          <label class="col-sm-1">Kepada<span class="text-red">*</span></label>
          <div class="col-sm-5" align="left">
            <select class="form-control select2" name="employee_id" style="width: 100%;" data-placeholder="Pilih Manager" required>
              <option value=""></option>
              @foreach($managers as $manager)
              <option value="{{ $manager->employee_id }}">{{ $manager->name }} - {{ $manager->position }} {{ $manager->department }}</option>
              @endforeach
            </select>
          </div>
          <label class="col-sm-1">Judul Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="judul_komplain" id="judul_komplain" placeholder="Judul / Subject Komplain" required="">
            <input type="hidden" class="form-control" name="via_komplain" id="via_komplain" value="Email" required readonly>
          </div>
        </div>

        <div class="form-group row" align="left">
          <label class="col-sm-1">Lokasi<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" style="width: 100%;" id="lokasi" name="lokasi" data-placeholder="Pilih Lokasi" required>
              <option></option>
              <option value='Office'>Office</option>
              <option value='Assy'>Assy</option>
              <option value='Body Process'>Body Process</option>
              <option value='Buffing'>Buffing</option>
              <option value='CL Body'>CL Body</option>
              <option value='Lacquering'>Lacquering</optiofn>
              <!-- <option value='Meeting Room'>Meeting Room</option> -->
              <option value='Part Process'>Part Process</option>
              <option value='Pianica'>Pianica</option>
              <option value='Plating'>Plating</option>
              <option value='Recorder'>Recorder</option>
              <option value='Sub Assy'>Sub Assy</option>
              <option value='Case KD'>Case KD</option>
              <!-- <option value='TR Room'>TR Room</option> -->
              <option value='Venova'>Venova</option>
              <option value='Warehouse'>Warehouse</option>
              <option value='Welding'>Welding</option>
              <option value='Other'>Other</option>
            </select>
          </div>
          <label class="col-sm-1">Departemen<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" name="department_id" id="department_id" style="width: 100%;" data-placeholder="Pilih Departemen" onchange="selectdepartemen()" required>
              <option value=""></option>
              <optgroup label="Production">
                @foreach($productions as $production)
                <option value="{{ $production->id }}">{{ $production->department_name }}</option>
                @endforeach
              </optgroup>
              <optgroup label="Procurement">
                @foreach($procurements as $procurment)
                <option value="{{ $procurment->id }}">{{ $procurment->department_name }}</option>
                @endforeach
              </optgroup>
              <optgroup label="Other">
                @foreach($others as $other)
                <option value="{{ $other->id }}">{{ $other->department_name }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tanggal Permintaan<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="tgl_permintaan" name="tgl_permintaan" placeholder="Masukkan Tanggal Permintaan" onchange="selectbulan()" required>
            </div>
          </div>
          <label class="col-sm-1">Sumber Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" id="sumber_komplain" name="sumber_komplain" style="width: 100%;" data-placeholder="Sumber Komplain" onchange="selectsumber()" required>
              <option value=""></option>
              <option value="Eksternal Complaint">Eksternal Complaint</option>
              <option value="Audit QA">Audit QA</option>
              <option value="Production Finding">Production Finding</option>
            </select>
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tanggal Balas<span class="text-red">*</span></label>
          <div class="col-sm-5">
             <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" id="tgl_balas" name="tgl_balas" placeholder="Masukkan Tanggal Balas" required>
            </div>
          </div>
          <label class="col-sm-1">Nomor CPAR<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="cpar_no" id="cpar_no" placeholder="Nomor CPAR" required readonly>
            <input type="hidden" class="form-control" name="kategori" id="kategori" placeholder="kategori" required>
            <input type="hidden" class="form-control" name="nomordepan" id="nomordepan" placeholder="nomordepan" required>
            <input type="hidden" class="form-control" name="lastthree" id="lastthree" placeholder="lastthree" required>
            <input type="hidden" class="form-control" name="getbulan" id="getbulan" placeholder="getbulan" required>
            <!-- <input type="text" class="form-control" name="staff" id="staff" placeholder="staff" required value="O11081664">
            <input type="text" class="form-control" name="chief" id="chief" placeholder="chief" required value="G03110980">
            <input type="text" class="form-control" name="manager" id="manager" placeholder="manager" required value="A97100056">
            <input type="text" class="form-control" name="dgm" id="dgm" placeholder="dgm" required value="E01090823">
            <input type="text" class="form-control" name="gm" id="gm" placeholder="gm" required value="P12061848"> -->
          </div>
        </div>

        <div class="form-group row increment" align="left">
          <label class="col-sm-1">File</label>
          <div class="col-sm-5">
            <input type="file" name="files[]">
            <button type="button" class="btn btn-success plusdata"><i class="glyphicon glyphicon-plus"></i>Add</button>
          </div>
          <span id="customer">
            <label class="col-sm-1">Customer<span class="text-red">*</span></label>
            <div class="col-sm-5" align="left">
              <select class="form-control select2" name="customer" style="width: 100%;" data-placeholder="Pilih Customer">
                <option value=""></option>
                @foreach($destinations as $destination)
                <option value="{{ $destination->destination_code }}">{{ $destination->destination_shortname }} - {{ $destination->destination_name }}</option>
                @endforeach
              </select>
            </div>
          </span>
          <span id="supplier">
            <label class="col-sm-1">Supplier<span class="text-red">*</span></label>
            <div class="col-sm-5" align="left">
              <select class="form-control select2" name="supplier" style="width: 100%;" data-placeholder="Pilih Supplier">
                <option value=""></option>
                @foreach($vendors as $vendor)
                <option value="{{ $vendor->vendor }}">{{ $vendor->name }}</option>
                @endforeach
              </select>
            </div>
          </span>
        </div>
          

        <div class="clone hide">
          <div class="form-group row control-group" style="margin-top:10px">
            <label class="col-sm-1">File</label>
            <div class="col-sm-6">
              <input type="file" name="files[]">
              <div class="input-group-btn"> 
                <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
              </div>
            </div>
          </div>
        </div>

        <!-- /.box-body -->
        <div class="col-sm-4 col-sm-offset-5">
          <div class="btn-group">
            <a class="btn btn-danger" href="{{ url('index/qc_report') }}">Cancel</a>
          </div>
          <div class="btn-group">
            <button type="submit" class="btn btn-primary col-sm-14">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')

  <script type="text/javascript">
    $(document).ready(function() {
      $(".plusdata").click(function(){ 
          var html = $(".clone").html();
          $(".increment").after(html);
      });

      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });

      $("#customer").hide();
      $("#supplier").hide();
    });

</script>
  <script>
    $(function () {
      $('.select2').select2()
    });
    
    $('#tgl_permintaan').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true
    });

    $('#tgl_balas').datepicker({
      format: "dd/mm/yyyy",
      autoclose: true,
      todayHighlight: true
    });

    //menjadikan angka ke romawi
    function romanize (num) {
      if (!+num)
        return false;
      var digits = String(+num).split(""),
        key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
               "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
               "","I","II","III","IV","V","VI","VII","VIII","IX"],
        roman = "",
        i = 3;
      while (i--)
        roman = (key[+digits.pop() + (i * 10)] || "") + roman;
      return Array(+digits.join("") + 1).join("M") + roman;
    }

    function addZero(i) {
      if (i < 10) {
        i = "0" + i;
      }
      return i;
    }

    function selectdepartemen(){

      $.ajax({
           url: "{{ url('index/qc_report/get_fiscal_year') }}", // your php file
           type : 'GET', // type of the HTTP request
           success : function(data){
              var obj = jQuery.parseJSON(data);
              var lastthree = obj.substr(obj.length - 3);
              // nomorcpar.value = "no/"+lastthree+"."+kategori+"/"+romawi+"/"+year;
              $('#lastthree').val(lastthree);
           }
        });
    }

    function selectbulan(){
          var tgl = document.getElementById("tgl_permintaan").value;
          var time = new Date(tgl);
          var dateArr = tgl.split("/");
          var forDate = dateArr[1];
          // console.log(forDate);
          $('#getbulan').val(forDate);  
    }


    function selectsumber() {
        var sumber = document.getElementById("sumber_komplain");
        var departemen = document.getElementById("department_id");
        var nomorcpar = document.getElementById("cpar_no");
        var kategori_cpar = document.getElementById("kategori");
        var getbulan = document.getElementById("getbulan").value;
        var getdepartemen = departemen.options[departemen.selectedIndex].value;
        var getsumber = sumber.options[sumber.selectedIndex].value;
        var kategori;

        var lastthree = $('#lastthree').val();
        if (getsumber == "Eksternal Complaint"){
          kategori = "E";
        }
        else if ((getdepartemen == 7 && getsumber == "Audit QA") || (getdepartemen == 7 && getsumber == "Production Finding")){
          kategori = "S";
        }
        else if (getdepartemen != 7 && getsumber == "Production Finding" || getsumber == "Audit QA"){
          kategori = "I";
        }

        if (kategori == "E") {
          kategori_cpar.value = "Eksternal";
          $("#customer").show();
          $("#supplier").hide();
        } else if (kategori == "S"){
          kategori_cpar.value = "Supplier";
          $("#supplier").show();
          $("#customer").hide();
        } else if (kategori == "I"){
          kategori_cpar.value = "Internal";
          $("#customer").hide();
          $("#supplier").hide();
        }

        // var bulan = new Date().getMonth()+1;
        var romawi = romanize(getbulan);
        var year = new Date().getFullYear();

        $.ajax({
           url: "{{ url('index/qc_report/get_nomor_depan') }}?kategori=" + kategori_cpar.value, 
           type : 'GET', 
           success : function(data){
              var obj = jQuery.parseJSON(data);
              var nomordepan = obj;
              // if (nomordepan == "") {
              //   nomordepan = 1;
              // }
              var no = nomordepan.split("/");
              var number = parseInt(no[0])
              $('#nomordepan').val(number+1);
              var nomordepan = $('#nomordepan').val();
              var truenumber = addZero(nomordepan);
              // var nomorsplit = nomor.split("/");
              nomorcpar.value = truenumber+"/"+lastthree+"."+kategori+"/"+romawi+"/"+year;
           }
        });
    }
  
  </script>
@stop

