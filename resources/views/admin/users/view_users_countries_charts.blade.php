<?php
 
 $dataPoints = array( 
	array("label"=>$getUserCountries[0]['country'], "y"=> $getUserCountries[0]['count']),
	array("label"=>$getUserCountries[1]['country'], "y"=> $getUserCountries[1]['count']),
    array("label"=>$getUserCountries[2]['country'], "y"=> $getUserCountries[2]['count']),
    array("label"=>$getUserCountries[3]['country'], "y"=> $getUserCountries[3]['count']),
    array("label"=>$getUserCountries[4]['country'], "y"=> $getUserCountries[4]['count'])
    
)
 
?>
<script>
window.onload = function() {
 
var d = new Date();
// var n = d.getFullYear();
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title: {
		text: "Registered Users Countries Count"
	},
	subtitles: [{
		text: "Year "+d.getFullYear()
	}],
	data: [{
		type: "pie",
		yValueFormatString: "#,##0.00\"%\"",
		indexLabel: "{label} ({y})",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
@extends('layouts.adminLayout.admin_design')
@section('content')

<div id="content">
    <div id="content-header">
        <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Users</a> <a href="#" class="current">View Users</a> </div>
        <h1>Users</h1>

        @if(Session::has('flash_message_error')) 
        <div class="alert alert-error alert-block">
            <button type="button" class="close" data-dismiss="alert">x</button>
            <strong>{!! session('flash_message_error') !!}</strong>
        </div>
        @endif   
        @if(Session::has('flash_message_success')) 
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">x</button>
                <strong>{!! session('flash_message_success') !!}</strong>
            </div>
        @endif 
    </div>
    <div style="margin-left: 20px;">
      <a href="{{ url('/admin/export-users') }}" class="btn btn-primary btn-mini">Export</a>
  </div>
    <div class="container-fluid">
      <hr>
      <div class="row-fluid">
        <div class="span12">
          
          <div class="widget-box">
            <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
              <h5>Users</h5>
            </div>
            <div class="widget-content nopadding">
                <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
@endsection