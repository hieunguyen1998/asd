<?php
@session_start(); //start session
require_once("models/m_chim_canh.php");
if(isset($_POST["product_code"]))
{
	foreach($_POST as $key => $value){
		$new_product[$key] = filter_var($value, FILTER_SANITIZE_STRING); 
	}
	
	$m_chim_canh=new M_chim_canh();
	$row=$m_chim_canh->Doc_chim_canh_theo_ma_chim($new_product['product_code']);
	
	$new_product["product_name"] = $row->ten_chim;
	$new_product["product_price"] = $row->gia; 
	$new_product["product_image"] = $row->hinh;	
	
	if(isset($_SESSION["products"])){  
		if(isset($_SESSION["products"][$new_product['product_code']]))
		{
			unset($_SESSION["products"][$new_product['product_code']]);
		}			
	}
	$_SESSION["products"][$new_product['product_code']] = $new_product;	
 	$total_items = count($_SESSION["products"]); 
	die(json_encode(array('items'=>$total_items)));
}


if(isset($_POST["load_cart"]) && $_POST["load_cart"]==1)
{

	if(isset($_SESSION["products"]) && count($_SESSION["products"])>0){
		$cart_box = '<ul class="cart-products-loaded">';
		$total = 0;
		foreach($_SESSION["products"] as $product){ 
			
		
			$product_name = $product["product_name"]; 
			$product_price = $product["product_price"];
			$product_code = $product["product_code"];
			$product_qty = $product["product_qty"];
			
			
			$cart_box .=  "<li> $product_name &mdash; ($product_qty * ".number_format($product_price,0,",","."). " $currency = ".number_format(($product_price * $product_qty),0,",","."). $currency . " ) <a href=\"#\" class=\"remove-item\" data-code=\"$product_code\">&times;</a></li>";
			$subtotal = ($product_price * $product_qty);
			$total = ($total + $subtotal);
		}
		$cart_box .= "</ul>";
		$cart_box .= '<div class="cart-products-total">Tổng cộng : '.number_format($total,0,",",".").$currency.' <u><a href="dat_hang.php" title="Xem giỏ hàng">Đặt hàng</a></u></div>';
		die($cart_box); 
	}else{
		die("Bạn chưa mua hàng... ");
	}
}


if(isset($_GET["remove_code"]) && isset($_SESSION["products"]))
{
	$product_code   = filter_var($_GET["remove_code"], FILTER_SANITIZE_STRING); 

	if(isset($_SESSION["products"][$product_code]))
	{
		unset($_SESSION["products"][$product_code]);
	}
	
 	$total_items = count($_SESSION["products"]);
	die(json_encode(array('items'=>$total_items)));
}

if(isset($_GET["update_code"]) && isset($_SESSION["products"]))
{
	$product_code   = filter_var($_GET["update_code"], FILTER_SANITIZE_STRING);
	$product_value   = filter_var($_GET["value"], FILTER_SANITIZE_STRING);

	if(isset($_SESSION["products"][$product_code]))
	{
		$_SESSION["products"][$product_code]["product_qty"]=$product_value;
	}
	
 	$total_items = count($_SESSION["products"]);
	die(json_encode(array('items'=>$total_items)));
}

?>