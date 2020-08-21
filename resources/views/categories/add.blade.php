@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">

      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
        </div>
      @endif
	  @if ($errors->has('name'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('name') }}
          </div>
		  @endif
		  @if ($errors->has('status'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('status') }}
          </div>
	  @endif
          <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
          <a href="{{url('/categories')}}">Categories</a>
        </li>
        <li class="breadcrumb-item active">Add Categories</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Add Categories
  			</div>
        <div class="card-body">
          <form  id="catForm" action="{{ url('create-category') }}" enctype="multipart/form-data" method="post">
			      
            @csrf
            
            <div class="col-md-6">
              
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="category_name" name="name" class="form-control" placeholder="Category Name" autofocus >
                  <label>Category Name</label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="file" id="image" name="image" class="form-control"  accept="image/*">
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <select id="parent_id" name="parent_id" class="form-control chosen-select" data-placeholder="Select Parent Category">
                    <option>Select Parent Category</option>
                    @foreach($categories as $category)
                      <option value = {{ $category->id }} >{{ $category->name }}</option>
                    @endforeach
                  </select>                    
                </div>
              </div>
              
              <div class="form-group">
                <div class="form-label-group">
                  <select id="status" name="status" class="form-control" >
          						<option value="">Select Status</option>
          						<option value="1" selected>Enable</option>
          						<option value="0">Disable</option>
          				</select>
                </div>
              </div>

			        <input type="hidden" name="is_deleted" value="0">
			        <input type="hidden" name="type" value="business">
              
              <div class="form-group">
                 <input type="submit" class="btn btn-primary btn-block" value="Submit">
              </div>
  		      </div>
          </form>
        </div>
      </div> 

    </div>
        <!-- /.container-fluid -->
		
		

        <!-- Sticky Footer -->
@include('layouts.admin_footer')