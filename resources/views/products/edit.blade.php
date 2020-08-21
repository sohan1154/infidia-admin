@include('layouts.admin_header')
  
  <div id="content-wrapper">

    <div class="container-fluid">
      <!-- Breadcrumbs-->
      @if(session()->has('alert-danger'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ session()->get('alert-danger') }}
        </div>
      @endif

      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{url('/home')}}">Dashboard</a>
        </li>
        @if(Auth::user()->role=='Admin')
          <li class="breadcrumb-item">
            <a href="{{url('/users/seller')}}">Sellers ({{$product->user->name}})</a>
          </li>
          <li class="breadcrumb-item active">
            <a href="{{url('/products?userid='.base64_encode($product->user_id))}}">Products</a>
          </li>
        @else
          <li class="breadcrumb-item active">
            <a href="{{url('/products')}}">Products</a>
          </li>
        @endif
        <li class="breadcrumb-item active">Edit Products</li>
      </ol>

      <!-- DataTables Example -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-table"></i> Edit Products
        </div>
        <div class="card-body">
          <form action="{{ url('product/edit/'.base64_encode($product->id)) }}" enctype="multipart/form-data" method="post" id="EditProducts">
              
            @csrf
            <input type="hidden" name="user_id" value="{{$user_id}}">
          
            <div class="col-md-6">

              <input type="hidden" name="attribute_set_id" value="{{$product->attribute_set_id}}" id="attribute_set_id">
              
              <div class="form-group">
                <div class="form-label-group">
                  <select id="category_id" name="category_id" class="form-control chosen-select" autofocus="autofocus">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                      <option value = {{ $category->id }} {{ $category->id==$product->category_id ? 'selected' : '' }} >{{ $category->name }}</option>
                    @endforeach
                  </select>                    
                </div>
              </div>
      
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="product_name" name="name" value="{{ $product->name }}" class="form-control" placeholder="Product Name" required="required">
                  <label for="product_name">Product Name</label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="sku" name="sku" value="{{ $product->sku }}" class="form-control" placeholder="SKU (stock keeping unit)"  readonly="readonly">
                  <label for="sku">SKU</label>
                </div>
              </div>
              
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="barcode" name="barcode" value="{{$product->barcode}}" class="form-control" placeholder="Barcode (ISBN, UPC, GTIN, etc.)">
                  <label for="sku">Barcode (ISBN, UPC, GTIN, etc.)</label>
                </div>
              </div>
              
              <div class="form-group">
                <div class="form-label-group">
                <textarea id="description" name="description" class="form-control ckeditor" placeholder="Description" >{{ $product->description }}</textarea> 
                </div>
              </div>     
      
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="meta_key" name="meta_key" value="{{ $product->meta_key }}" class="form-control" placeholder="Keywords" >
                  <label for="meta_key">Keywords</label>
                </div>
              </div>
              
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="meta_description" name="meta_description" value="{{ $product->meta_description }}" class="form-control" placeholder="Meta Description" >
                  <label for="meta_description">Meta Description</label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="return_policy" name="return_policy" value="{{ $product->return_policy }}" class="form-control" placeholder="Return Policy" >
                  <label for="return_policy">Return Policy</label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="warranty" name="warranty" value="{{ $product->warranty }}" class="form-control" placeholder="Warranty" >
                  <label for="warranty">Warranty</label>
                </div>
              </div>
      
              <div class="form-group">
                <div class="form-label-group">
                  <input type="text" id="shipping_time" name="shipping_time" value="{{ $product->shipping_time }}" class="form-control" placeholder="Shipping Time" >
                  <label for="shipping_time">Shipping Time</label>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="number" id="base_price" min="0" name="base_price" value="{{ $product->price->base_price }}" class="form-control" placeholder="MRP" required="required">
                  <label for="base_price">Base Price</label>
                </div>
              </div>
              
              <div class="form-group">
                <div class="form-label-group">
                  <input type="number" id="sale_price" min="0" name="sale_price" value="{{ $product->price->sale_price }}" class="form-control" placeholder="Sale Price" required="required">
                  <label for="sale_price">Sale Price</label>
                </div>
              </div>
              
              <div class="form-group">
                <div class="form-label-group">
                  <input type="number" id="gst" name="gst" min="0" value="{{ $product->price->gst }}" class="form-control" placeholder="GST %" maxlength="5">
                  <label for="gsy">GST %</label>
                </div>
              </div>
      
              <div class="form-group">
                <div class="form-label-group">
                  <input type="number" id="qty" name="qty" min="0" value="{{ $product->stock->qty }}" class="form-control" placeholder="Sale Price" required="required">
                  <label for="qty">Quantity</label>
                </div>
              </div>

              <div class="form-group">
                <div class="custom-control custom-checkbox small">
                  <input type="checkbox" class="custom-control-input" id="is_display_outof_stock_product" name="is_display_outof_stock_product" <?php echo ($product->is_display_outof_stock_product) ? 'checked' : '' ?> >
                  <label class="custom-control-label" for="is_display_outof_stock_product">Continue display when outof stock</label>
                </div>
              </div>
      
              <div class="form-group">
                <div class="form-row">
                  <div class=" col-md-8">
                    <div class="form-label-group">
                      <input type="number" id="weight" min="0" name="weight" value="{{ $product->weight }}"  class="form-control" placeholder="Product Weight" >
                      <label for="weight">Product Weight</label>
                    </div>
                  </div>
                  <div class=" col-md-4">
                    <select id="weight_unit" name="weight_unit" class="form-control"  required="required">
                      <option value="1" {{ $product->weight_unit=='kg' ? 'selected' : '' }} >kg</option>
                      <option value="0" {{ $product->weight_unit=='g' ? 'selected' : '' }} >g</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <input type="file" id="product_image" name="image[]" class="form-control" multiple >
                  <label for="product_image">Product Image </label>
                </div>
                <?php 
                  $images = productImagesData($product->id);
                  $allImages = unserialize($images);
                  if($allImages>0){
                    foreach ($allImages as $key => $image) {
                      echo '<span class="imageTag'.$key.'"><img src="'.url('/').'/images/products/'.$product->id.'/thumb/'.$image.'" width=75><span data-pid="'.$product->id.'" class="RemoveImage" id="removeId'.$key.'">X</span></span>';
                    }
                  }
                ?>
              </div>

              <div class="form-group">
                <div class="form-label-group">
                  <select id="status" name="status" class="form-control"  required="required">
                      <option value="">Select Status</option>
                      <option value="1" {{ $product->status=='1' ? 'selected' : '' }} >Enable</option>
                      <option value="0" {{ $product->status=='0' ? 'selected' : '' }} >Disable</option>
                  </select>
                </div>
              </div>

              <div class="form-group attributes-list-div ">
                <div class="form-label-group" id="attributes-list">
                  &nbsp;
                  @include('products.get_attributes')
                </div>
              </div>

              <div class="form-group col-md-5 add-product-attr-div " style="float:right;">
                <div class="form-group">
                  <input type="button" id="add_product_attr" class="btn btn-primary btn-block add_product_attr " value="Add Product Attributes">
                </div>
              </div>

              <div class="form-group col-md-12" id="product_attrs" style="float:right;">
                &nbsp;
                <span class="product-attrs-span"></span>
                @include('products.attached_attribute')
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

<!-- Sticky Footer -->
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
  var number = "<?php echo (!empty($product->productAttributes)) ? count($product->productAttributes) : 0; ?>";
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

@include('layouts.admin_footer')