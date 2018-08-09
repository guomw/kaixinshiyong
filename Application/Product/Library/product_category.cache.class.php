<?php 
class product_category
{
	public function run() {
		return model('product/product_category')->build_cache();
	}
}