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
              <a href="{{url('/business-categories')}}">Business Categories</a>
            </li>
            <li class="breadcrumb-item active">Business Product Categories</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Business Product Categories of ({{$sub_category->name}})
			  <a href="{{ url('add-business-product-categories').'/'.base64_encode($sub_category->id)}}"><button type="button" class="btn btn-primary add-button">Add New Business Product Categories</button></a>
			</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Category Name</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>ID</th>
                      <th>Category Name</th>
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
                      <td>
                        <a title="Delete" href="{{ url('delete-business-product-category/'.base64_encode($sub_category->id).'/'.base64_encode($category->id))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
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