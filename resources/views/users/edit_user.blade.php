@include('layouts.admin_header')
<div id="content-wrapper">
  <div class="container-fluid">

      @if(session()->has('alert-danger'))
      <div class="alert alert-danger">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
      </div>
    @endif

        <!-- Breadcrumbs-->
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{url('/home')}}">Dashboard</a>
      </li>
      <li class="breadcrumb-item active">
        <?php $segmentchild = Request::segment(2);?>
        <a href="{{url('/users/'.$segmentchild)}}">{{ $segmentchild== 'delivery_boy' ? 'Delivery Boy' : ucfirst($segmentchild) }} Users</a>
      </li>
      <li class="breadcrumb-item active">Edit Users</li>
    </ol>
    <!-- DataTables Example -->
    <div class="card mb-3">
      <div class="card-header">
        <i class="fas fa-user"></i> Edit Users
			</div>
      <div class="card-body">
        <form action="{{ url('update-user/'.$user->id.'/'.$user['role']) }}" method="post" id="UserForm"  enctype="multipart/form-data" >
		    @csrf
          <div class="col-md-6">
            <div class="form-group">
              <div class="form-label-group">
                <input type="text" id="user_name" name="name" value="{{$user['name']}}" class="form-control" placeholder="User Name">
                <label for="user_name">User Name</label>
              </div>
            </div>
            <div class="form-group">
              <div class="form-label-group">
                <input type="file" id="image" name="image" class="form-control" >
                <input type="hidden" name="old_image" class="form-control" value="{{userProfilePath($user->id)}}">
                <label for="image">User Image</label>
              </div>
              <img src="{{userProfile($user->id)}}" width="200">
            </div>
            <div class="form-group">
              <div class="form-label-group">
                <input type="textarea" id="role" value="{{$user['role']}}" name="" class="form-control" placeholder="Role" readonly="readonly">
                <label for="role">Role</label>
              </div>
            </div>
		        <div class="form-group">
              <div class="form-label-group">
                <input type="text" id="email" name="" value="{{$user['email']}}" class="form-control" placeholder="User Email"  readonly="readonly">
                <label for="user_name">User Email</label>
              </div>
            </div>
            <div class="form-group">
               <input type="submit" class="btn btn-primary btn-block" value="Save">
            </div>
          </div>
        </form>
      </div>
    </div> 
  </div>
</div>    
<!-- /.container-fluid -->
<!-- Sticky Footer -->
@include('layouts.admin_footer')