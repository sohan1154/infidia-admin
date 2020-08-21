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
            <?php $segmentchild = Request::segment(2);?>
            <li class="breadcrumb-item active">{{ $segmentchild== 'delivery_boy' ? 'Delivery Boy' : ucfirst($segmentchild) }} Users</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            
            <div class="card-header">
              <i class="fas fa-user"></i>
              {{ $segmentchild== 'delivery_boy' ? 'Delivery Boy' : $segmentchild }} Users
			      </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Store ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Mobile Number</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>Store ID</th>
					            <th>Name</th>
                      <th>Email</th>
                      <th>Mobile Number</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    @php ($i = 1)
				            @foreach($users as $user)
					          @if($user['role']==$role)
                    <tr>
                      <td>00{{$user['id']}}</td>
                      <td>{{$user['name']}}</td>
                      <td>{{$user['email']}}</td>
                      <td>{{$user['mobile']}}</td>
                      <!--<td>@if($user['status'] == 1) Enable @else Disable @endif</td>-->
                      <td>
                      @if($user->role == 'seller') 
                        <a title="Products" href="{{ url('products?userid='.base64_encode($user->id))}}"><i class="fa fa-list " aria-hidden="true"></i></a>  
                      @endif    
                        <a title="Orders" href="{{ url('orders?userid='.base64_encode($user->id))}}"><i class="fa fa-luggage-cart " aria-hidden="true"></i></a>  
                        <a title="Returned/Cancelled Orders" href="{{ url('returned-orders?userid='.base64_encode($user->id))}}"><i class="fa fa-undo " aria-hidden="true"></i></a>  
                        <a title="View" href="{{ url('users/'.$role.'/view/'.base64_encode($user->id))}}"><i class="fa fa-eye " aria-hidden="true"></i></a>  
                      @if($user->status == 1) 
                        <a title="Change Status" href="{{ url('users/status/'.base64_encode($user->id).'/0/'.$role)}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                      @else
                        <a title="Change Status" href="{{ url('users/status/'.base64_encode($user->id).'/1/'.$role)}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                      @endif 
                      <a title="Edit" href="{{ url('users/'.$role.'/edit/'.base64_encode($user->id))}}"><i class="fa fa-edit " aria-hidden="true"></i></a>
                      <a title="Delete" href="{{ url('users/delete/'.base64_encode($user->id).'/'.$role)}}" onclick="return myFunction()"><i class="fas fa-trash" aria-hidden="true"></i></a>
                      @if(!$user->is_verified) 
                        <a title="Verifiy" href="{{ url('users/verify/'.base64_encode($user->id))}}" class="btn btn-danger btn-circle">Vefify</a>
                      @endif   
					           </td>
                    </tr>
					          @endif
                    @php ($i++)
					          @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        
        </div>
        <!-- /.container-fluid -->
		
		    <script>
          function myFunction() {
            if(confirm("Are you sure you want to delete this User ?")){

            }else{
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')