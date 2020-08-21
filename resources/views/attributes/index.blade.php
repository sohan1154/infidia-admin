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
            <li class="breadcrumb-item active">Attributes</li>
          </ol>

          <!-- DataTables Example -->
          <div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Attributes
			  <a href="{{ url('attributes/create')}}"><button type="button" class="btn btn-primary add-button">Add New Attribute</button></a>
			</div>
            <div class="card-body">
              <div class="table-responsive">
              

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th colspan="4">Category wise Attributes</th>
                    </tr>
                  </thead>
                  <!-- <tfoot>
                    <tr>
                      <th colspan="4">Attributes</th>
                    </tr>
                  </tfoot> -->
                  <tbody>

                    @if(!empty($categories))  
                    @php ($i = 1)    
                    @foreach($categories as $key => $category)

                      <tr onclick="toggleCategory('{{$key}}')" style="cursor: pointer;">
                        <td colspan="4">{{$category->name}}</td>
                      </tr>

                      @foreach($category->attributes as $conter => $attribute)
                        @if($conter==0)
                        <tr class="collapse-box-{{$key}} attribute-row" style="background-color:#eee;">
                          <th>ID</th>
                          <th>Attribute</th>
                          <th>Category</th>
                          <th>Action</th>
                        </tr>
                        @endif
                        <tr class="collapse-box-{{$key}} attribute-row" style="background-color:#eee;">
                          <td>{{$conter + 1}}</td>
                          <td>{{$attribute->name}}</td>
                          <td>{{$attribute->category->name}}</td>
                          <td>
                            @if($attribute->status == 1) 
                              <a title="Change Status" href="{{ url('attributes/status/'.base64_encode($attribute->id).'/0')}}"><i class="fa fa-check " aria-hidden="true"></i></a>
                            @else
                              <a title="Change Status" href="{{ url('attributes/status/'.base64_encode($attribute->id).'/1')}}"><i class="fa fa-times " aria-hidden="true"></i></a>  
                            @endif  
                            <a title="Edit" href="{{ url('attributes/update/'.base64_encode($attribute->id))}}"><i class="fa fa-edit " aria-hidden="true"></i></a>
                            <a href="{{ url('delete-attribute/'.base64_encode($attribute->id))}}" onclick="return myFunction()"><i class="fa fa-trash" aria-hidden="true"></i></a>
                          </td>
                        </tr>
                        @php ($i++)  
                      @endforeach
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

          // $('#dataTable').DataTable({
          //     "ordering": false
          // });

          $(document).ready(function(){
              $(".attribute-row").hide();
          });

          function toggleCategory(id) {

            $('.collapse-box-' + id).toggle();
          }

          function myFunction() {
            if(confirm("Are you sure you want to delete Attribute ?")){

            } else {
              return false;
            }
          }
        </script>

        <!-- Sticky Footer -->
@include('layouts.admin_footer')