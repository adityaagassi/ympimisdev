@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    thead>tr>th{
        text-align:center;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(211,211,211);
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error {
        display: none;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance:textfield;
    }

</style>
@stop

@section('header')
<section class="content-header">
    <h1>
        Knock Down Outputs <span class="text-purple"> ???</span>
        <small>Splitter KD Case <span class="text-purple"> ???</span></small>
    </h1>
</section>
@endsection

@section('content')
<section class="content">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
        </p>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <center><span style="font-size: 3vw; color: red;font-weight: bold;"><i class="fa fa-angle-double-down"></i> SCAN KDO <i class="fa fa-angle-double-down"></i></span></center>
                </div>
                <div class="box-body" style="padding-bottom: 30px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group col-md-8 col-md-offset-2">
                                <div class="input-group-addon" id="icon-serial" style="font-weight: bold">
                                    <i class="glyphicon glyphicon-barcode"></i>
                                </div>
                                <input type="text" style="text-align: center; font-size: 22" class="form-control" id="kdo_number" placeholder="Scan KDO Here..." required>
                                <div class="input-group-addon" id="icon-serial">
                                    <i class="glyphicon glyphicon-ok"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="kdo_information">
        <div class="col-xs-12" style="padding-top: 1%;">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                    <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">KDO Information</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <table id="kdo_detail" class="table table-bordered table-striped table-hover" style="width: 100%;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 15%">KDO</th>
                                    <th style="width: 15%">Material</th>
                                    <th style="width: 40%">Desc</th>
                                    <th style="width: 10%">Location</th>
                                    <th style="width: 10%">Quantity</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="kdo_detail_body">
                            </tbody>
                        </table>

                        <div id="split_result">
                            <h2>Split Result :</h2>
                            <table id="kdo_splitter_result" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 2%">KD Number</th>
                                        <th style="width: 2%">Material Number</th>
                                        <th style="width: 5%">Material Description</th>
                                        <th style="width: 2%">Location</th>
                                        <th style="width: 1%">Qty</th>
                                        <th style="width: 3%">Created At</th>
                                        <th style="width: 1%">Reprint</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-default fade" id="split_modal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                    <h4 class="modal-title">
                        Split KDO
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12" style="padding: 0px;">
                                <div class="col-xs-3" style="padding-right: 0px;">
                                    <span style="font-weight: bold; font-size: 12px;">KDO Number:</span>
                                </div>
                                <div class="col-xs-3">
                                    <span style="font-weight: bold; font-size: 12px;">Material:</span>
                                </div>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <span style="font-weight: bold; font-size: 12px;">Material Description:</span>
                                </div>
                            </div>                            
                            <div class="col-xs-12" style="padding: 0px;">
                                <div class="col-xs-3" style="padding-right: 0px;">
                                    <input type="text" id="kd_number" style="width: 100%; font-size: 16px; text-align: center;" disabled>
                                </div>
                                <div class="col-xs-3">
                                    <input type="text" id="material_number" style="width: 100%; font-size: 16px; text-align: center;" disabled>
                                </div>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <input type="text" id="material_description" style="width: 100%; font-size: 16px; text-align: center;" disabled>
                                </div>
                            </div>
                            <div class="col-xs-12" style="padding: 0px;">
                                <div class="col-xs-3" style="padding-right: 0px;">
                                    <span style="font-weight: bold; font-size: 12px;">Quanity:</span>
                                </div>
                                <div class="col-xs-3">
                                    <span style="font-weight: bold; font-size: 12px;">Split Qty:</span>
                                </div>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <span style="font-weight: bold; font-size: 12px;">&nbsp;</span>
                                </div>
                            </div>
                            <div class="col-xs-12" style="padding: 0px;">
                                <div class="col-xs-3" style="padding-right: 0px;">
                                    <input type="number" id="quantity" style="width: 100%; font-size: 16px; text-align: center;" disabled>
                                </div>
                                <div class="col-xs-3">
                                    <input type="number" id="split_quantity" style="width: 100%; font-size: 16px; text-align: center;">
                                </div>
                                <div class="col-xs-3" style="padding-left: 0px;">
                                    <a class="btn btn-success btn-xs" style="width: 100%; font-size: 16px;" onclick="addSplit()">Add</a>
                                </div>
                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <table class="table table-hover table-bordered table-striped" id="table_split">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th style="width: 3%;">Material</th>
                                            <th style="width: 4%;">Desc</th>
                                            <th style="width: 3%;">Quantity</th>
                                            <th style="width: 1%;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_split_body">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" onclick="submitSplit()"><span><i class="fa fa-save"></i> Submit</span></button>
                </div>
            </div>
        </div>
    </div>


</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $("#kdo_number").focus();
        $("#kdo_information").hide();
        $("#split_result").hide();
    })

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
    var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
    var split = [];
    var count = 0;


    function openSuccessGritter(title, message){
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '2000'
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

    $('#kdo_number').keydown(function(event) {
        if (event.keyCode == 13 || event.keyCode == 9) {
            if($("#kdo_number").val().length >= 10){
                clearAll()
                scanKdoSplitter();
            }
            else{
                openErrorGritter('Error!', 'Nomor KDO tidak sesuai.');
                $("#kdo_number").val("");
                audio_error.play();
            }
        }
    });

    function scanKdoSplitter(){
        var kd_number  = $("#kdo_number").val();
        var data = {
            kd_number : kd_number,
            status : 2
        }

        $.get('{{ url("scan/kd_splitter") }}', data, function(result, status, xhr){
            if(result.status){
                $("#kdo_number").val("");
                $("#kdo_number").focus();

                $('#kdo_detail').DataTable().clear();
                $('#kdo_detail').DataTable().destroy();
                $('#kdo_detail_body').html("");

                var body = '';
                body += '<tr id="row'+result.kd.kd_number+'">';
                body += '<td style="vertical-align: middle;">'+result.kd.kd_number+'</td>';
                body += '<td style="vertical-align: middle;">'+result.kd.material_number+'</td>';
                body += '<td style="vertical-align: middle;">'+result.kd.material_description+'</td>';
                body += '<td style="vertical-align: middle;">'+result.kd.location+'</td>';
                body += '<td style="vertical-align: middle;">'+result.kd.quantity+'</td>';
                body += '<td><a href="javascript:void(0)" class="btn btn-sm btn-primary" onclick="showModal(id)" id="'+result.kd.kd_number+'""><i class="fa fa-hand-scissors-o"> Split</i></a></td>';
                body += '</tr>';

                $('#kdo_detail_body').append(body);
                $('#kdo_detail').DataTable({
                    'dom': 'Bfrtip',
                    'responsive':true,
                    'buttons': {
                        buttons:[]
                    },
                    'paging': false,
                    'lengthChange': true,
                    'searching': false,
                    'ordering': true,
                    'order': [],
                    'info': false,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true

                });

                $("#kdo_information").show();

                audio_ok.play();
                openSuccessGritter('Success!', result.message);

            }else{
                openErrorGritter('Error!', result.message);
                audio_error.play();
                $("#kdo_number").val("");
                $("#kdo_number").focus();
            }
        });
    }

    function showModal(id) {

        var kd_number = $('#row'+id).find('td').eq(0).text();
        var material_number = $('#row'+id).find('td').eq(1).text();
        var material_description = $('#row'+id).find('td').eq(2).text();
        var location = $('#row'+id).find('td').eq(3).text();
        var quantity = $('#row'+id).find('td').eq(4).text();

        console.log('#row+'+id);
        console.log(kd_number);

        $('#kd_number').val(kd_number);
        $('#material_number').val(material_number);
        $('#material_description').val(material_description);
        $('#quantity').val(quantity);
        $('#table_split_body').html('');


        $('#split_modal').modal('show');
    }

    function addSplit() {
        if($('#split_quantity').val() != ""){

            var material_number = $('#material_number').val();
            var material_description = $('#material_description').val();
            var kdo_quantity = $('#quantity').val();
            var quantity = $('#split_quantity').val();

            if((parseInt(count) + parseInt(quantity)) <= parseInt(kdo_quantity)){
                var tableData = "";
                tableData += "<tr id='rowsplit"+split.length+"'>";
                tableData += '<td>'+material_number+'</td>';
                tableData += '<td>'+material_description+'</td>';
                tableData += '<td>'+quantity+'</td>';
                tableData += "<td><a href='javascript:void(0)' onclick='remSplit(id)' id='rowsplit"+split.length+"' class='btn btn-danger btn-xs' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
                tableData += '</tr>';

                split.push({
                    'material_number' : material_number,
                    'material_description' : material_description,
                    'quantity' : quantity
                });

                count += parseInt(quantity);
                console.log(split);
                console.log(count);


                $('#table_split_body').append(tableData);
            }else{
                openErrorGritter('Error!', 'Quantity Split KDO melebihi Quantity KDO Asli');
                audio_error.play();
            }

            $('#split_quantity').val('');
        }else{
            openErrorGritter('Error!', 'Input Split Qty'); 
        }
    }

    function remSplit(id) {
        console.log(id);

        $('#'+id).remove();
        var index = id.replace("rowsplit", "");
        count -= parseInt(split[index].quantity);
        split.splice(index, 1);

        console.log(split);
        console.log(count);

    }

    function submitSplit() {

        var kd_number = $("#kd_number").val();
        var kdo_quantity = $('#quantity').val();

        if(split.length < 2){
            openErrorGritter('Error!', 'Split tidak bisa dilakukan'); 
            return false;
        }

        if(parseInt(kdo_quantity) != parseInt(count)){
            openErrorGritter('Error!', 'Quantity Split KDO tidak sama dengan Quantity KDO Asli'); 
            return false;
        }

        var data = {
            kd_number : kd_number,
            split : split
        }


        $("#loading").show();
        $.post('{{ url("fetch/kd_splitter") }}', data,  function(result, status, xhr){
            if(result.status){

                $("#kdo_number").focus();

                $('#material_number').val('');
                $('#material_description').val('');
                $('#quantity').val('');
                $('#split_quantity').val('');
                split = [];
                count = 0;

                fillTableNew(result.new_kd);
                $("#split_result").show();
                $('#'+kd_number).remove();

                $('#split_modal').modal('hide');

                $("#loading").hide();
                openSuccessGritter('Success', result.message);
            }else{
                $("#loading").hide();
                openErrorGritter('Error!', result.message);
            }

        });
    }

    function fillTableNew(kd_number){

        var data = {
            kd_number : kd_number
        }


        // $.get('{{ url("fetch/kdo_splitter_detail") }}', data,  function(result, status, xhr){});

        
        $('#kdo_splitter_result').DataTable().destroy();
        $('#kdo_splitter_result tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
        });
        var table = $('#kdo_splitter_result').DataTable( {
            'paging'        : true,
            'dom': 'Bfrtip',
            'responsive': true,
            'responsive': true,
            'lengthMenu': [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            'buttons': {
                buttons:[
                {
                    extend: 'pageLength',
                    className: 'btn btn-default',
                },
                {
                    extend: 'copy',
                    className: 'btn btn-success',
                    text: '<i class="fa fa-copy"></i> Copy',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-info',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn btn-warning',
                    text: '<i class="fa fa-print"></i> Print',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                },
                ]
            },
            'lengthChange'  : true,
            'searching'     : true,
            'ordering'      : true,
            'info'        : true,
            'order'       : [],
            'autoWidth'   : true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "type" : "get",
                "url" : "{{ url("fetch/kdo_splitter_detail") }}",
                "data" : data,
            },
            "columns": [
            { "data": "kd_number" },
            { "data": "material_number" },
            { "data": "material_description" },
            { "data": "location" },
            { "data": "quantity" },
            { "data": "updated_at" },
            { "data": "reprintKDO" }
            ]
        });

        table.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                    .search( this.value )
                    .draw();
                }
            });
        });

        $('#kdo_splitter_result tfoot tr').appendTo('#kdo_splitter_result thead');
    }

    function clearAll(){
        $('#material_number').val('');
        $('#material_description').val('');
        $('#quantity').val('');
        $('#split_quantity').val('');
        split = [];
        count = 0;

        $("#kdo_information").hide();
        $("#split_result").hide();

    }

    function reprintKDODetail(id){

        var data = id.split('+');

        var kd_detail = data[0];
        var location = data[1];


        printLabelSubassy(kd_detail, ('reprint'+kd_detail));
        openSuccessGritter('Success!', "Reprint Success");

    }

    function printLabelSubassy(kd_detail,windowName) {

        var url = '{{ url("index/print_label_split") }}'+'/'+kd_detail;

        newwindow = window.open(url, windowName, 'height=250,width=450');

        if (window.focus) {
            newwindow.focus();
        }

        return false;
    }



</script>

@stop