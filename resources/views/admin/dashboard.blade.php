@extends('layouts.adminLayout.admin_design')
@section('content')
<!--main-container-part-->
<div id="content">
    <!--breadcrumbs-->
      <div id="content-header">
        <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a></div>
      </div>
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
    <!--End-breadcrumbs-->
    
    <!--Action boxes-->
      <div class="container-fluid">
        <div class="quick-actions_homepage">
          <ul class="quick-actions">
            <li class="bg_lb"> <a href="{{ url('admin/dashboard') }}"> <i class="icon-dashboard"></i> <span class="label label-important"></span> My Dashboard </a> </li>
            {{-- <li class="bg_lg span3"> <a href="charts.html"> <i class="icon-signal"></i> Charts</a> </li> --}}
            @if(Session::get('adminDetails')['categories_view_access'] == 1)
              <li class="bg_ly"> <a href="{{ url('admin/view-categories') }}"> <i class="icon-inbox"></i><span class="label label-success">101</span> Categories </a> </li>
            @endif

            @if(Session::get('adminDetails')['products_access'] == 1)
            <li class="bg_lo"> <a href="{{ url('admin/view-products') }}"> <i class="icon-inbox"></i><span class="label label-success">101</span> Products </a> </li>
            @endif

            @if(Session::get('adminDetails')['orders_access'] == 1)
            <li class="bg_lb"> <a href="{{ url('admin/view-orders') }}"> <i class="icon-inbox"></i><span class="label label-success">101</span> Orders </a> </li>
            @endif

            @if(Session::get('adminDetails')['users_access'] == 1)
            <li class="bg_lr"> <a href="{{ url('admin/view-users') }}"> <i class="icon-inbox"></i><span class="label label-success">101</span> Users </a> </li>
            @endif
          </ul>
        </div>
    <!--End-Action boxes-->    
    
    <!--Chart-box-->    
        <div class="row-fluid">
          <div class="widget-box">
            <div class="widget-title bg_lg"><span class="icon"><i class="icon-signal"></i></span>
              <h5>Site Analytics</h5>
            </div>
            <div class="widget-content" >
              <div class="row-fluid">
                <div class="span9">
                  <div class="chart"></div>
                </div>
                <div class="span3">
                  <ul class="site-stats">
                    <li class="bg_lh"><i class="icon-user"></i> <strong>2540</strong> <small>Total Users</small></li>
                    <li class="bg_lh"><i class="icon-plus"></i> <strong>120</strong> <small>New Users </small></li>
                    <li class="bg_lh"><i class="icon-shopping-cart"></i> <strong>656</strong> <small>Total Shop</small></li>
                    <li class="bg_lh"><i class="icon-tag"></i> <strong>9540</strong> <small>Total Orders</small></li>
                    <li class="bg_lh"><i class="icon-repeat"></i> <strong>10</strong> <small>Pending Orders</small></li>
                    <li class="bg_lh"><i class="icon-globe"></i> <strong>8540</strong> <small>Online Orders</small></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
    <!--End-Chart-box--> 
        <hr/>
        <div class="row-fluid">
          <div class="span6">
            <div class="widget-box">
              <div class="widget-title bg_ly" data-toggle="collapse" href="#collapseG2"><span class="icon"><i class="icon-chevron-down"></i></span>
                <h5>Latest Posts</h5>
              </div>
              <div class="widget-content nopadding collapse in" id="collapseG2">
                <ul class="recent-posts">
                  <li>
                    <div class="user-thumb"> <img width="40" height="40" alt="User" src="{{ asset('images/backend_images/demo/av1.jpg') }}"> </div>
                    <div class="article-post"> <span class="user-info"> By: john Deo / Date: 2 Aug 2012 / Time:09:27 AM </span>
                      <p><a href="#">This is a much longer one that will go on for a few lines.It has multiple paragraphs and is full of waffle to pad out the comment.</a> </p>
                    </div>
                  </li>
                  <li>
                    <div class="user-thumb"> <img width="40" height="40" alt="User" src="{{ asset('images/backend_images/demo/av2.jpg') }}"> </div>
                    <div class="article-post"> <span class="user-info"> By: john Deo / Date: 2 Aug 2012 / Time:09:27 AM </span>
                      <p><a href="#">This is a much longer one that will go on for a few lines.It has multiple paragraphs and is full of waffle to pad out the comment.</a> </p>
                    </div>
                  </li>
                  <li>
                    <div class="user-thumb"> <img width="40" height="40" alt="User" src="{{ asset('images/backend_images/demo/av4.jpg')}} "> </div>
                    <div class="article-post"> <span class="user-info"> By: john Deo / Date: 2 Aug 2012 / Time:09:27 AM </span>
                      <p><a href="#">This is a much longer one that will go on for a few lines.Itaffle to pad out the comment.</a> </p>
                    </div>
                  <li>
                    <button class="btn btn-warning btn-mini">View All</button>
                  </li>
                </ul>
              </div>
            </div>
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-ok"></i></span>
                <h5>To Do list</h5>
              </div>
              <div class="widget-content">
                <div class="todo">
                  <ul>
                    <li class="clearfix">
                      <div class="txt"> Luanch This theme on Themeforest <span class="by label">Alex</span></div>
                      <div class="pull-right"> <a class="tip" href="#" title="Edit Task"><i class="icon-pencil"></i></a> <a class="tip" href="#" title="Delete"><i class="icon-remove"></i></a> </div>
                    </li>
                    <li class="clearfix">
                      <div class="txt"> Manage Pending Orders <span class="date badge badge-warning">Today</span> </div>
                      <div class="pull-right"> <a class="tip" href="#" title="Edit Task"><i class="icon-pencil"></i></a> <a class="tip" href="#" title="Delete"><i class="icon-remove"></i></a> </div>
                    </li>
                    <li class="clearfix">
                      <div class="txt"> MAke your desk clean <span class="by label">Admin</span></div>
                      <div class="pull-right"> <a class="tip" href="#" title="Edit Task"><i class="icon-pencil"></i></a> <a class="tip" href="#" title="Delete"><i class="icon-remove"></i></a> </div>
                    </li>
                    <li class="clearfix">
                      <div class="txt"> Today we celebrate the theme <span class="date badge badge-info">08.03.2013</span> </div>
                      <div class="pull-right"> <a class="tip" href="#" title="Edit Task"><i class="icon-pencil"></i></a> <a class="tip" href="#" title="Delete"><i class="icon-remove"></i></a> </div>
                    </li>
                    <li class="clearfix">
                      <div class="txt"> Manage all the orders <span class="date badge badge-important">12.03.2013</span> </div>
                      <div class="pull-right"> <a class="tip" href="#" title="Edit Task"><i class="icon-pencil"></i></a> <a class="tip" href="#" title="Delete"><i class="icon-remove"></i></a> </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="widget-box">
              <div class="widget-title"> <span class="icon"><i class="icon-ok"></i></span>
                <h5>Progress Box</h5>
              </div>
              <div class="widget-content">
                <ul class="unstyled">
                  <li> <span class="icon24 icomoon-icon-arrow-up-2 green"></span> 81% Clicks <span class="pull-right strong">567</span>
                    <div class="progress progress-striped ">
                      <div style="width: 81%;" class="bar"></div>
                    </div>
                  </li>
                  <li> <span class="icon24 icomoon-icon-arrow-up-2 green"></span> 72% Uniquie Clicks <span class="pull-right strong">507</span>
                    <div class="progress progress-success progress-striped ">
                      <div style="width: 72%;" class="bar"></div>
                    </div>
                  </li>
                  <li> <span class="icon24 icomoon-icon-arrow-down-2 red"></span> 53% Impressions <span class="pull-right strong">457</span>
                    <div class="progress progress-warning progress-striped ">
                      <div style="width: 53%;" class="bar"></div>
                    </div>
                  </li>
                  <li> <span class="icon24 icomoon-icon-arrow-up-2 green"></span> 3% Online Users <span class="pull-right strong">8</span>
                    <div class="progress progress-danger progress-striped ">
                      <div style="width: 3%;" class="bar"></div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <div class="widget-box">
              <div class="widget-title bg_lo"  data-toggle="collapse" href="#collapseG3" > <span class="icon"> <i class="icon-chevron-down"></i> </span>
                <h5>News updates</h5>
              </div>
              <div class="widget-content nopadding updates collapse in" id="collapseG3">
                <div class="new-update clearfix"><i class="icon-ok-sign"></i>
                  <div class="update-done"><a title="" href="#"><strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</strong></a> <span>dolor sit amet, consectetur adipiscing eli</span> </div>
                  <div class="update-date"><span class="update-day">20</span>jan</div>
                </div>
                <div class="new-update clearfix"> <i class="icon-gift"></i> <span class="update-notice"> <a title="" href="#"><strong>Congratulation Maruti, Happy Birthday </strong></a> <span>many many happy returns of the day</span> </span> <span class="update-date"><span class="update-day">11</span>jan</span> </div>
                <div class="new-update clearfix"> <i class="icon-move"></i> <span class="update-alert"> <a title="" href="#"><strong>Maruti is a Responsive Admin theme</strong></a> <span>But already everything was solved. It will ...</span> </span> <span class="update-date"><span class="update-day">07</span>Jan</span> </div>
                <div class="new-update clearfix"> <i class="icon-leaf"></i> <span class="update-done"> <a title="" href="#"><strong>Envato approved Maruti Admin template</strong></a> <span>i am very happy to approved by TF</span> </span> <span class="update-date"><span class="update-day">05</span>jan</span> </div>
                <div class="new-update clearfix"> <i class="icon-question-sign"></i> <span class="update-notice"> <a title="" href="#"><strong>I am alwayse here if you have any question</strong></a> <span>we glad that you choose our template</span> </span> <span class="update-date"><span class="update-day">01</span>jan</span> </div>
              </div>
            </div>
            
          </div>
          
        </div>
      </div>
    </div>
    
    <!--end-main-container-part-->
@endsection