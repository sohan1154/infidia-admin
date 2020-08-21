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
              <a href="{{url('/users/seller')}}">Sellers ({{$sellerName}})</a>
            </li>
            @endif
            <li class="breadcrumb-item active">Products</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Products
				<br>
			  <a href="{{ url('product/create?userid='.base64_encode($user_id))}}"><button type="button" class="btn btn-primary add-button">Add New Product</button></a>
			</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Product Name</th>
                      <th>SKU</th>
                      <th>Category</th>
                      <th>Sale Price</th>
                      <th>Qty</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>ID</th>
                      <th>Product Name</th>
                      <th>SKU</th>
                      <th>Category</th>
                      <th>Sale Price</th>
                      <th>Qty</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  @if(!empty($products))
                  @php ($i = 1)
				          @foreach($products as $product)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$product->name}}</td>
                      <td>{{$product->sku}}</td>
                      <td>{{$product->categories['name']}}</td>
                      <td>&#8377;{{$product->price['sale_price']}}</td>
                      <td>{{$product->stock['qty']}}</td>
					            <td>
                        <a title="View" href="{{ url('products/view/'.base64_encode($product->id))}}"><i class="fa fa-eye " aria-hidden="true"></i></a>
                        @if($product->status == 1) 
                          <a title="Change Status" href="{{ url('products/status/'.base64_encode($product->id).'/0')}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                        @else
                          <a title="Change Status" href="{{ url('products/status/'.base64_encode($product->id).'/1')}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                        @endif  
                        <a title="Edit" href="{{ url('product/update/'.base64_encode($product->id).'?userid='.base64_encode($user_id))}}"><i class="fa fa-edit " aria-hidden="true"></i></a>
                        <a title="Delete" href="{{ url('delete-product/'.base64_encode($product->id))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
          			      </td>
                    </tr>
					        @php ($i++)
                  @endforeach
                  @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        
        </div>
        <!-- /.container-fluid -->
		
		    <script>
          function myFunction() {
            if(confirm("Are you sure you want to delete Product ?")){

            } else {
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')