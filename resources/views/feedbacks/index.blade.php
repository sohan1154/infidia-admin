@include('layouts.admin_header')
      <div id="content-wrapper">

        <div class="container-fluid">

          <!-- Breadcrumbs-->
          @if(session()->has('alert-success'))
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-success') }}
            </div>
          @elseif(session()->has('error'))
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('error') }}
            </div>
          @endif
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="{{url('/home')}}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Feedbacks</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            
            <div class="card-header">
              <i class="fas fa-user"></i>
              Feedbacks
			      </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>User Name</th>
                      <th>Feedback Message</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>User Name</th>
                      <th>Feedback Message</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
				            @foreach($results as $value)
                    <tr>
                      <td>{{$value->user->name}}</td>
                      <td>{{$value->description}}</td>
                      <td>
                      @if($value->status == 1) 
                        <a title="Change Status" href="{{ url('feedbacks/status/'.base64_encode($value->id).'/0')}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                      @else
                        <a title="Change Status" href="{{ url('feedbacks/status/'.base64_encode($value->id).'/1')}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                      @endif 
                      <a title="View" href="{{ url('feedbacks/view/'.base64_encode($value->id))}}"><i class="fas fa-eye" aria-hidden="true"></i></a>
                      <a title="Delete" href="{{ url('feedbacks/delete/'.base64_encode($value->id))}}" onclick="return myFunction()"><i class="fas fa-trash" aria-hidden="true"></i></a>
					           </td>
                    </tr>
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
            if(confirm("Are you sure you want to delete this record ?")){

            }else{
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')