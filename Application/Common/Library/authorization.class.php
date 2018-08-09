<?php

namespace Common\Library;

class authorization
{
	protected $host = "";
	protected $file = "";
	protected $error = "";
	protected $key = "";
	protected $code = "";

	public function __construct()
	{

	}

	public function check()
	{

		C("system_auth_type", "professional");
		
		if (in_array(MODULE_NAME, array("Wap", "Wechat")) && (C('system_auth_type') != "professional")) {
			exit("该功能仅专业版使用");
		}
		return TRUE;
	}

	public function getRemoteKey()
	{
		
			return TRUE;
	
	}

	public function getLocalKey()
	{
		if (!file_exists($this->file)) {
			$this->error = "授权文件不存在";
			return FALSE;
		}

		if (!is_writable($this->file)) {
			$this->error = "授权文件不可写";
			return FALSE;
		}

		$result = file_get_contents($this->file);

		if (!$result) {
			$this->error = "授权信息为空";
			return FALSE;
		}

		$this->getDecodeKey($result);
		return TRUE;
	}

	public function getDecodeKey($code)
	{
		
		$this->code = unserialize(authcode($code, "DECODE", $this->key));
		print_r($this->code);exit;
		return TRUE;
	}

	public function writeAuthFile()
	{
		if ($this->code) {
			$auth = $this->code;
			$auth["update_time"] = NOW_TIME;
			@file_put_contents($this->file, authcode(serialize($auth), "ENCODE", $this->key));
		}

		return TRUE;
	}

	private function parse_url($host = "")
	{
		$host = (empty($host) ? $this->host : $host);

		if (empty($host)) {
			return NULL;
		}

		$regx1 = "/(([^\/\?#&]+\.)?([^\/\?#&\.]+\.)(com\.cn|org\.cn|net\.cn|com\.jp|co\.jp|com\.kr|com\.tw)(\:[0-9]+)?)\/?/i";
		$regx2 = "/(([^\/\?#&]+\.)?([^\/\?#&\.]+\.)(cn|com|org|info|us|fr|de|tv|net|cc|biz|hk|jp|kr|name|me|tw|la)(\:[0-9]+)?)\/?/i";
		$tophost = "";

		if (preg_match($regx1, $host, $matches)) {
			$host = $matches[1];
		}
		else if (preg_match($regx2, $host, $matches)) {
			$host = $matches[1];
		}

		if ($matches) {
			$tophost = $matches[3] . $matches[4];
			$domainLevel = ($matches[2] == "www." ? 1 : substr_count($matches[2], ".") + 1);
		}
		else {
			$tophost = "";
			$domainLevel = 0;
		}

		return $tophost;
	}

	public function sendAuthorization()
	{
		$lastSend = getcache("__last__", "commons");
		if (isset($lastSend) && ((86400 * 7) < (NOW_TIME - $lastSend))) {
			helpers("authorization");
			sendauthorization("used");
			setcache("__last__", NOW_TIME, "commons");
		}
	}

	public function getError()
	{
		if ($this->error) {
			return $this->error . " By (www.e188w.com)<br/>TIMESTAMP：" . dgmdate(NOW_TIME);
		}

		return false;
	}
}


?>
