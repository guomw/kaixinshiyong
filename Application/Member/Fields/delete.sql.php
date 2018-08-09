<?php
defined('IN_ADMIN') or exit('No permission resources.');
sqlexecute("ALTER TABLE `$tablename` DROP `$field`");
?>