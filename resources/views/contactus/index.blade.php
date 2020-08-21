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
            <li class="breadcrumb-item active">Contact US</li>
          </ol>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Message</th>
                    </tr>
                  </thead>

                  <tbody>
                   @if(!empty($list))  
                   @php ($i = 1)  
				           @foreach($list as $value)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$value->first_name}}</td>
                      <td>{{$value->last_name}}</td>
                      <td>{{$value->email}}</td>
                      <td><?php echo html_entity_decode($value->message);?></td>
                    </tr>
					          @php ($i++)  
                    @endforeach
                    @endif
                  </tbody>

                </table>
              </div>
            </div>
          </div>

        
        </div>
        <!-- /.container-fluid -->

        <!-- Sticky Footer -->
        @include('layouts.admin_footer')