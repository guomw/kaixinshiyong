<?php 
$sqlquery = <<<EOT
DROP TABLE IF EXISTS `prefix_gbook`;
EOT;
/* 删除缓存 */
setcache('gbook_setting', NULL, 'module');
?>