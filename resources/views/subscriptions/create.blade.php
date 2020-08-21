@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <!-- Breadcrumbs-->
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
      @if ($errors->has('price'))
          <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>{{ $errors->first('price') }}
          </div>
      @endif

      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Add Subscription Plan</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Add Subscription Plan
  			</div>
        <div class="card-body">
          <form id="planForm" action="{{ url('subscriptions/add') }}" enctype="multipart/form-data" method="post" >
            @csrf          
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="name" name="name" class="form-control" placeholder="Plan Name" >
                  <label for="name">Plan Name</label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="number" id="price" name="price" class="form-control" placeholder="Price($)" min="0" >
                  <label for="price">Price($)</label>
                </div>
              </div>
     
              <div class="form-group">
                <div class="form-label-group">
                  <textarea id="plan_description" name="feature" class="form-control ckeditor" placeholder="Plan Feature" ></textarea>                    
                </div>
              </div>
            </div>
            <div class="col-md-6">
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
    <!-- Sticky Footer -->
@include('layouts.admin_footer')
<script type="text/javascript">
    CKEDITOR.replace( 'plan_description',
    {
      customConfig : 'config.js',
      toolbar : 'simple',
      maxlength : 75
    });
</script> 