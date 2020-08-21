@include('layouts.admin_header')
<div id="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">
          <a href="{{url('/feedbacks')}}">Feedbacks</a>
        </li>
        <li class="breadcrumb-item active">Details</li>
      </ol>

      <!-- DataTables Example -->
    <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Feedback Details 
        </div>
        <div class="card-body">
            <div class="col-lg-12">
	            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		            <tbody>
		                <tr><th>User Name</th><td>{{$rowInfo->user->name}}</td></tr>

		                <tr><th>Feedback Message</th><td>{{$rowInfo->description}}</td></tr>

		                <tr><th>Status</th><td>{{getStatus($rowInfo->status)}}</td></tr>

		                <tr><th>Status</th><td>{{formatedDate($rowInfo->created_at)}}</td></tr>

		                <tr><th>Status</th><td>{{formatedDate($rowInfo->updated_at)}}</td></tr>
		            </tbody>
	            </table>  
            </div>
        </div>
      </div> 
    </div> 	
</div>

<!-- Sticky Footer -->
@include('layouts.admin_footer')