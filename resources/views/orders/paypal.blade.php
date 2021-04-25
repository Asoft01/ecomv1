@extends('layouts.frontLayout.front_design')
@section('content')
<?php use App\Order; ?>
<section id="cart_items">
    <div class="container">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
              <li><a href="#">Home</a></li>
              <li class="active">PayPal</li>
            </ol>
        </div>
    </div>
</section>

<section id="do_action">
    <div class="container">
        <div class="heading" align="center">
            <h3>YOUR ORDER HAS BEEN PLACED</h3>
            <p>Your order number is {{ Session::get('order_id') }} and total payable about is INR {{ Session::get('grand_total') }}</p>
            <p>Please make payment by clicking on below Payment Button</p>
            <?php
                $orderDetails = Order::getOrderDetails(Session::get('order_id'));
                $orderDetails = json_decode(json_encode($orderDetails));
                // echo "<pre>"; print_r($orderDetails); die;
                $nameArr = explode(' ', $orderDetails->name);
                $getCountryCode = Order::getCountryCode($orderDetails->country);
                $getCountryCode = json_decode(json_encode($getCountryCode));
                // echo "<pre>"; print_r($getCountryCode); die;

            ?>

            {{-- <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="POST"> --}}
                <form action="https://www.paypal.com/cgi-bin/webscr" method="POST">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="sb-hr98j4930593@business.example.com">
                <input type="hidden" name="item_name" value="{{ Session::get('order_id') }}">
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="amount" value="{{ Session::get('grand_total') }}">
                <input type="hidden" name="first_name" value="{{ $nameArr[0] }}">
                <input type="hidden" name="last_name" value="{{ $nameArr[1] }}">
                <input type="hidden" name="address1" value="{{ $orderDetails->address}}">
                <input type="hidden" name="address2" value="">
                <input type="hidden" name="city" value="{{ $orderDetails->city }}">
                <input type="hidden" name="state" value="{{ $orderDetails->state }}">
                <input type="hidden" name="zip" value="{{ $orderDetails->pincode }}">
                <input type="hidden" name="email" value="{{ $orderDetails->user_email }}">
                <input type="hidden" name="email" value="{{ $getCountryCode->country_code }}">
                <input type="hidden" name="return" value="{{ url('paypal/thanks') }}">
                <input type="hidden" name="cance_return" value="{{ url('paypal/cancel') }}">
                
                <input type="image" src="https://www.braintreepayments.com/images/features/paypal/paypal-button@2x.png" value="Pay Now" width="400px" height="70px">
                
            </form>
        </div>
    </div>
</section><!--/#do_action-->
@endsection

<?php 
    Session::forget('grand_total');
    Session::forget('order_id');
    
?>