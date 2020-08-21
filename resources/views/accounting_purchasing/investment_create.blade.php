@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  .col-xs-1,
  .col-xs-2,
  .col-xs-3,
  .col-xs-4,
  .col-xs-5,
  .col-xs-6,
  .col-xs-7,
  .col-xs-8,
  .col-xs-9,
  .col-xs-10 {
    padding-top: 5px;
  }

  
</style>
@endsection
@section('header')
<section class="content-header" style="margin-left: 10%">
  <h1>
    Create {{ $page }}
    <small>{{$title_jp}}</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="#">Examples</a></li>
    <li class="active">Blank page</li> --}}
  </ol>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible" style="margin-left: 10%;width: 80%;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible" style="margin-left: 10%;width: 80%;">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary" style="margin-left: 10%;width: 80%">
    <div class="box-header" style="margin-top: 10px;text-align: center">
      <h2 class="box-title"><b>Investment - Expense Apllication</b></h2>
    </div>  
    <form  id="importForm" name="importForm" method="post" action="{{ url('investment/create_post') }}" enctype="multipart/form-data">
      <div class="box-body">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form_identitas">Applicant<span class="text-red">*</span></label>
            <input type="text" id="form_identitas" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}} - {{$employee->department}}" readonly>
            <input type="hidden" id="applicant_id" name="applicant_id" class="form-control" value="{{$employee->employee_id}}" readonly>
            <input type="hidden" id="applicant_name" name="applicant_name" class="form-control" value="{{$employee->name}}" readonly>
            <input type="hidden" id="applicant_department" name="applicant_department" class="form-control" value="{{$employee->department}}" readonly>
          </div>
          <div class="col-xs-5">
            <label for="form_bagian">Submission Date<span class="text-red">*</span></label>
            <input type="text" id="date" class="form-control" value="<?= date('d F Y')?>" readonly>
            <input type="hidden" id="submission_date" name="submission_date" class="form-control" value="<?= date('Y-m-d')?>" readonly>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form_kategori">Kind Of Application<span class="text-red">*</span></label>
            <select class="form-control select2" id="category" name="category" data-placeholder='Choose Category' style="width: 100%"  
            onchange="selectClass(this); getNomor();" required="">
              <option value="">&nbsp;</option>
              <option value="Investment">Investment</option>
              <option value="Expense">Expense</option>
            </select>
          </div>
          <div class="col-xs-5">
            <label for="form_judul">Subject<span class="text-red">*</span></label>
            <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject" required="" >
          </div>
        </div>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form_kategori">Class Of Assets / Kind Of Expense<span class="text-red">*</span></label>
            <select class="form-control select2" id="type" name="type" data-placeholder='Choose Type' style="width: 100%" required="" onchange="getNomor()">

            </select>
          </div>

          <div class="col-xs-5">
            <label for="form">Vendor<span class="text-red">*</span></label>
            <select class="form-control select2" id="vendor" name="vendor" data-placeholder='Choose Supplier' style="width: 100%" required="" onchange="getSupplier(this)">
              <option value="">&nbsp;</option>
              @foreach($vendor as $ven)
              <option value="{{$ven->vendor_code}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
              @endforeach
            </select>

            <input type="hidden" class="form-control" id="vendor_name" name="vendor_name" readonly="">
          </div>
        </div>
        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form_grup">Main Objective<span class="text-red">*</span></label>
            <select class="form-control select2" id="objective" name="objective" data-placeholder='Choose objective' style="width: 100%" required="">
              <option value="">&nbsp;</option>
              <option value="Safety & Prevention of Pollution & Disaster">Safety & Prevention of Pollution & Disaster</option>
              <option value="R & D">R & D</option>
              <option value="Production of New Model">Production of New Model</option>
              <option value="Rationalization">Rationalization</option>
              <option value="Production Increase">Production Increase</option>
              <option value="Repair & Modification">Repair & Modification</option>
            </select>
          </div>

          <div class="col-xs-5">
            <label for="form_judul">Objective Explanation<span class="text-red">*</span></label>
            <input type="text" id="objective_detail" name="objective_detail" class="form-control" placeholder="Objective Explanation" required="">
          </div>
        </div>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="form_judul">Date Of Order<span class="text-red">*</span></label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right datepicker" id="date_order" name="date_order" placeholder="Date of Order" required="">
            </div>
          </div>
          <div class="col-xs-5">
            <label for="form">Date Of Delivery<span class="text-red">*</span></label>
            <div class="input-group date">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
              <input type="text" class="form-control pull-right datepicker" id="date_delivery" name="date_delivery" placeholder="Date of Delivery" required="">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
            <label for="payment">Payment Term<span class="text-red">*</span></label>
            <input type="text" id="payment_term" name="payment_term" class="form-control" placeholder="Payment Term" required="" readonly="">
          </div>
          <div class="col-xs-5">
            <label for="file">File Quotation<span class="text-red">*</span></label>
            <input type="file" id="attachment" name="attachment[]" multiple="" required="">
          </div>
        </div>

        <div class="row">
          <div class="col-xs-10 col-xs-offset-1">
            <label for="form_bagian">Reff Number<span class="text-red">*</span></label>
            <input type="text" class="form-control" id="reff_number" name="reff_number" placeholder="Reff Number" required="" readonly="">
          </div>
        </div>

        <div class="row">
          <div class="col-xs-5 col-xs-offset-1">
              <label>Note (Optional)</label>
              <textarea class="form-control pull-right" id="note" name="note"></textarea>
          </div>
          <div class="col-xs-5">
              <label>Other Quotation (Optional)</label>
              <textarea class="form-control pull-right" id="quotation_supplier" name="quotation_supplier"></textarea>
          </div>
        </div>

        
        <div class="row">
          <div class="col-sm-4 col-sm-offset-5" style="padding-top: 10px">
            <div class="btn-group">
              <a class="btn btn-danger" href="{{ url('investment') }}">Cancel</a>
            </div>
            <div class="btn-group">
              <button type="button" class="btn btn-success pull-right" onclick="submitForm()"><i class="fa fa-plus"></i>&nbsp; Create</button>
              <!-- <button type="submit" class="btn btn-success pull-right"><i class="fa fa-edit"></i>&nbsp; Create </button> -->
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

    jQuery(document).ready(function() {
      $('body').toggleClass("sidebar-collapse");
      $("#navbar-collapse").text('');

      CKEDITOR.replace('note' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '100px'
      });

      CKEDITOR.replace('quotation_supplier' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '100px'
      });
      

      });

      $(function () {
        $('.select2').select2({
          dropdownAutoWidth : true,
          allowClear:true,
        });
      });

      $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "yyyy-mm-dd",
        orientation: 'bottom auto',
      });

    function getSupplier(elem){

      $.ajax({
        url: "{{ route('admin.pogetsupplier') }}?supplier_code="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#vendor_name').val(obj.name);
          $('#payment_term').val(obj.duration);
        } 
      });
    }

    function getNomor() {
      var kode = "";
      var jenis = "";

      var cat = document.getElementById("category");
      var category = cat.options[cat.selectedIndex].value;

      var jen = document.getElementById("type");
      var jen2 = jen.options[jen.selectedIndex].value;

      if (category == "Investment") {
        kode = "F";
      }
      else if (category == "Expense"){
        kode = "E";
      }

      if (jen2 == "Building") {
        jenis = "B";
      }
      else if(jen2 == "Machine & Equipment"){
        jenis = "M";
      }
      else if(jen2 == "Vehicle"){
        jenis = "V";
      }
      else if(jen2 == "Tools, Jigs & Furniture"){
        jenis = "T";
      }
      else if(jen2 == "Moulding"){
        jenis = "MD";
      }
      else if(jen2 == "PC & Printer"){
        jenis = "PC";
      }
      else if(jen2 == "Land Acquisition"){
        jenis = "Ln";
      }

      if (jen2 == "Office Supplies") {
        jenis = "O";
      }
      else if(jen2 == "Repair & Maintenance"){
        jenis = "R";
      }
      else if(jen2 == "Constool"){
        jenis = "C";
      }
      else if(jen2 == "Professional Fee"){
        jenis = "P";
      }
      else if(jen2 == "Miscellaneous"){
        jenis = "etc";
      }
      else if(jen2 == "Others"){
        jenis = "etc";
      }


      var reff_no = document.getElementById("reff_number");

      $.ajax({
        url: "{{ url('investment/get_nomor_investment') }}", 
        type : 'GET', 
        success : function(data){
          var obj = jQuery.parseJSON(data);
          var tahun = obj.tahun;
          var bulan = obj.bulan;
          var no_urut = obj.no_urut;
          
          var romawi = romanize(bulan);

          reff_no.value = 'N'+tahun+no_urut+'/'+romawi+'/'+kode+'-'+jenis;
        }
      });

    }

    // Submit Form

    function submitForm() {

      if ($("#applicant_name").val() == "") {
        $("#loading").hide();
        alert("Kolom Nama Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#applicant_department").val() == "") {
        $("#loading").hide();
        alert("Akun Anda Tidak Memiliki Departemen");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#category").val() == "") {
        $("#loading").hide();
        alert("Kolom Kategori Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#subject").val() == "") {
        $("#loading").hide();
        alert("Kolom Subject Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#type").val() == "") {
        $("#loading").hide();
        alert("Kolom Tipe Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#objective").val() == "") {
        $("#loading").hide();
        alert("Kolom Objective Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#objective_detail").val() == "") {
        $("#loading").hide();
        alert("Kolom Detail Objective Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#date_order").val() == "") {
        $("#loading").hide();
        alert("Kolom Order Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#date_delivery").val() == "") {
        $("#loading").hide();
        alert("Kolom Delivery Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#payment_term").val() == "") {
        $("#loading").hide();
        alert("Kolom Payment Term Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#attachment").val() == "") {
        $("#loading").hide();
        alert("Kolom Attachment Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#reff_number").val() == "") {
        $("#loading").hide();
        alert("Kolom Reff Number Harap diisi");
        $("html").scrollTop(0);
        return false;
      }
      
      var conf = confirm("Apakah Anda yakin ingin membuat investment Ini?");
      if (conf == true) {
        $('[name=importForm]').submit();
      } else {

      }
    }
    
    $("#form_submit").click( function() {
      $("#loading").show();

      if ($("#applicant_name").val() == "") {
        $("#loading").hide();
        alert("Kolom Nama Kosong");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#applicant_department").val() == "") {
        $("#loading").hide();
        alert("Akun Anda Tidak Memiliki Departemen");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#category").val() == "") {
        $("#loading").hide();
        alert("Kolom Kategori Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#subject").val() == "") {
        $("#loading").hide();
        alert("Kolom Subject Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#type").val() == "") {
        $("#loading").hide();
        alert("Kolom Tipe Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#objective").val() == "") {
        $("#loading").hide();
        alert("Kolom Objective Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#objective_detail").val() == "") {
        $("#loading").hide();
        alert("Kolom Detail Objective Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#date_order").val() == "") {
        $("#loading").hide();
        alert("Kolom Order Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#date_delivery").val() == "") {
        $("#loading").hide();
        alert("Kolom Delivery Date Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#payment_term").val() == "") {
        $("#loading").hide();
        alert("Kolom Payment Term Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      if ($("#reff_number").val() == "") {
        $("#loading").hide();
        alert("Kolom Reff Number Harap diisi");
        $("html").scrollTop(0);
        return false;
      }

      var data = {
        applicant_id: $("#applicant_id").val(),
        applicant_name: $("#applicant_name").val(),
        applicant_department: $("#applicant_department").val(),
        reff_number: $("#reff_number").val(),
        submission_date: $("#submission_date").val(),
        category: $("#category").val(),
        subject: $("#subject").val(),
        type: $("#type").val(),
        objective: $("#objective").val(),
        objective_detail: $("#objective_detail").val(),
        supplier: $("#vendor").val(),
        supplier_name: $("#vendor_name").val(),
        date_order: $("#date_order").val(),
        date_delivery: $("#date_delivery").val(),
        payment_term: $("#payment_term").val(),
        note: CKEDITOR.instances.note.getData(),
        quotation_supplier: CKEDITOR.instances.quotation_supplier.getData(),
        attachment: $("#attachment").val()
      };

      $.post('{{ url("investment/create_post") }}', data, function(result, status, xhr){
        if(result.status == true){    
          $("#loading").hide();
          openSuccessGritter("Success","Berhasil Dibuat");
          setTimeout(function(){  window.location = "{{url('investment/detail')}}/"+result.id; }, 1000); 
        }
        else {
          $("#loading").hide();
          openErrorGritter('Error!', result.datas);
        }
        
      });

    });

    function selectClass(elem){
        var isi = elem.value;

        list = "";
        list += "<option></option> ";
        if (isi == "Investment") {
          list += "<option value='Building'>Building</option>";
          list += "<option value='Machine & Equipment'>Machine & Equipment</option>";
          list += "<option value='Vehicle'>Vehicle</option>";          
          list += "<option value='Tools, Jigs & Furniture'>Tools, Jigs & Furniture</option>";
          list += "<option value='Moulding'>Moulding</option>";
          list += "<option value='PC & Printer'>PC & Printer</option>";
          list += "<option value='Land Acquisition'>Land Acquisition</option>";
        }
        else if (isi == "Expense"){
          list += "<option value='Office Supplies'>Office Supplies</option>";
          list += "<option value='Repair & Maintenance'>Repair & Maintenance</option>";
          list += "<option value='Constool'>Constool</option>";
          list += "<option value='Professional Fee'>Proffesional Fee</option>";
          list += "<option value='Miscellaneous'>Miscellaneous</option>";
          list += "<option value='Meal'>Meal</option>";
          list += "<option value='Handling charge'>Handling charge</option>";
          list += "<option value='Technical Assistant'>Technical Assistant</option>";
          list += "<option value='Rent'>Rent</option>";
          list += "<option value='Transport Expense'>Transport Expense</option>";
          list += "<option value='Postage & Telecomunication'>Postage & Telecomunication</option>";
          list += "<option value='Bussiness Trip'>Bussiness Trip</option>";
          list += "<option value='Information System'>Information System</option>";
          list += "<option value='Packaging Cost'>Packaging Cost</option>";
          list += "<option value='Electricity, Water, & Gas'>Electricity, Water, & Gas</option>";
          list += "<option value='Insurance'>Insurance</option>";
          list += "<option value='Meeting&Guest'>Meeting & Guest</option>";
          list += "<option value='Book&periodical'>Book & periodical</option>";
          list += "<option value='Tax&Publicdues'>Tax & Publicdues</option>";
          list += "<option value='Medical'>Medical</option>";
          list += "<option value='Photocopy&printing'>Photocopy & printing</option>";
          list += "<option value='Expatriate permittance'>Expatriate permittance</option>";
          list += "<option value='Wellfare'>Wellfare</option>";
          list += "<option value='Training&Development'>Training & Development</option>";
          list += "<option value='Recruitment'>Recruitment</option>";
          list += "<option value='Others'>Others</option>";
        }

        $('#type').html(list);

    }

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


    function openSuccessGritter(title, message){
      jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '3000'
      });
    }

    function openErrorGritter(title, message) {
        jQuery.gritter.add({
          title: title,
          text: message,
          class_name: 'growl-danger',
          image: '{{ url("images/image-stop.png") }}',
          sticky: false,
          time: '2000'
        });
      }

  </script>
@stop

