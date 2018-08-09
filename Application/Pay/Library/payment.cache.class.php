<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class payment
{
	public function __construct() {
		$this->db = D('Pay/PayPayment');
	}

	public function run() {
		$this->db->build_cache(TRUE);
	}
}