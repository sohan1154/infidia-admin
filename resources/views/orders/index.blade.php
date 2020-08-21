@include('layouts.admin_header')
      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Breadcrumbs-->
          @if(session()->has('alert-success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-success') }}
            </div>
          @endif
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{url('/home')}}">Dashboard</a>
            </li>
            @if(Auth::user()->role=='Admin')
            <li class="breadcrumb-item">
              <a href="{{url('/users/'.$roleInUrl)}}">{{ $roleInUrl== 'delivery_boy' ? 'Delivery Boy' : ucfirst($roleInUrl) }} Users ({{$userName}})</a>
            </li>
            @endif
            <li class="breadcrumb-item active">Orders</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <!--<div class="card-header">
              <i class="fas fa-table"></i>
              Orders
			  <a href="{{ url('orders/create')}}"><button type="button" class="btn btn-primary add-button">Add New Page</button></a>
			</div>-->
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Order Id</th>
                      <th>Order Date&Time</th>
                      <th>User</th>
                      <th>Order Status</th>
                      <th>Payment Status</th>
                      <th>Total Price</th>
                      <th>View</th>
                    </tr>
                  </thead>
                 
                  <tbody>
				            @foreach($orders as $order)
                    <tr>
                      <td>{{$order->order_id}}</td>
                      <td>{{$order->created_at}}</td>
                      <td>{{$order->user['name']}}</td>
                      <td>{{$order->order_status}}</td>
                      <td><?php if($order->payment_status=='1'){ echo 'Completed';} else { echo 'Pending'; } ?></td>
                      <td><span style="font-family:DejaVu Sans;">&#8377;</span>{{$order->total_amount}}</td>
					            <td>
                        <a title="View" href="{{ url('orders/view/'.base64_encode($order->id).'?userid='.base64_encode($user_id))}}"><i class="fa fa-eye " aria-hidden="true"></i></a>
                      </td>
                    </tr>
					          @endforeach
                  </tbody>

                </table>
              </div>
            </div>
          </div>

        
        </div>
        <!-- /.container-fluid -->
		
		   

        <!-- Sticky Footer -->
@include('layouts.admin_footer')