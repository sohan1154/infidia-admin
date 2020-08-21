<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Infidia - Dashboard</title>

    <!-- Bootstrap core CSS-->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Page level plugin CSS-->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">

    <!-- chosen -->
    <link href="{{ asset('js/chosen/chosen.css') }}" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">
	  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />   -->
	  <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>

    <script>
      var baseurl = "<?php echo config('app.url'); ?>";
    </script>
  </head>

  <body id="page-top">

    <nav class="navbar navbar-expand navbar-white bg-white static-top">

      <?php
        if(empty(Auth::user())){
          Auth::logout();
          $url = env('APP_URL','');
          header('Location: '.$url);
          echo '<h1 style="text-align:center;">you can not access administrator account</h1>'; die;
        }
      ?>
		
      <a id="navbarDropdown"  class="navbar-brand mr-1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
       <img src="https://i.imgur.com/u0qr3uA.png"> <span class="caret"></span>
      </a>

      <button class="btn btn-link btn-sm text-dark order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
      </button>

      <!-- Navbar Search -->
      <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <div class="input-group">
          
        </div>
      </form>

      <!-- Navbar -->
      <ul class="navbar-nav ml-auto ml-md-0">
       
        <li class="nav-item dropdown no-arrow">

          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ Auth::user()->name }} <i class="fas fa-user-circle fa-fw"></i>
          </a>

          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
           
            <a id="navbarDropdown"  class="dropdown-item" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
              {{ Auth::user()->name }} <span class="caret"></span>
            </a>
           
            <div class="dropdown-divider"></div>

            <a class="dropdown-item" href="{{ route('logout') }}">
              {{ __('Logout') }}
            </a>
            <!-- <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              {{ __('Logout') }}
            </a> -->
            <!-- <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form> -->
          </div>
        </li>
      </ul>

    </nav>

    <div id="wrapper">

    <?php 
      $url = url()->current();
      $current_url = explode("/",$url);
      $active_url = last($current_url);
      $segment = Request::segment(1);
      $segmentchild = Request::segment(2);
      $role = Auth::user()->role;
    ?>

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">

      <li class="nav-item <?php echo $segment=='home' ? 'active' : '' ?>">
          <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
      </li>
      
      @if($role=='Admin')
		  <li class="nav-item <?php echo $segment=='banners' ? 'active' : '' ?>">
          <a class="nav-link" href="{{ url('/banners') }}">
            <i class="fas fa-fw fa-images"></i>
            <span>Banners </span></a>
      </li>

		  <li class="nav-item <?php echo $segment=='categories' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/categories') }}">
            <i class="fas fa-fw fa-th-large"></i>
            <span>Categories</span></a>
      </li>
      @endif

      <li class="nav-item <?php echo $segment=='business-categories' ? 'active' : '' ?>">
          <a class="nav-link" href="{{ url('/business-categories') }}">
            <i class="fas fa-fw fa-th"></i>
            <span>Business Categories </span></a>
      </li>

      @if($role=='Admin')
		  <li class="nav-item <?php echo $segment=='attributes' ? 'active' : '' ?>">
          <a class="nav-link" href="{{ url('/attributes') }}">
            <i class="fas fa-fw fa-sitemap"></i>
            <span>Attributes </span></a>
      </li>
      @endif

      @if($role=='Seller')
      <li class="nav-item <?php echo $segment=='products' || $segment=='product' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/products') }}">
            <i class="fas fa-fw fa-star"></i>
            <span>Products</span></a>
      </li>
      <li class="nav-item <?php echo $segment=='customers' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/customers') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Customers</span></a>
      </li>
      <li class="nav-item <?php echo $segment=='orders' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/orders') }}">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Orders</span></a>
      </li>
      @endif

		  @if($role=='Admin')
      <!-- <li class="nav-item <?php echo $segment=='subscriptions' ? 'active' : '' ?>">
          <a class="nav-link" href="{{ url('/subscriptions') }}">
            <i class="fas fa-fw fa-credit-card"></i>
            <span>Subscriptions Plan</span></a>
      </li>  -->

		  <li class="nav-item dropdown <?php echo $segment=='users' || $segment=='products' || $segment=='product' || $segment=='' || $segment==''  || $segment==''? 'active show' : '' ?>">
          <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-users"></i>
            <span>Users</span>
          </a>
		      <div class="dropdown-menu <?php echo $segment=='users' || $segment=='products' || $segment=='product' || $segment=='' || $segment==''  || $segment==''? 'active show' : '' ?>" aria-labelledby="pagesDropdown">
            <a class="dropdown-item <?php echo ($segment=='users' && $segmentchild=='Seller')  || ($segment=='users' && $segmentchild=='') || ($segment=='products' || $segment=='product') ? 'active' : '' ?>" href="{{ url('/users/Seller') }}">Sellers</a>
            <a class="dropdown-item <?php echo $segmentchild=='Buyer' ? 'active' : '' ?>" href="{{ url('/users/Buyer') }}">Buyers</a>
            <a class="dropdown-item <?php echo $segmentchild=='delivery_boy' || $segmentchild=='Delivery' ? 'active' : '' ?>" href="{{ url('/users/delivery_boy') }}">Delivery Boys</a> 
          </div>
      </li>	
      @endif

      @if($role=='Admin' || $role=='Seller')
      <li class="nav-item <?php echo $segment=='importcsv' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/importcsv') }}">
            <i class="fas fa-fw fa-file-upload"></i>
            <span>Import CSV</span></a>
      </li>
      @endif

      @if($role=='Admin')
      <li class="nav-item <?php echo $segment=='feedbacks' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/feedbacks') }}">
            <i class="fas fa-fw fa-envelope"></i>
            <span>Feedbacks</span></a>
      </li>
      <li class="nav-item <?php echo $segment=='contactus' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/contactus') }}">
            <i class="fas fa-fw fa-envelope"></i>
            <span>Contact US</span></a>
      </li>
      <li class="nav-item <?php echo $segment=='pages' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/pages') }}">
            <i class="fas fa-fw fa-file"></i>
            <span>Pages</span></a>
      </li>
      @endif

      <li class="nav-item <?php echo $segment=='ratings' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('ratings/') }}">
          <i class="fa fa-star"></i>
          <span>Review & Rating</span></a>
      </li>
      
      <!-- @if($role=='Admin')
      <li class="nav-item <?php echo $segment=='settings' ? 'active' : '' ?>">
        <a class="nav-link" href="{{ url('/settings') }}">
          <i class="fas fa-fw fa-cog"></i>
          <span>Settings</span></a>
      </li>
      @endif --!>

    </ul>

		<style>
		  label.error { float: none; color: red !important; padding-left: 50%; vertical-align: bottom; }
		</style>