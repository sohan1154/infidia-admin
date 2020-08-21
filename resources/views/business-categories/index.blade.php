@include('layouts.admin_header')
<?php $role = Auth::user()->role;?>
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
            <li class="breadcrumb-item active">Business Categories</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Categories
			  <a href="{{ url('add-business-categories')}}"><button type="button" class="btn btn-primary add-button">Add Main Category</button></a>
			</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Category Name</th>
                      <th>Parent Category</th>
                      <th>Image</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>ID</th>
                      <th>Category Name</th>
                      <th>Parent Category</th>
                      <th>Image</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    
                    @if(!empty($categories))
                    @php ($i = 1)
				            @foreach($categories as $category)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$category->category->name}}</td>
                      <td>{{@$category->parentCategory->category->name}}</td>
                      <td><img src="<?php echo CategoryImage($category->category->id);?>" width="80"></td>
                      <td>
                        @if($category->parent_id != 0 && $role == 'seller')
                          <a title="Business Product Categories" href="{{ url('business-product-categories/'.base64_encode($category->category->id))}}"><i class="fa fa-list " aria-hidden="true"></i></a>
                        @endif 
                        @if(!$category->parent_id)
                          <a title="Add Sub Categories" href="{{ url('add-business-sub-categories/'.base64_encode($category->id))}}"><i class="fa fa-plus " aria-hidden="true"></i></a>
                        @endif
                        <a title="Delete" href="{{ url('delete-business-category/'.base64_encode($category->id))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            if(confirm("Are you sure you want to delete this category ?")){

            }else{
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')