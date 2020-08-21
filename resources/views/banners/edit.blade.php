@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <!-- Breadcrumbs-->
      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
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
        <li class="breadcrumb-item active">Edit Banner</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Edit Banner
        </div>
        <div class="card-body">
        <form id="bannerEditForm" action="{{ url('/banners/edit/'.base64_encode($banners->id)) }}" enctype="multipart/form-data" method="post">
        @csrf
            
        <div class="col-md-6">
          <div class="form-group">
            <div class="form-label-group">
              <input type="text" id="banner_name" name="banner_name" value="{{ $banners->banner_name }}" class="form-control" placeholder="Banner Name">
              <label for="product_name">Title</label>
            </div>
          </div>
          <!-- <div class="form-group">
            <div class="form-label-group">
              <textarea id="description" name="banner_description" class="form-control" placeholder="Banner Description" >{{ $banners->banner_description }}</textarea>
            </div>
          </div> -->
				</div>

        <div class="col-md-6">                  
          <div class="form-group">
            <div class="form-label-group">
	            @if($banners->banner_image!='') 
                 <img src="{{url('/').'/images/banners/'.$banners->banner_image}}" width="150px;">
              @endif
              <input type="file" id="banner_image" name="banner_image" value="{{ $banners->banner_image }}" class="form-control" accept="image/*">
            </div>
          </div>

          <div class="form-group">
            <div class="form-label-group">
              <input type="text" id="external_link" name="external_link" class="form-control" placeholder="External Link" value="{{ $banners->external_link }}" >
              <label for="external_link">External Link</label>
              <small>ex. http://exmaple.com this is optional</small>
            </div>
          </div>

          <div class="form-group">
            <div class="form-label-group">
              <select id="user_id" name="user_id" class="form-control chosen-select"  >
                <option value="">Select Seller</option>
                @foreach($users as $id=>$name)
                  <option value = {{ $id }} {{ $banners->user_id==$id ? 'selected' : '' }} >{{ $name }}</option>
                @endforeach
              </select>                    
            </div>
          </div>

          <div class="form-group">
            <div class="form-label-group">
              <select id="status" name="status" class="form-control">
                  <option value="">Select Status</option>
                  <option value="1" {{ $banners->status=='1' ? 'selected' : '' }} >Enable</option>
                  <option value="0" {{ $banners->status=='0' ? 'selected' : '' }} >Disable</option>
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