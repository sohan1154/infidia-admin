  @include('layouts.admin_header')
    
    <div id="content-wrapper">

      <div class="container-fluid">
        <!-- Breadcrumbs-->
        @if(session()->has('alert-success'))
          <div class="alert alert-success">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-success') }}
          </div>
        @elseif(session()->has('alert-danger'))
          <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
          </div>
        @endif


        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{url('/home')}}">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">Import Products CSV</li>
        </ol>

        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-table"></i> Import Products CSV file (<a href="{{ url('sample-csv/sample.csv') }}">Download Sample File</a>)
          </div>
          <div class="card-body">
            <form action="{{ url('importcsv/add') }}" enctype="multipart/form-data" method="post" id="ImportCSV" autocomplete="off" >
              
              @csrf
            
              <div class="col-md-6">
              
                <div class="form-group <?php echo ($role=='seller') ? 'hide' : '' ?>">
                  <div class="form-label-group">
                    <select id="seller_id" name="seller_id" class="form-control chosen-select" required>
                      <?php if($role!='seller') { ?>
                      <option value="">Select Seller</option>
                      <?php } ?>
                      @foreach($sellers as $value)
                        <option value = {{ $value->id }} >{{ $value->name }}</option>
                      @endforeach
                    </select>                    
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="file" id="csvfile" name="csvfile" class="form-control" required>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-block" value="Submit">
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div> 

      </div>
   

  <!-- Sticky Footer -->
  @include('layouts.admin_footer')