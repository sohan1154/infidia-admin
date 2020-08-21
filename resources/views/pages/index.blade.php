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
            <li class="breadcrumb-item active">Pages</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Pages
			  <a href="{{ url('pages/create')}}"><button type="button" class="btn btn-primary add-button">Add New Page</button></a>
			</div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Page Name</th>
                     <th>Page Description</th>
                      
                      
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
					  <th>ID</th>
                      <th>Page Name</th>
                     <th>Page Description</th>
                     
                      
                      <th>Action</th>
                    </tr>
                  </tfoot>
                  <tbody>
                   @if(!empty($pages))  
                   @php ($i = 1)  
				           @foreach($pages as $page)
                    <tr>
                      <td>{{$i}}</td>
                      <td>{{$page->title}}</td>
                      <td><?php echo html_entity_decode($page->description);?></td>
					 
                     
					  
					  
					            <td>
                      @if($page->status == 1) 
                          <a title="Change Status" href="{{ url('pages/status/'.base64_encode($page->id).'/0')}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                          @else
                          <a title="Change Status" href="{{ url('pages/status/'.base64_encode($page->id).'/1')}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                          @endif  
          						<a title="Edit" href="{{ url('pages/update/'.base64_encode($page->id))}}"><i class="fa fa-edit " aria-hidden="true"></i></a>
          						<a title="Delete" href="{{ url('delete-page/'.base64_encode($page->id))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
            if(confirm("Are you sure you want to delete this Page ?")){

            } else {
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')