@extends('layouts.master')
@section('stylesheets')
@stop

@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>Details <span class="text-purple">??????</span></small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li> --}}
	</ol>
</section>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">


@stop