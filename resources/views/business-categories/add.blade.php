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
          <a href="{{url('/business-categories')}}">Categories</a>
        </li>
        <li class="breadcrumb-item active">Add Categories</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Add Categories {{$category_name}}
  			</div>
        <div class="card-body">
          <form  id="catForm" action="{{ url('create-business-categories') }}" enctype="multipart/form-data" method="post">
			      
            @csrf
            
            <div class="col-md-6">

              <div class="form-group">
                <div class="form-label-group">
                  <select id="category_ids" name="category_ids[]" class="form-control chosen-select" multiple data-placeholder="Select Category">
                    @foreach($categories as $category)
                      <option value = {{ $category->id }} >{{ $category->name }}</option>
                    @endforeach
                  </select>                    
                </div>
              </div>

              <input type="hidden" name="parent_id" value="{{$parent_id}}">
          
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