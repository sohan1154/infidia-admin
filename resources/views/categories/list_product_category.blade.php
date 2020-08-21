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
            <li class="breadcrumb-item">
              <a href="{{url('/categories')}}">Categories</a>
            </li>
            <li class="breadcrumb-item active">Product Categories</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Product Categories of ({{$parent_category->name}})
			  <a href="{{ url('add-product-category').'/'.$parent_id}}"><button type="button" class="btn btn-primary add-button">Add New Product Categories</button></a>
			</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Category Name</th>
                      <th>Parent Category</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>ID</th>
                      <th>Category Name</th>
                      <th>Parent Category</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    
                    @if(!empty($categories))
                    @php ($i = 1)
				            @foreach($categories as $category)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$category['name']}}</td>
                      <td>{{@$category->parentCategory->name}}</td>
                      <td>
                        @if($category['status'] == 1) 
                          <a title="Change Status" href="{{ url('category/status/'.base64_encode($category['id']).'/0')}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                        @else
                          <a title="Change Status" href="{{ url('category/status/'.base64_encode($category['id']).'/1')}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                        @endif 
						            <a title="Edit" href="{{ url('edit-product-category/'.$parent_id.'/'.base64_encode($category['id']))}}"><i class="fa fa-edit " aria-hidden="true"></i></a>
						            <a title="Delete" href="{{ url('delete-product-category/'.$parent_id.'/'.base64_encode($category['id']))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
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