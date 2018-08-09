<?php
function sendAuthorization($action = 'install')
{
	$url = 'http://www.dealswill.com/authorization.php?';
	$param = array('action' => $action, 'domain' => $_SERVER['HTTP_HOST'], 'identifier' => c('SYSTEM_IDENTIFIER'), 'version' => c('SYSTEM_VERSION'), 'release' => c('SYSTEM_RELEASE'));
	return _dfsockopen($url) . http_build_query($param);
}

?>