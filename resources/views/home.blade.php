@include('layouts.admin_header')

      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Breadcrumbs-->
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{url('/home')}}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Overview</li>
          </ol>

          <!-- Icon Cards-->
          <div class="row">
            <?php $role_id = Auth::user()->role;
                  if($role_id=='Admin') { ?>
            <div class="col-xl-2 col-sm-6 mb-2">
              <div class="card text-white bg-primary o-hidden h-100">
                <div class="card-body">
                  <div class="card-body-icon">
                    <i class="fas fa-fw fa-user"></i>
                  </div>
                  <div class="mr-5">Sellers</div>
                  <div class="mr-5">{{$sellerUser}}</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="{{url('/users/seller')}}">
                  <span class="float-left">View Details</span>
                  <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                  </span>
                </a>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 mb-2">
              <div class="card text-white bg-danger o-hidden h-100">
                <div class="card-body">
                  <div class="card-body-icon">
                    <i class="fas fa-fw fa-user"></i>
                  </div>
                  <div class="mr-5">Buyers</div>
                  <div class="mr-5">{{$buyerUser}}</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="{{url('/users/Buyer')}}">
                  <span class="float-left">View Details</span>
                  <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                  </span>
                </a>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 mb-2">
              <div class="card text-white bg-warning o-hidden h-100">
                <div class="card-body">
                  <div class="card-body-icon">
                    <i class="fas fa-fw fa-user"></i>
                  </div>
                  <div class="mr-10">Delivery Boys</div>
                  <div class="mr-5">{{$deliveryUser}}</div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="{{url('/users/delivery_boy')}}">
                  <span class="float-left">View Details</span>
                  <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                  </span>
                </a>
              </div>
            </div>
          <?php } ?>
            <div class="col-xl-2 col-sm-6 mb-2">
              <div class="card text-white bg-success o-hidden h-100">
                <div class="card-body">
                  <div class="card-body-icon">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                  </div>
                  <div class="mr-5">Orders</div>
                  
				  <div class="mr-5"><?php if(isset($orders)){ ?> {{$orders}} <?php } ?></div>
				  
                </div>
                <a class="card-footer text-white clearfix small z-1" href="{{url('/orders')}}">
                  <span class="float-left">View Details </span>
                  <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                  </span>
                </a>
              </div>
            </div>
            <div class="col-xl-2 col-sm-6 mb-2">
              <div class="card text-white bg-primary o-hidden h-100">
                <div class="card-body">
                  <div class="card-body-icon">
                    <i class="fa fa-credit-card"></i>
                  </div>
                  <div class="mr-6">Amount</div>
                  <div class="mr-5"><span style="font-family:DejaVu Sans;">&#8377;</span> <?php if(isset($payment)){ ?>
				  {{$payment}} <?php } ?></div>
                </div>
                <a class="card-footer text-white clearfix small z-1" href="javascript:;">
                  <span class="float-left">View Details </span>
                  <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                  </span>
                </a>
              </div>
            </div>
          </div>
          <?php $role_id = Auth::user()->role;
      if($role_id=='Admin') { ?>
          <!-- User Area Chart Example-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-chart-area"></i>
              User Area Chart Example</div>
            <div class="card-body">
              <canvas id="userAreaChart" width="100%" height="30"></canvas>
            </div>
            <div class="card-footer small text-muted">Updated {{date('Y-m-d H:i A')}}</div>
          </div>
        <?php }?>

          <!-- Order Area Chart Example-->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-chart-area"></i>
              Order Area Chart Example</div>
            <div class="card-body">
              <canvas id="orderAreaChart" width="100%" height="30"></canvas>
            </div>
            <div class="card-footer small text-muted">Updated {{date('Y-m-d H:i A')}}</div>
          </div>

          <!-- DataTables Example -->
        </div>
        <!-- /.container-fluid -->


@include('layouts.admin_footer')

<script type="text/javascript">
// Area Chart Example
var ctx = document.getElementById("userAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: [<?php foreach($userRecode as $recode) { echo "'".date('M, Y', strtotime($recode->created_at))."',";}?>],
    datasets: [{
      label: "Users",
      lineTension: 0.3,
      backgroundColor: "rgba(2,117,216,0.2)",
      borderColor: "rgba(2,117,216,1)",
      pointRadius: 5,
      pointBackgroundColor: "rgba(2,117,216,1)",
      pointBorderColor: "rgba(255,255,255,0.8)",
      pointHoverRadius: 5,
      pointHoverBackgroundColor: "rgba(2,117,216,1)",
      pointHitRadius: 50,
      pointBorderWidth: 2,
      data: [<?php foreach($userRecode as $recode) { echo "'".$recode->total."',";}?>],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 10
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: <?php echo (!empty($userRecodecount->total)) ? $userRecodecount->total : 0; ?>,
          maxTicksLimit: 5
        },
        gridLines: {
          color: "rgba(0, 0, 0, .125)",
        }
      }],
    },
    legend: {
      display: true
    }
  }
});
</script>

<script type="text/javascript">
var ctx = document.getElementById("orderAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: [<?php foreach($orderRecode as $recode) { echo "'".date('M, Y', strtotime($recode->created_at))."',";}?>],
    datasets: [{
      label: "Orders",
      lineTension: 0.3,
      backgroundColor: "rgba(2,117,216,0.2)",
      borderColor: "rgba(2,117,216,1)",
      pointRadius: 5,
      pointBackgroundColor: "rgba(2,117,216,1)",
      pointBorderColor: "rgba(255,255,255,0.8)",
      pointHoverRadius: 5,
      pointHoverBackgroundColor: "rgba(2,117,216,1)",
      pointHitRadius: 50,
      pointBorderWidth: 2,
      data: [<?php foreach($orderRecode as $recode) { echo "'".$recode->total."',";}?>],
    }],
  },
  options: {
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false
        },
        ticks: {
          maxTicksLimit: 10
        }
      }],
      yAxes: [{
        ticks: {
          min: 0,
          max: <?php echo isset($orderRecodecount->total) ? $orderRecodecount->total : ""; ?>,
          maxTicksLimit: 5
        },
        gridLines: {
          color: "rgba(0, 0, 0, .125)",
        }
      }],
    },
    legend: {
      display: true
    }
  }
});
</script>