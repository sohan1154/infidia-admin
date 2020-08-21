@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <!-- Breadcrumbs-->
      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
        </div>
      @endif
	    @if ($errors->has('banner_image'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('banner_image') }}
          </div>
      @endif
	    @if ($errors->has('status'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('status') }}
          </div>
      @endif
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
          <a href="{{url('/banners')}}">Banners</a>
        </li>
        <li class="breadcrumb-item active">Add Banner</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Add Banner
  			</div>
      <div class="card-body">
      <form id="bannerForm" action="{{ url('banners/add') }}" enctype="multipart/form-data" method="post">
			  @csrf
            
        <div class="col-md-6">
	        <div class="form-group">
            <div class="form-label-group">
              <input type="text" id="banner_name" name="banner_name" class="form-control" placeholder="Banner Name" value="{{old('banner_name')}}" autofocus >
              <label for="banner_name">Title</label>
            </div>
          </div>
				  
				  <!-- <div class="form-group">
            <div class="form-label-group">
              <textarea id="banner_description" name="banner_description" class="form-control" placeholder="Banner Description" maxlength="75" ></textarea> 
              <em for="role" generated="true"  id="letterCount" class="error help-block hide"></em>           
            </div>
          </div> -->
      	</div>

        <div class="col-md-6">
          <div class="form-group">
            <div class="form-label-group">
              <input type="file" id="banner_image" name="banner_image" class="form-control" >
              <label for="banner_image">Banner Image</label>
            </div>
          </div>

          <div class="form-group">
            <div class="form-label-group">
              <input type="text" id="external_link" name="external_link" class="form-control" placeholder="External Link" value="{{old('external_link')}}" >
              <label for="external_link">External Link</label>
            </div>
            <small>ex. http://exmaple.com this is optional</small>
          </div>

          <div class="form-group">
            <div class="form-label-group">
              <select id="user_id" name="user_id" class="form-control chosen-select"  >
                <option value="">Select Seller</option>
                @foreach($users as $id=>$name)
                  <option value = {{ $id }} >{{ $name }}</option>
                @endforeach
              </select>                    
            </div>
          </div>

          <div class="form-group">
            <div class="form-label-group">
              <select id="status" name="status" class="form-control">
                  <option value="">Select Status</option>
                  <option value="1" selected>Enable</option>
                  <option value="0">Disable</option>
              </select>
            </div>
          </div>
        </div>
              
        <div class="col-md-6 form-group">
           <input type="submit" class="btn btn-primary btn-block" value="Submit">
        </div>

      </form>
    </div>
    </div> 

    </div>

    <!-- Sticky Footer -->
  @include('layouts.admin_footer')
  <script type="text/javascript">
    CKEDITOR.replace( 'banner_description',
    {
      customConfig : 'config.js',
      toolbar : 'simple',
      maxlength : 75
    });
  </script>