@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <!-- Breadcrumbs-->
      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
        </div>
      @endif

      @if($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger">             
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ $error }}</div>
        @endforeach
      @endif

      <ol class="breadcrumb">
      <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
          <a href="{{url('/attributes')}}">Attributes</a>
        </li>
        <li class="breadcrumb-item active">Edit Attribute</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Edit Attribute
        </div>
        <div class="card-body">
          <form  id="attrForm" action="{{ url('/attributes/edit/'.base64_encode($attributes->id)) }}" enctype="multipart/form-data" method="post">
            
            @csrf
            
            <div class="col-md-6">
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="name" name="name" value="{{ $attributes->name }}" class="form-control" placeholder="Attribute Name" >
                  <label for="name">Attribute Name</label>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <div class="form-label-group">
                  <select id="category_id" name="category_id" class="form-control chosen-select" >
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                      <option value = {{ $category->id }} {{ $category->id==$attributes->category->id ? 'selected' : '' }} >{{ $category->name }}</option>
                    @endforeach
                  </select>                    
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <div class="form-label-group">
                  <select id="status" name="status" class="form-control"  required="required">
                      <option value="">Select Status</option>
                      <option value="1" {{ $attributes->status=='1' ? 'selected' : '' }} >Enable</option>
                      <option value="0" {{ $attributes->status=='0' ? 'selected' : '' }} >Disable</option>
                  </select>
                </div>
              </div>
            </div>
          
            <div class="col-md-6 form-group">
              <input type="submit" class="btn btn-primary btn-block" value="Save">
            </div>

          </form>
        </div>
      </div> 

    </div>
    
    <!-- Sticky Footer -->
@include('layouts.admin_footer')
<script>
var count = 1;
function type_select(val){
	var text_box = "";
	//alert(val);
	if(val == "dropdown"){
		$('#options_div').css("display",'block');
	}
	else{
		$('#options_div').css("display",'none');
	}
	
}
function add_more_option(){
	count++;
	var text_box = "<div class='form-group'><div class='form-label-group'><input type='text' id='option_"+count+"' name='attribute_option[]' class='form-control'></div></div>";
	$('#select_option').append(text_box);
	console.log("add"+count);
	
}
function remove_more_option(){
	
	$('#option_'+count).remove();
	count--;
	console.log("remove"+count);
}

$(document).ready(function(){
	//alert($("#type").val());
  if($("#type").val()=='dropdown'){
	  $('#options_div').css("display",'block');
  }
});

</script>
<style>
.add-button-new, .add-button-new:hover {
    border: 0;
    padding: 5px 10px !important;
    margin: 0 0 10px 5px;  
}
.mB10 {
	margin-bottom:1rem;
}
</style>