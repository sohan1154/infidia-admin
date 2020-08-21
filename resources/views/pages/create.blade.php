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
      <li class="breadcrumb-item active">Add Page</li>
    </ol>
    <!-- DataTables Example -->
    <div class="card mb-3">
      <div class="card-header">
        <i class="fas fa-table"></i> Add Page
			</div>
      <div class="card-body">
        <form id="cmsForm" action="{{ url('pages/add') }}" enctype="multipart/form-data" method="post" >
		      @csrf          
          <div class="col-md-6">
		        <div class="form-group">
              <div class="form-label-group">
                <input type="text" id="title" name="title" class="form-control" placeholder="Page Name" >
                <label for="title">Page Name</label>
              </div>
            </div>
	 
	          <div class="form-group">
              <div class="form-label-group">
                <textarea id="cms_description" name="description" class="form-control ckeditor" placeholder="Page Description" ></textarea>                    
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <div class="form-label-group">
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <label for="image">Page Image</label>
              </div>
            </div>
            <div class="form-group">
              <div class="form-label-group">
                <select id="status" name="status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="1">Enable</option>
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