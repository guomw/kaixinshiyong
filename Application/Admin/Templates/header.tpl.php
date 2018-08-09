<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"<?php if(isset($addbg)) { ?> class="addbg"<?php } ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo L('website_manage');?></title>
<link href="<?php echo CSS_PATH?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH.LANG_SET;?>-system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>table_form.css" rel="stylesheet" type="text/css" />
<?php
if(isset($show_dialog)) {
?>
<link href="<?php echo CSS_PATH?>dialog.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
<?php } ?>
	<link rel="stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles1.css" title="styles1" media="screen" />
	<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles2.css" title="styles2" media="screen" />
	<link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles3.css" title="styles3" media="screen" />
    <link rel="alternate stylesheet" type="text/css" href="<?php echo CSS_PATH?>style/<?php echo LANG_SET;?>-styles4.css" title="styles4" media="screen" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.min.js"></script> 
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>admin_common.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<?php if(isset($show_validator)) { ?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<?php } ?>
<script type="text/javascript">
	window.focus();
	var fromhash = "<?php echo session('FROMHASH');?>";
	<?php if(!isset($show_pc_hash)) { ?>
		window.onload = function() {
		var html_a = document.getElementsByTagName('a');
		var num = html_a.length;
		for(var i=0;i<num;i++) {
			var href = html_a[i].href;
			if(href && href.indexOf('javascript:') == -1 && href.indexOf('fromhash') == -1 && html_a[i].rel != 'nofollow') {
				if(href.indexOf('?') != -1) {
					html_a[i].href = href+'&fromhash='+fromhash;
				} else {
					html_a[i].href = href+'?fromhash='+fromhash;
				}
				if (href.indexOf('menuid') == -1) {
					html_a[i].href = href + '&menuid=<?php echo MENUID; ?>';
				}
			}
		}

		var html_form = document.forms;
		var num = html_form.length;
		for(var i=0;i<num;i++) {
			var newNode = document.createElement("input");
			newNode.name = 'fromhash';
			newNode.type = 'hidden';
			newNode.value = fromhash;
			html_form[i].appendChild(newNode);
		}
	}
<?php } ?>
</script>
</head>
<body>
<?php if (!isset($show_header)): ?>
<div class="subnav">
	<?php if (isset($h2_title)): ?><h2 class="title-1 line-x f14 fb blue lh28">{$h2_title}</h2><?php endif; ?>
    <div class="content-menu ib-a blue line-x">
    <?php if (isset($big_menu)): ?>
    <a class="add fb" href="<?php echo $big_menu[0]; ?>"><em><?php echo $big_menu[1];?></em></a>&nbsp;&nbsp;&nbsp;
    <?php endif; ?>
    <?php echo model('node')->getsubmenu(MENUID); ?>
    </div>
</div>
<?php endif; ?>
<style type="text/css">
	html{_overflow-y:scroll}
</style>