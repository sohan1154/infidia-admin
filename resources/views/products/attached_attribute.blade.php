
<?php $index = 0; ?>

@foreach($product->productAttributes as $singleAtt)
	
	<div id="product-attr-{{$index}}" class="product-attrs-box product-attr-{{$index}}" style="border:1px solid; color: #ccc; padding: 10px; margin-top: 10px;">
		
		<input type="hidden" name="attr[{{$index}}][id]" class="form-control" value="{{$singleAtt->id}}">
		
		<?php 
		$count = 0;
		$attrs = unserialize($singleAtt->attrs);
		?>
		@foreach($attrs as $value)
		<input type="hidden" name="attr[{{$index}}][attr][{{$count}}][attribute_type]" class="form-control" value="{{$value['attribute_type']}}" readonly>
		
		<div class="form-group">
			<div class="form-label-group">
				<label>{{$value['attribute_type']}}</label>
				<input name="attr[{{$index}}][attr][{{$count}}][attribute_value]" class="form-control product-custom-attr-value" placeholder="{{$value['attribute_type']}}" value="{{$value['attribute_value']}}">
			</div>
		</div>
		<?php $count++; ?>
		@endforeach

		<div class="form-group">
			<div class="form-label-group">
				<label>SKU (stock keeping unit)</label>
				<input type="text" name="attr[{{$index}}][sku]" class="form-control" placeholder="SKU (stock keeping unit)" value="{{$singleAtt->sku}}" readonly>
			</div>
		</div>

		<div class="form-group">
			<div class="form-label-group">
				<label>Barcode (ISBN, UPC, GTIN, etc.)</label>
				<input type="text" name="attr[{{$index}}][barcode]" class="form-control" placeholder="Barcode (ISBN, UPC, GTIN, etc.)" value="{{$singleAtt->barcode}}">
			</div>
		</div>

		<div class="form-group">
			<div class="form-label-group">
				<label>MRP</label>
				<input type="number" min="0" name="attr[{{$index}}][base_price]" class="form-control product-custom-attr-base-price" placeholder="MRP" value="{{$singleAtt->base_price}}">
			</div>
		</div>

		<div class="form-group">
			<div class="form-label-group">
				<label>Sale Price</label>
				<input type="number" min="0" name="attr[{{$index}}][sale_price]" class="form-control product-custom-attr-sale-price" placeholder="Sale Price" value="{{$singleAtt->sale_price}}">
			</div>
		</div> 

		<div class="form-group">
			<div class="form-label-group">
				<label>QTY</label>
				<input type="number" name="attr[{{$index}}][qty]"  min="0" class="form-control product-custom-attr-qty" placeholder="QTY" value="{{$singleAtt->qty}}">
			</div>
		</div>

		<div class="form-group">
			<div class="form-label-group">
				<input type="file" name="attr[{{$index}}][image][]" class="form-control " multiple >
			</div>
			<?php 
				$allImages = unserialize($singleAtt->images);
				if($allImages>0){
					foreach ($allImages as $key => $image) {
						echo '<span class="attrImageTag-'.$singleAtt->id.'-'.$key.'"><img src="'.url('/').'/images/products/'.$product->id.'/attr/thumb/'.$image.'" width=75><span data-attrId="'.$singleAtt->id.'" class="RemoveAttrImage" id="removeAttrId'.$key.'">X</span></span>';
					}
				}
			?>
		</div>

		<div class="form-group col-md-3" style="float:right;">
			<div class="form-label-group">
				<input type="button" id="attrId{{$singleAtt->id}}" class="btn btn-danger btn-block deleteProductAttrBox" value="Remove">
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

	<?php $index++; ?>
@endforeach