@include('layouts.admin_header')
<div id="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">
          <?php $segmentchild = Request::segment(2);?>
          <a href="{{url('/users/'.$segmentchild)}}">{{ $segmentchild== 'delivery_boy' ? 'Delivery Boy' : ucfirst($segmentchild) }} Users</a>
        </li>
        <li class="breadcrumb-item active">User Details</li>
      </ol>

      <!-- DataTables Example -->
    <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> User Details 
        </div>
        <div class="card-body">
            <div class="col-lg-12">
	            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		            <tbody>
		                <tr><th>Name</th><td>{{$users->name}}</td></tr>
		                <tr><th>Email</th><td>{{$users->email}}</td></tr>
		                <tr><th>Role</th><td>{{$users->role}}</td></tr>
		                <tr><th>Mobile</th><td>{{$users->mobile}}</td></tr>
					    <?php if($users->role=='Seller'){ 
					    echo "<tr><th>Min. Order</th><td>".getSellerMinOrder($users->id)."</td></tr>";	
					    echo "<tr><th>Rating</th><td>";
					    for($i=1;$i<=$users->rating;$i++){
                         echo '<i class="fa fa-star"></i>';
                        } 
                        echo "</td></tr>";
                        echo "<tr><th>Category</th><td>";
                        $cat = explode(',', $users->category);
                        if($cat){
	                        foreach ($cat as $key => $value) {
	                        	$keyval = $key +1;
	                        	echo $keyval.'. '.categoryName($value)."<br>";
	                        }
	                    }    
                        echo "</td></tr>";
                        $plan = SubscriptionPlan($users->plan_id);
                        echo "<tr><th>Subscription Plan</th><td>".$plan->name."</td></tr>";
                        } ?>
                        <tr><th>Address</th><td>
                        @if(count($userAddress)>0)
                        @foreach($userAddress as $key => $address)
                        @if($users->role=='Seller')
                        {{$users->shop_address.' '.$users->city.' '.$users->state.' '.$users->country}}
                        @elseif($users->role=='Buyer')
                        {{$address->type.' :  '.$address->location.' '.$address->house_no.', '.$address->address.',  '.$address->landmark}}
                        @else
                        {{$address->location.' '.$address->address}}
                        <br>
                        @endif
                        @endforeach
                        @endif
                        </td></tr>
                        <tr><th>Registerd By</th><td>{{$users->fb_id!='' ? 'Facebook' : 'App'}}</td></tr>
                        <tr><th>Image</th><td><img src="{{userProfile($users->id)}}" width="200"></td></tr>
					    
		            </tbody>
	            </table>  
            </div>
		</div>
    </div> 
    </div> 	
</div>    
    <!-- Sticky Footer -->
@include('layouts.admin_footer')