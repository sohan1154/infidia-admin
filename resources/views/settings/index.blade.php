@include('layouts.admin_header')
 
<div id="content-wrapper">
  <div class="container-fluid">
      <!-- Breadcrumbs-->
      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
        </div>
      @endif
      @if(session()->has('alert-success'))
        <div class="alert alert-success">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-success') }}
        </div>
      @endif
     

      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Setting</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Setting
        </div>
        <div class="card-body">
          <form action="{{ url('/settings-update') }}" enctype="multipart/form-data" method="post">
           @csrf 
            <div class="col-lg-12">
              <table class="table table-bordered" id="" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Option Name</th>
                      <th>Option Value</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th>Option Name</th>
                      <th>Option Value</th>
                    </tr>
                  </tfoot>
                  <tbody>
				          @foreach($settings as $option)
                    <tr>
                      <td>{{$option->option_name}}</td>
					            <td><input type="hidden" name="option_id[]" class="form-control" value="{{$option->id}}">
                      <input type="{{$option->option_type}}" name="option_value[]" class="form-control" placeholder="Option Value" value="{{$option->option_value}}">
                        @if($option->option_type=='file' && $option->option_value!='')
                           <img src="{{url('/')}}/images/{{$option->option_value}}" width="120">
                        @endif
                      </td>
                    </tr>
					        @endforeach
                  </tbody>
              </table>
              <div class="form-group">
                <div class="form-group">
                  <input type="submit" class="btn btn-primary btn-block" value="Save">
                </div>
              </div>          
            </div>
          </form>
        </div>
      </div>
  </div>
</div>
<!-- Sticky Footer -->
@include('layouts.admin_footer')