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
            <li class="breadcrumb-item active">Banners</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Banners
      			  <a href="{{ url('banners/create')}}"><button type="button" class="btn btn-primary add-button">Add New Banner</button></a>
      			</div>
            <div class="card-body">
              <div class="table-responsive">
                
                <!-- bulk action options -->
                <!-- <div class="row"> -->
                  <!-- <div class="input-group row">
                    <select id="bulk_action" name="bulk_action">
                      <option value="active">Active</option>
                      <option value="deactive">Deactive</option>
                      <option value="delete">Delete</option>
                    </select>

                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button" id="bulk_submit_button" name="bulk_submit_button">Submit</button>
                    </div>
                  </div> -->
                <!-- </div> -->

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Title</th>
                      <th>Banner Relation</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					            <th>ID</th>
                      <th>Title</th>
                      <th>Banner Relation</th>
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  @if(!empty($banners))  
                  @php ($i = 1)                    
				          @foreach($banners as $banner)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$banner->banner_name}}</td>
                      <td>
                        {{@$banner->external_link}}
                        {{@$banner->user->name}}
                      </td>                      
                      <td>
                        @if($banner->status == 1) 
                            <a title="Change Status" href="{{ url('banners/status/'.base64_encode($banner->id).'/0')}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                        @else
                            <a title="Change Status" href="{{ url('banners/status/'.base64_encode($banner->id).'/1')}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                        @endif  
                        <a title="Edit" href="{{ url('banners/update/'.base64_encode($banner->id))}}"><i class="fa fa-edit " aria-hidden="true"></i></a>
                        <a title="Delete" href="{{ url('delete-banner/'.base64_encode($banner->id))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <!-- <input type="checkbox" name="ids[]" /> -->
                      </td>
					  
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
		
		    <script>
          function myFunction() {
            if(confirm("Are you sure you want to delete this Banner ?")){

            } else {
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')