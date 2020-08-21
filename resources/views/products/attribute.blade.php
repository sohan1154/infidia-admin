<div id="product-attr-{{$index}}" class="product-attrs-box product-attr-{{$index}}" style="border:1px solid; color: #ccc; padding: 10px; margin-top: 10px;">
	
	<?php $count = 0; ?>
	@foreach($attributes as $key=>$value)
		<input type="hidden" name="attr[{{$index}}][attr][{{$count}}][attribute_type]" class="form-control" value="{{$value}}" readonly>
		
		<div class="form-group">
			<div class="form-label-group">
				<label>{{$value}}</label>
				<input name="attr[{{$index}}][attr][{{$count}}][attribute_value]" class="form-control product-custom-attr-value" placeholder="{{$value}}">
			</div>
		</div>
		<?php $count++; ?>
	@endforeach

	<div class="form-row">
		<div class="form-group col-md-2">
		<div class="form-label-group">
			<input type="text" name="attr[{{$index}}][sku_prefix]" class="form-control" value="<?php echo ($user_id);?>" readonly>
		</div>
		</div>

		<div class="form-group col-md-10">
		<div class="form-label-group">
			<label>SKU (stock keeping unit)</label>
			<input type="text" name="attr[{{$index}}][sku]" class="form-control" placeholder="SKU (stock keeping unit)">
		</div>
		</div>
	</div>

	
	<div class="form-group">
		<div class="form-label-group">
			<label>Barcode (ISBN, UPC, GTIN, etc.)</label>
			<input type="text" name="attr[{{$index}}][barcode]" class="form-control" placeholder="Barcode (ISBN, UPC, GTIN, etc.)">
		</div>
	</div>

	<div class="form-group">
		<div class="form-label-group">
			<label>MRP</label>
			<input type="number" min="0" name="attr[{{$index}}][base_price]" class="form-control product-custom-attr-base-price" placeholder="MRP">
		</div>
	</div>

	<div class="form-group">
		<div class="form-label-group">
			<label>Sale Price</label>
			<input type="number" min="0" name="attr[{{$index}}][sale_price]" class="form-control product-custom-attr-sale-price" placeholder="Sale Price" >
		</div>
	</div> 

	<div class="form-group">
		<div class="form-label-group">
			<label>QTY</label>
			<input type="number" name="attr[{{$index}}][qty]"  min="0" class="form-control product-custom-attr-qty" placeholder="QTY">
		</div>
	</div>

	<div class="form-group">
		<div class="form-label-group">
			<input type="file" name="attr[{{$index}}][image][]" class="form-control product-custom-attr-image" multiple >
		</div>
	</div>

	<div class="form-group col-md-3" style="float:right;">
		<div class="form-label-group">
			<input type="button" class="btn btn-danger btn-block remve_product_attr" value="Remove">
		</div>
	</div>

	<br><br>

</div>

<script>
$('.remve_product_attr').click(function () {
	$(this).parents('.product-attrs-box').remove();
});

$('#attribute_ids').val(0).trigger("chosen:updated"); // reset attribute dropdown box

// custom validation on added attribte fields
$('.product-custom-attr-value, .product-custom-attr-image').each(function() {
    $(this).rules('add', {
        required: true,
        messages: {
            required:  "This field is required.",
        }
    });
});
$('.product-custom-attr-base-price, .product-custom-attr-sale-price, .product-custom-attr-qty').each(function() {
    $(this).rules('add', {
        required: true,
        number: true,
        messages: {
            required:  "This field is required.",
            number:  "Please enter number only",
        }
    });
});

</script>