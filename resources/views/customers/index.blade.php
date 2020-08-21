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
            <li class="breadcrumb-item active">Customers</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            
            <div class="card-header">
              <i class="fas fa-user"></i>
              Customers
			      </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Mobile Number</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>ID</th>
					            <th>Name</th>
                      <th>Email</th>
                      <th>Mobile Number</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    @php ($i = 1)
				            @foreach($customers as $user)
                    <tr>
                      <td>{{$user['id']}}</td>
                      <td>{{$user['name']}}</td>
                      <td>{{$user['email']}}</td>
                      <td>{{$user['mobile']}}</td>
                      <td>
                        <a title="Orders" href="{{ url('customers/orders/'.base64_encode($user->id))}}"><i class="fa fa-cart-arrow-down " aria-hidden="true"></i></a>  
                        <a title="View" href="{{ url('customers/view/'.base64_encode($user->id))}}"><i class="fa fa-eye " aria-hidden="true"></i></a>  
					           </td>
                    </tr>
                    @php ($i++)
					          @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        
        </div>
        <!-- /.container-fluid -->
		

        <!-- Sticky Footer -->
@include('layouts.admin_footer')