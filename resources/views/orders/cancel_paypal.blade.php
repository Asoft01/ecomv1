@extends('layouts.frontLayout.front_design')
@section('content')
<section id="cart_items">
    <div class="container">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
              <li><a href="#">Home</a></li>
              <li class="active">Thanks</li>
            </ol>
        </div>
    </div>
</section>

<section id="do_action">
    <div class="container">
        <div class="heading" align="center">
            <h3>YOUR PAYPAL ORDER HAS BEEN CANCELLED</h3>
            <p>Thanks for the Payment, we will process your order very soon</p>
            <p>Please contact us if there is any enquiry.</p>
        </div>
    </div>
</section><!--/#do_action-->
@endsection

<?php 
    Session::forget('grand_total');
    Session::forget('order_id');
    
?>