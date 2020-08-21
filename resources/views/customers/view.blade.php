@include('layouts.admin_header')
<div id="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">
          <a href="{{url('/customers')}}">Customers</a>
        </li>
        <li class="breadcrumb-item active">Details</li>
      </ol>

      <!-- DataTables Example -->
    <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Customer Details 
        </div>
        <div class="card-body">
            <div class="col-lg-12">
	            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
		            <tbody>
		                <tr><th>Name</th><td>{{$users->name}}</td></tr>
		                <tr><th>Email</th><td>{{$users->email}}</td></tr>
		                <tr><th>Mobile</th><td>{{$users->mobile}}</td></tr>
                    <tr><th>Address</th><td>
                      @if(count($userAddress)>0)
                      @foreach($userAddress as $key => $address)
                      @if($users->role=='Seller')
                      {{$users->shop_address.' '.$users->city.' '.$users->state.' '.$users->country}}
                      @elseif($users->role=='Buyer')
                      {{$address->type.' :  '.$address->location.' '.$address->house_no.', '.$address->address.',  '.$address->landmark}}
                      @else
                      {{$address->location.' '.$address->address}}
                      <br>
                      @endif
                      @endforeach
                      @endif
                      </td>
                    </tr>
                    <tr><th>Registerd By</th><td>{{$users->fb_id!='' ? 'Facebook' : 'App'}}</td></tr>
                    <tr><th>Image</th><td><img src="{{userProfile($users->id)}}" width="200"></td></tr>
		            </tbody>
	            </table>  
            </div>
		</div>
    </div> 
    </div> 	
</div>    
    <!-- Sticky Footer -->
@include('layouts.admin_footer')