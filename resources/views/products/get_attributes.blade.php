                      
<select id="attribute_ids" name="attribute_ids" class="form-control chosen-select" multiple data-placeholder="Select Attributes" >
	@foreach($attributes as $key=>$value)
		<option value = {{ $key }} >{{ $value }}</option>
	@endforeach
</select>

<script>
	$(document).ready(function () {
		$(".chosen-select").chosen();
	});
</script>