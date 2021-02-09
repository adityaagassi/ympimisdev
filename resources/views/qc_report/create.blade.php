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
            <select class="form-control select2" name="employee_id" style="width: 100%;" data-placeholder="Pilih Manager" required onchange="getManagerDepartemen(this)">
              <option value=""></option>
              @foreach($managers as $manager)
              <option value="{{ $manager->employee_id }}_{{ $manager->department }}">{{ $manager->name }} - {{ $manager->position }} {{ $manager->department }}</option>
              @endforeach
              <option value="PI0703002_Production Engineering Department">Susilo Basri Prasetyo - Manager Production Engineering Department</option>
              <option value="PI9805006_Woodwind Instrument - Body Parts Process (WI-BPP) Department">Fatchur Rozi - Manager Woodwind Instrument - Body Parts Process (WI-BPP) Department</option>
              <option value="PI9906002_Woodwind Instrument - Key Parts Process (WI-KPP) Department">Khoirul Umam - Woodwind Instrument - Key Parts Process (WI-KPP) Department</option>
            </select>
          </div>
          <label class="col-sm-1">Judul Komplain<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <input type="text" class="form-control" name="judul_komplain" id="judul_komplain" placeholder="Judul / Subject Komplain" required="">
            <input type="hidden" class="form-control" name="via_komplain" id="via_komplain" value="Email" required readonly>
          </div>
        </div>

        <div class="form-group row" align="left">
          <label class="col-sm-1">Lokasi NG / Masalah<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <select class="form-control select2" style="width: 100%;" id="lokasi" name="lokasi" data-placeholder="Pilih Lokasi" required>
              <option></option>
              <option value='Office'>Office</option>
              <option value='Assy'>Assy</option>
              <option value='Body Process'>Body Process</option>
              <option value='Buffing'>Buffing</option>
              <option value='CL Body'>CL Body</option>
              <option value='Lacquering'>Lacquering</option>
              <option value='Part Process'>Part Process</option>
              <option value='Pianica'>Pianica</option>
              <option value='Plating'>Plating</option>
              <option value='Recorder'>Recorder</option>
              <option value='Sub Assy'>Sub Assy</option>
              <option value='Case KD'>Case KD</option>
              <option value='Venova'>Venova</option>
              <option value='Warehouse'>Warehouse</option>
              <option value='Welding'>Welding</option>
              <option value='Incoming Check QA'>Incoming Check QA</option>
              <option value='Other'>Other</option>
            </select>
          </div>
          <label class="col-sm-1">Departemen Penerima<span class="text-red">*</span></label>
          <div class="col-sm-5">

            <input type="text" class="form-control" id="department_name" name="department_name" required="" readonly="">
            <input type="hidden" class="form-control" id="department_id" name="department_id" required="">
            <!-- <select class="form-control select2" name="department_id" id="department_id" style="width: 100%;" data-placeholder="Pilih Departemen" onchange="selectdepartemen()" required>
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
            </select> -->
          </div>
        </div>
        <div class="form-group row" align="left">
          <label class="col-sm-1">Tanggal Permintaan<span class="text-red">*</span></label>
          <div class="col-sm-5">
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right" placeholder="" value="<?= date('d/m/Y') ?>" disabled>
              <input type="hidden" class="form-control pull-right" id="tgl_permintaan" name="tgl_permintaan" placeholder="" value="<?= date('d/m/Y') ?>" >
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
            <input type="hidden" class="form-control" name="getyear" id="getyear" placeholder="getyear" required>
            <!-- <input type="text" class="form-control" name="staff" id="staff" placeholder="staff" required value="O11081664">
            <input type="text" class="form-control" name="chief" id="chief" placeholder="chief" required value="G03110980">
            <input type="text" class="form-control" name="manager" id="manager" placeholder="manager" required value="A97100056">
            <input type="text" class="form-control" name="dgm" id="dgm" placeholder="dgm" required value="E01090823">
            <input type="text" class="form-control" name="gm" id="gm" placeholder="gm" required value="P12061848"> -->
          </div>
        </div>

        <div id="kategori_komplain"></div>

        <div id="kategori_komplain_internal"></div>
        
        <div class="form-group row increment" align="left">
          <label class="col-sm-1">File</label>
          <div class="col-sm-5">
            <input type="file" name="files[]" multiple="">
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

          <span id="penemu_ng">
            <label class="col-sm-1">Penemu Masalah<span class="text-red">*</span></label>
            <div class="col-sm-5" align="left">
              <select class="form-control select2" id="penemu" name="penemu" style="width: 100%;" data-placeholder="Pilih Penemu" required="">
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
      $("#penemu_ng").hide();

      var tgl = document.getElementById("tgl_permintaan").value;
      var time = new Date(tgl);
      var dateArr = tgl.split("/");
      var forDate = dateArr[1];
      var forYear = dateArr[2];
      $('#getbulan').val(forDate); 
      $('#getyear').val(forYear);  
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

    function getManagerDepartemen(elem){

      $.ajax({
        url: "{{ route('admin.getDepartemen') }}?manager="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#department_id').val(obj.id_department);
          $('#department_name').val(obj.department);
          // console.log(obj.id_department)
        } 
      });

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

    function selectdepartemen(){

      
    }

    // function selectbulan(){
      
    // }


    function selectsumber() {
        var sumber = document.getElementById("sumber_komplain");
        var departemen = document.getElementById("department_id");
        var nomorcpar = document.getElementById("cpar_no");
        var kategori_cpar = document.getElementById("kategori");
        var getbulan = document.getElementById("getbulan").value;
        var gettahun = document.getElementById("getyear").value;
        var getdepartemen = document.getElementById("department_id").value;
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

          $("#kategori_komplain").show();
          $("#kategori_komplain_internal").hide();

          $("#customer").show();
          $("#supplier").hide();
          $("#penemu_ng").hide();

          $('#kategori_komplain').html("");

          $addeksternal = '<div class="form-group row" align="left"><div class="col-sm-6"></div><label class="col-sm-1">Kategori Komplain<span class="text-red">*</span></label><div class="col-sm-5" align="left"><select class="form-control select2" name="kategori_komplain" style="width: 100%;" data-placeholder="Pilih Kategori Komplain"><option value="KD Parts">KD Parts</option><option value="FG">Finished Goods</option><option value="NG Jelas">NG Jelas</option><option value="Claim Rate">Claim Rate</option></select></div></div>';

          $('#kategori_komplain').append($addeksternal);

        } else if (kategori == "S"){
          kategori_cpar.value = "Supplier";

          $("#kategori_komplain").show();
          $("#kategori_komplain_internal").hide();

          $("#supplier").show();
          $("#customer").hide();
          $("#penemu_ng").hide();

          $('#kategori_komplain').html("");

          $addsupplier = '<div class="form-group row" align="left"><div class="col-sm-6"></div><label class="col-sm-1">Kategori Komplain<span class="text-red">*</span></label><div class="col-sm-5" align="left"><select class="form-control select2" name="kategori_komplain" style="width: 100%;" data-placeholder="Pilih Kategori Komplain"><option value="Non YMMJ">Non YMMJ</option></select></div></div>';

          $('#kategori_komplain').append($addsupplier);

        } else if (kategori == "I"){
          kategori_cpar.value = "Internal";
          $("#kategori_komplain").hide();
          $('#kategori_komplain_internal').show();

          $("#customer").hide();
          $("#supplier").hide();
          $("#penemu_ng").show();

          $('#kategori_komplain_internal').html("");

          $addinternal = '<div class="form-group row" align="left"><div class="col-sm-6"></div><label class="col-sm-1">Kategori Komplain<span class="text-red">*</span></label><div class="col-sm-5" align="left"><select class="form-control select2" name="kategori_komplain" style="width: 100%;" data-placeholder="Pilih Kategori Komplain"><option value="Ketidaksesuaian Kualitas">Ketidaksesuaian Kualitas</option></select></div></div>';

          $('#kategori_komplain_internal').append($addinternal);

          $('#penemu').html("");

          list = "";
          list += "<option></option> ";
          if (getsumber == "Audit QA") {
            list += "<option value='QA M Pro'>QA M Pro</option>";
            list += "<option value='QA Sax FG'>QA Sax FG</option>";
            list += "<option value='QA Sax KD'>QA Sax KD</option>";          
            list += "<option value='QA CL FG'>QA CL FG</option>";
            list += "<option value='QA CL KD'>QA CL KD</option>";
            list += "<option value='QA FL FG Fungsi'>QA FL FG Fungsi</option>";
            list += "<option value='QA FL FG Visual 1'>QA FL FG Visual 1</option>";
            list += "<option value='QA FL FG Visual 2'>QA FL FG Visual 2</option>";
            list += "<option value='QA FL KD'>QA FL KD</option>";
          }
          else if (getsumber == "Production Finding"){
            list += "<option value='Assy Sax'>Assy Sax</option>";
            list += "<option value='Sub Assy Sax'>Sub Assy Sax</option>";
            list += "<option value='Assy CL'>Assy CL</option>";
            list += "<option value='Sub Assy CL'>Sub Assy CL</option>";
            list += "<option value='Assy FL'>Assy FL</option>";
            list += "<option value='Sub Assy FL'>Sub Assy FL</option>";
            list += "<option value='Plating'>Plating</option>";
            list += "<option value='Painting'>Painting</option>";
            list += "<option value='Buffing'>Buffing</option>";
            list += "<option value='Welding'>Welding</option>";
            list += "<option value='HTS'>HTS</option>";
            list += "<option value='B Pro'>B Pro</option>";
            list += "<option value='M Pro'>M Pro</option>";
            list += "<option value='Mouthpiece'>Mouthpiece</option>";
            list += "<option value='Pianica'>Pianica</option>";
            list += "<option value='Reedplate'>Reedplate</option>";
            list += "<option value='Assy Recorder'>Assy Recorder</option>";
            list += "<option value='Injeksi'>Injeksi</option>";
            list += "<option value='Venova'>Venova</option>";
            list += "<option value='Case Pro'>Case Pro</option>";
          }

          $('#penemu').html(list);

        }

        // var bulan = new Date().getMonth()+1;
        var romawi = romanize(getbulan);
        var year = new Date().getFullYear();

        $.ajax({
           url: "{{ url('index/qc_report/get_nomor_depan') }}?kategori=" + kategori_cpar.value, 
           type : 'GET', 
           success : function(data){
              var obj = jQuery.parseJSON(data);
              var nomordepan = obj.nomor;
              var fy = obj.year;
              var no = nomordepan.split("/");

              //get fiscal year from nomor cpar (196.E)
              var nomorsatu = no[1];
              var nomorreal = nomorsatu.split(".");

              if (lastthree != nomorreal[0]) {
                var number = 0;
              }
              else{
                var number = parseInt(no[0]);
              }

              $('#nomordepan').val(number+1);
              var nomordepan = $('#nomordepan').val();
              var truenumber = addZero(nomordepan);
              
              // var nomorsplit = nomor.split("/");

              nomorcpar.value = truenumber+"/"+lastthree+"."+kategori+"/"+romawi+"/"+gettahun;
           }
        });
    }
  
  </script>
@stop

