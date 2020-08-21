@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <!-- Breadcrumbs-->
      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
        </div>
      @endif
	  @if ($errors->has('title'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('title') }}
          </div>
      @endif
	  @if ($errors->has('description'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('description') }}
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
        <li class="breadcrumb-item active">Edit Page</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Edit Page
        </div>
        <div class="card-body">
          <form action="{{ url('/pages/edit/'.base64_encode($pages->id)) }}" enctype="multipart/form-data" method="post"  id="cmsForm">
        @csrf
            
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="form-label-group">
                      <input type="text" id="title" name="title" value="{{ $pages->title }}" class="form-control" placeholder="Page Name">
                      <label for="product_name">Page Name</label>
                    </div>
                  </div>
				          <div class="form-group">
                    <div class="form-label-group">
				              <textarea id="cms_description" name="description" class="form-control ckeditor" placeholder="Page Description" >{{ $pages->description }}</textarea>
                    </div>
                  </div>
				        </div>

                <div class="col-md-6">                  
                  <div class="form-group">
                    <div class="form-label-group">
                     @if($pages->image!='') 
					           <img src="{{url('/').'/images/'.$pages->image}}" width="150px;">
                     @endif
                      <input type="file" id="image" name="image" value="{{ $pages->image }}" class="form-control" accept="image/*">
                      
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="form-label-group">
                      <select id="status" name="status" class="form-control">
                          <option value="">Select Status</option>
                          <option value="1" {{ $pages->status=='1' ? 'selected' : '' }} >Enable</option>
                          <option value="0" {{ $pages->status=='0' ? 'selected' : '' }} >Disable</option>
                      </select>
                    </div>
                  </div>
                </div>
              
            <div class="col-md-6 form-group">
               <input type="submit" class="btn btn-primary btn-block" value="Save">
            </div>

          </form>
        </div>
      </div> 

    </div>
    
    <!-- Sticky Footer -->
@include('layouts.admin_footer')
<script type="text/javascript">
    CKEDITOR.replace( 'cms_description',
    {
      customConfig : 'config.js',
      toolbar : 'simple',
      maxlength : 75,
      filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
      filebrowserUploadMethod: 'form'
    });
</script> 