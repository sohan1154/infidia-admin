@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        @if(Auth::user()->role=='Admin')
          <li class="breadcrumb-item">
			<a href="{{url('/users/'.$roleInUrl)}}">{{ $roleInUrl== 'delivery_boy' ? 'Delivery Boy' : ucfirst($roleInUrl) }} Users ({{$userName}})</a>
          </li>
          <li class="breadcrumb-item active">
            <a href="{{url('/returned-orders?userid='.base64_encode($user_id))}}">Returned Orders</a>
          </li>
        @else
          <li class="breadcrumb-item active">
		  	<a href="{{url('/orders')}}">Orders</a>
          </li>
        @endif
        <li class="breadcrumb-item active">Order Details</li>
      </ol>
	<?php 
		$orderid 			= Orderid($orders->order_id);
		$productdata 		= ProductDetails($orders->product_id);
		$productAttrdata 	= [];//ProductmultAttrDetails($orders->product_attr_id);
		$amount             = unserialize($orders->amount);
		$qty                = unserialize($orders->qty);
		$shipping_address	= UserAddress($orders->shipping_address);
		$billing_address	= UserAddress($orders->billing_address);
		 //echo "<pre>";print_r($billingaddress);
		// print_r($productAttrdata);die;
	?>
    <!-- DataTables Example -->
    <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Order Details
        </div>
        <div class="card-body">
          <div class="col-lg-12">
	        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                 <tbody>
                	
					 
						<?php 
							if(!empty($shipping_address->location) && !empty($billing_address->location)){
								$ship_address_loc = $shipping_address->location;
								$ship_address_houseno = $shipping_address->house_no;
								$ship_address_land = $shipping_address->landmark;
								$ship_address_county = $shipping_address->county;
								$ship_address_state = $shipping_address->state;
								$ship_address_city = $shipping_address->city;
								
								$bill_address_loc = $billing_address->location;
								$bill_address_houseno = $billing_address->house_no;
								$bill_address_land = $billing_address->landmark;
								$bill_address_county        = $billing_address->county;
								$bill_address_state        = $billing_address->state;
								$bill_address_city        = $billing_address->city;
							}else if(!empty($shipping_address->location)){
								$ship_address_loc = $shipping_address->location;
								$ship_address_houseno = $shipping_address->house_no;
								$ship_address_land = $shipping_address->landmark;
								$ship_address_county = $shipping_address->county;
								$ship_address_state = $shipping_address->state;
								$ship_address_city = $shipping_address->city;
								
								$bill_address_loc = $shipping_address->location;
								$bill_address_houseno = $shipping_address->house_no;
								$bill_address_land = $shipping_address->landmark;
								$bill_address_county = $shipping_address->county;
								$bill_address_state = $shipping_address->state;
								$bill_address_city = $shipping_address->city;
							}else{
								if(isset($billing_address->location)){
									$ship_address_loc = $billing_address->location;
									$ship_address_houseno = $billing_address->house_no;
									$ship_address_land = $billing_address->landmark;
									$ship_address_county = $billing_address->county;
									$ship_address_state = $billing_address->state;
									$ship_address_city = $billing_address->city;
									
									$bill_address_loc = $billing_address->location;
									$bill_address_houseno = $billing_address->house_no;
									$bill_address_land = $billing_address->landmark;
									$bill_address_county = $billing_address->county;
									$bill_address_state = $billing_address->state;
									$bill_address_city = $billing_address->city;
								}
							}
						?>
						<tr><td>	Seller : </td><td>{{$orders->seller_id}} </td></tr>
						<tr><td>	Shop Name : </td><td><?Php echo userDetails($orders->seller_id)->name; ?> </td></tr>
					    <tr><td>	Order Id : </td><td>{{$orders->order_id}} </td></tr>
					    <tr><td>	Total Amount : </td><td><span style="font-family:DejaVu Sans;">&#8377;</span> {{$orders->total_amount}} </td></tr>
					   	<tr><td>Order Status :</td><td> {{$orders->order_status}} </td></tr>
					   	<tr><td>Payment Status :</td><td> <?php if($orders->payment_status=='1'){ echo 'Completed';} else { echo 'Pending'; } ?> </td></tr>
						<?php if(isset($billing_address->location)){ ?> 
					   	<tr><td>Shipping Address :</td><td> {{$ship_address_loc}} {{$ship_address_houseno}} ,{{$ship_address_land}} <br>{{$ship_address_city}} {{$ship_address_state}}{{$ship_address_county}}</td></tr>
					   	<tr><td>Billing Address :</td><td> {{$bill_address_loc}} {{$bill_address_houseno}} ,{{$bill_address_land}} <br>{{$bill_address_city}} {{$bill_address_state}}{{$bill_address_county}}</td></tr>
						<?php } ?>
                        <tr><td>Payment Transaction ID :</td><td> {{paymentTnxDta($orders->id)}} </td></tr>
                        <tr><td>Order Created On :</td><td> {{$orders->created_at}} </td></tr>
				  
                </tbody>
            
            
          </table>  
          
          </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Products
        </div>
        <div class="card-body">
          <div class="col-lg-12">
	        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <tbody>
                	<?php if($productdata){
                		  foreach($productdata as $key => $val){ ?>
					<tr> 
					   <td>Product Name : {{$val->name}}<br>
					   	   Sku : {{$val->sku}} <br>
                        <?php foreach ($productAttrdata as $key1 => $value) {
                        	if($key==$key1){ 
                              $attrData =  unserialize($value->attr_name);
                              foreach ($attrData as $attrkey => $attrvalue) {
                              	 echo '<lable>'.$attrkey.' : </lable>'.$attrvalue.'<br>';
                              	 echo '<lable>Amount : </lable>'.$amount[$key1].'<br>';
                              	 echo '<lable>Qty : </lable>'.$qty[$key1].'<br>';
                              }
                              
                        	 }
                        } ?></td>
				    </tr>
				    <?php } } ?>
                </tbody>
            </table>  
          
          </div>

        </div>
    </div> 
    <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Users Details
        </div>
        <div class="card-body">
          <div class="col-lg-12">
	        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                 <tbody>
                 	<?php 
					//echo $orders->user_id;die;
					$userDetails = userDetails($orders->user_id);
					$UserAddress = getUserAddressByUserId($orders->user_id)->location;
					//print_r($UserAddress);die;
					?>
                	<tr><td>User Name : </td><td><?php if(isset($userDetails->name)){ ?> {{$userDetails->name}} <?php } ?></td></tr>
                	<tr><td>User Email : </td><td><?php if(isset($userDetails->email)){ ?> {{$userDetails->email}} <?php } ?></td></tr>
                	<tr><td>Phone number : </td><td><?php if(isset($userDetails->mobile)){ ?> {{$userDetails->mobile}}  <?php } ?></td></tr>
                	<tr><td>User Address : </td><td><?php if(isset($userDetails->mobile)){ ?> {{$UserAddress}}  <?php } ?></td></tr>
					<tr><td>Image : </td><td><img src="<?php echo userProfile($orders->user_id);?>" width=120></td></tr>
				  
                </tbody>
            
            
          </table>  
          
          </div>

        </div>
    </div>
	
	<div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Delivery Boy Details
        </div>
        <div class="card-body">
          <div class="col-lg-12">
	        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                 <tbody>
                 	<?php 
					if(isset($deliveryBoy->delivery_id) && !empty($deliveryBoy->delivery_id)){
						
					
					//echo $orders->user_id;die;
					$userDetails = userDetails($deliveryBoy->delivery_id);
					//$UserAddress = getUserAddressByUserId($orders->user_id)->location;
					//print_r($UserAddress);die;
					?>
                	<tr><td>Delivery boy Name : </td><td><?php if(isset($userDetails->name)){ ?> {{$userDetails->name}} <?php } ?></td></tr>
                	<tr><td>Delivery boy Email : </td><td><?php if(isset($userDetails->email)){ ?> {{$userDetails->email}} <?php } ?></td></tr>
                	<tr><td>Delivery boy number : </td><td><?php if(isset($userDetails->mobile)){ ?> {{$userDetails->mobile}}  <?php } ?></td></tr>
                	<tr><td>Delivery boy Image : </td><td><img src="<?php echo userProfile($orders->user_id);?>" width=120></td></tr>
					<?php }
					else{ ?>
						<tr><td> -- </td></tr>
					<?php } ?>
                </tbody>
            
            
          </table>  
          
          </div>

        </div>
    </div>

    </div> 
<style>
.cke_top{display:none;}
.cke_bottom{display:none;}

</style>	
    <!-- Sticky Footer -->
@include('layouts.admin_footer')