<!DOCTYPE html>
<html>
<head>
    <title>User mark as Return this Order</title>
</head>

<body>
<h2>Return Details:</h2>
<br/>
An order return request submited by {{$uname}} successfully. Please Process this request. 
<br/>
<br/>
Order Id: {{$morder}}
<br/>
Order Amount: {{$uamount}}
<br>
<br/>
<table>
 <tbody>
                	<?php if($productdata){
						$i = 1;
                		  foreach($productdata as $key => $val){ ?>
					<tr> 
					   <td>{{$i}} - Product Name : {{$val->name}}<br>
					   	   Sku : {{$val->sku}} <br>
                        <?php foreach ($productAttrdata as $key1 => $value) {
                        	if($key==$key1){ 
                              $attrData =  unserialize($value->attr_name);
                              foreach ($attrData as $attrkey => $attrvalue) {
                              	 echo '<lable>'.$attrkey.' : </lable>'.$attrvalue.'<br>';
                              	 echo '<lable>Amount : </lable>'.$amount[$key1].'<br>';
                              	 echo '<lable>Qty : </lable>'.$qty[$key1].'<br>';
                              }
                              
                        	 }
                        } ?></td>
				    </tr>
				    <?php 
					$i++; } } ?>
                </tbody>
            </table>  
</body>

</html>