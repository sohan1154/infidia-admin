@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
		    @if(Auth::user()->role=='Admin')
          <li class="breadcrumb-item">
            <a href="{{url('/users/seller')}}">Sellers ({{$products->user->name}})</a>
          </li>
          <li class="breadcrumb-item active">
            <a href="{{url('/products?userid='.base64_encode($products->user_id))}}">Products</a>
          </li>
        @else
          <li class="breadcrumb-item active">
            <a href="{{url('/products')}}">Products</a>
          </li>
        @endif
        <li class="breadcrumb-item active">Produt Details</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Product Details
        </div>
        <div class="card-body">
          <div class="col-lg-12">
          <?php $user = userDetails($products->user_id);
				$catname = categoryName($products->category_id);
				$stock = productStocksQty($products->id);
				$meta = metaData($products->id);
				$images = productImagesData($products->id);
				$images = unserialize($images);
				//dd($images);
				
				//echo $catname;
		  ?>
				  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <tbody>
              <tr>
                <th>Sku</th><td>{{$products->sku}}</td>
			 </tr>
			 <?php if(isset($meta->keyword)){ $keyword= $meta->keyword;}else { $keyword= '';}?>
			 <?php if(isset($meta->meta_description)){ $meta_description= $meta->meta_description;}else { $meta_description= '';}?>
			 <?php if(isset($stock->stock_status)){ $stock_status= $stock->stock_status;}else { $stock_status= '';}?>
			 <?php if(isset($stock->qty)){ $stock_qty= $stock->qty;}else { $stock_qty= '';}?>
               <tr> <th>Product Name</th><td>{{$products->name}}</td> </tr>
			    <tr> <th>Product Weight</th><td>{{$products->weight}}</td> </tr>
                 <tr> <th>Meta Keyword</th><td>{{$keyword}}</td> </tr>
                 <tr> <th>Meta Description</th><td>{{$meta_description}}</td> </tr>

                 <tr> <th>Sale Price</th><td>&#8377; {{productSalePrice($products->id)}}</td> </tr>
                 <tr> <th>MRP</th><td>&#8377; {{productBasePrice($products->id)}}</td> </tr>
                 <tr> <th>Stock Status</th><td><?php echo $stock_status=='1' ? 'In Stock' : 'Out of Stock';?></td> </tr>
				
                
                <tr> <th>Quantity</th><td>{{$stock_qty}}</td> </tr>
                 <tr><th>Category</th><td>{{$catname}}</td> </tr>
				 <tr><th>Seller</th><td>{{@$user->name}}</td> </tr>
                 <tr><th>Description</th><td>
				 <textarea id="description" class="form-control ckeditor" readonly>{{$products->description}}</textarea> 
				 </td> </tr>
			  <tr><th>Images</th><td>
			  <?php
			  if(!empty($images)){
			  foreach ($images as $value) {
				  //$uploadpath = $_SERVER['HTTP_HOST'].'/citymarket/public/images';
				   //$uploadpath = "{{ URL::to('/') }}/images/"
							//echo $uploadpath; echo '/'; echo $value;
						//echo $value;	
					?> 
					 <img src="{{ URL::to('/') }}/public/images/products/{{$products->id}}/thumb/{{$value}}"  >
					<?php }
					}
			  ?>
			  
				</td> </tr>
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