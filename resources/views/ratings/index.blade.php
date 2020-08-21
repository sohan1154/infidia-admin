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
            <li class="breadcrumb-item active">Review & Rating</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Order Id</th>
                      <th>User Name</th>
                      <th>Shop Name</th>
                      <th>Review</th>
                      <th>Rating</th>
					         </tr>
                  </thead>
                 
                  <tbody>
                  @if($rating)  
				          @foreach($rating as $value)
                  <?php 
				  //echo $value->order_id."--";
				  $order = OrderDetail($value->order_id); 
				  $order_id = '';
				  if(isset($order->order_id)){
					  $order_id = $order->order_id;
				  }
				  
				  ?>
                    <tr>
                      <td>{{$order_id}}</td>
                      <td>{{userName($value->user_id)}}</td>
                      <td>{{userName($value->seller_id)}}</td>
                      <td>{{$value->review}}</td>
                      <td>
                        <?php for($i=1;$i<=$value->rating;$i++){
                         echo '<i class="fa fa-star"></i>';
                        } ?>
                      </td>
                      
					</tr>					 
					        @endforeach
                  @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        
        </div>
        <!-- /.container-fluid -->
		
		   

        <!-- Sticky Footer -->
@include('layouts.admin_footer')