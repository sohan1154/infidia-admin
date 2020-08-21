  @include('layouts.admin_header')
    
    <div id="content-wrapper">

      <div class="container-fluid">
        <!-- Breadcrumbs-->
        <!-- @if(session()->has('alert-danger'))
          <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
          </div>
        @endif -->      
        @if ($errors->has('user_id'))
          <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>The Seller Name field is required</div>
        @elseif($errors->has('name'))
          <div class="alert alert-danger">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>The Product Name field is required</div>
        @else      
          @if($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ $error }}</div>
            @endforeach
          @endif
        @endif

        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{url('/home')}}">Dashboard</a>
          </li>
          @if(Auth::user()->role=='Admin')
            <li class="breadcrumb-item">
              <a href="{{url('/users/seller')}}">Sellers ({{$sellerName}})</a>
            </li>
            <li class="breadcrumb-item active">
              <a href="{{url('/products?userid='.base64_encode($user_id))}}">Products</a>
            </li>
          @else
            <li class="breadcrumb-item active">
              <a href="{{url('/products')}}">Products</a>
            </li>
          @endif
          <li class="breadcrumb-item active">Add Products</li>
        </ol>

        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-table"></i> Add Products
          </div>
          <div class="card-body">
            <form action="{{ url('product/add') }}" enctype="multipart/form-data" method="post" id="AddProducts" autocomplete="off" >
              
              @csrf
              <input type="hidden" name="user_id" value="{{$user_id}}">
            
              <div class="col-md-6">
              
                <div class="form-group">
                  <div class="form-label-group">
                    <?php $lastParentId = 0; ?>
                    <select id="category_id" name="category_id" class="form-control chosen-select" >
                      <option value="">Select Category</option>
                      @foreach($categories as $category)
                        @if($category->parent_id!=$lastParentId)
                        <?php $lastParentId = $category->parent_id; ?>
                        <optgroup label="{{ $category->parentCategory->name }}">
                        @endif
                          <option value ={{ $category->id }}>{{ $category->name }}</option>
                        @if($category->parent_id!=$lastParentId)
                        </optgroup>
                        @endif
                      @endforeach
                    </select>                    
                  </div>
                </div>
              
                <div class="form-group ">
                  <div class="form-label-group fieldgroup">
                    <input type="text" id="product_name" name="name" class="form-control" placeholder="Product Name" >
                  </div>
                </div>

                <div class="form-row">
                  <div class="form-group col-md-2">
                    <div class="form-label-group">
                      <input type="text" id="sku_prefix" name="sku_prefix" class="form-control" value="<?php echo ($user_id);?>" readonly>
                    </div>
                  </div>

                  <div class="form-group col-md-10">
                    <div class="form-label-group">
                      <input type="text" id="sku" name="sku" class="form-control" placeholder="SKU (stock keeping unit)" >
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-label-group">
                    <input type="text" id="barcode" name="barcode" class="form-control" placeholder="Barcode (ISBN, UPC, GTIN, etc.)">
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-label-group">
                    <textarea id="description" name="description" class="form-control ckeditor" placeholder="Description" ></textarea> 
                  </div>
                </div> 

                <div class="form-group">
                  <div class="form-label-group">
                    <input type="text" id="meta_key" name="meta_key" class="form-control" placeholder="Keywords" >
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-label-group">
                    <input type="text" id="meta_description" name="meta_description"  class="form-control" placeholder="Meta Description" >
                  </div>
                </div>
                    
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="text" id="return_policy" name="return_policy" class="form-control" placeholder="Return Policy" >
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-label-group">
                    <input type="text" id="warranty" name="warranty" class="form-control" placeholder="Warranty" >
                  </div>
                </div>
        
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="text" id="shipping_time" name="shipping_time" class="form-control" placeholder="Shipping Time" >
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="number" id="base_price" min="0" name="base_price" class="form-control" placeholder="MRP">
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="number" id="sale_price" min="0" name="sale_price" class="form-control" placeholder="Sale Price" >
                    @if ($errors->has('sale_price'))
                    <span class="help-block">
                      <strong>@lang('words.'.$errors->first('sale_price'))</strong>
                    </span>
                    @endif
                  </div>
                </div> 
                
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="number" id="gst" min="0" name="gst" class="form-control" placeholder="GST %" maxlength="5">
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-label-group">
                    <input type="number" id="qty" name="qty"  min="0" class="form-control" placeholder="qty">
                  </div>
                </div>
      
                <div class="form-group">
                  <div class="custom-control custom-checkbox small">
                    <input type="checkbox" class="custom-control-input" id="is_display_outof_stock_product" name="is_display_outof_stock_product" checked >
                    <label class="custom-control-label" for="is_display_outof_stock_product">Continue display when outof stock</label>
                  </div>
                </div>
              
                <div class="form-group">
                  <div class="form-row">
                    <div class=" col-md-8">
                      <div class="form-label-group">
                        <input type="number" id="weight" min="0" name="weight" class="form-control" placeholder="Product Weight" min='0'>
                        <label for="weight">Product Weight</label>
                      </div>
                    </div>
                    <div class=" col-md-4">
                      <select id="weight_unit" name="weight_unit" class="form-control"  required="required">
                        <option value="1" selected>kg</option>
                        <option value="0">g</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="form-label-group">
                    <input type="file" id="product_image" name="image[]" class="form-control" multiple >
                  </div>
                </div>

                <div class="form-group">
                  <div class="form-label-group">
                    <select id="status" name="status" class="form-control">
                        <option value="">Select Status</option>
                        <option value="1" selected>Enable</option>
                        <option value="0">Disable</option>
                    </select>
                  </div>
                </div>

                <div class="form-group attributes-list-div hide">
                  <div class="form-label-group" id="attributes-list">
                    &nbsp;
                  </div>
                </div>

                <div class="form-group col-md-5 add-product-attr-div hide" style="float:right;">
                  <div class="form-group">
                    <input type="button" id="add_product_attr" class="btn btn-primary btn-block add_product_attr " value="Add Product Attributes">
                  </div>
                </div>

                <div class="form-group col-md-12" id="product_attrs" style="float:right;">
                  &nbsp;
                  <span class="product-attrs-span"></span>
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
    
  <style type="text/css">
    #AddProducts .form-group input::-webkit-input-placeholder { /* Chrome/Opera/Safari */
      color:#495057;
    }
    #AddProducts .form-group input::-moz-placeholder { /* Firefox 19+ */
      color:#495057;
    }
    #AddProducts .form-group input:-ms-input-placeholder { /* IE 10+ */
      color:#495057;
    }
    #AddProducts .form-group input:-moz-placeholder { /* Firefox 18- */
      color:#495057;
    }
  </style>

  <script>
  // filter attributes based on product categories
  $(document).ready(function () {
    $('#category_id').chosen().change(function () {
        
      var category_id = $("#category_id").val();

      $.ajax({
        url: baseurl + "product/get-attributes/" + category_id,
        type: 'get',
        dataType: 'html',
        success: function(result){
          
          $('.attributes-list-div').removeClass('hide');
          $('.add-product-attr-div').removeClass('hide');

          $('#attributes-list').html(result);
        }
      });
    });
  });

  
  // add product attributes
  var number = 0;
  $('#add_product_attr').click(function() {

    var attribute_ids = $("#attribute_ids").val();
      var user_id = "<?php echo ($user_id);?>";

    if(attribute_ids.length < 1) {
      alert('Please select at last one attribute types.');
      $("#attribute_ids").trigger('chosen:activate');
      return false;
    }

    var data = {attrs: attribute_ids, number: number, user_id: user_id}
    $.ajax({
      url: baseurl + "product/add-attrs-in-form",
      data: data,
      type: 'get',
      dataType: 'html',
      success: function(result){

        $(result).insertAfter('span.product-attrs-span');

        number++;
      }
    });
  });
    
  // submit form
  $('#AddProducts').submit(function (e) {

    var base_price=document.getElementById("base_price").value;
    var sale_price=document.getElementById("sale_price").value;

    if(base_price<sale_price)
    {
      alert('Base price should be greater than sale price');
      return false;
    }
  });
  </script>
      
  <!-- Sticky Footer -->
  @include('layouts.admin_footer')