<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
	<title>提示信息</title>
	<link rel="stylesheet" type="text/css" href="<?php echo THEME_STYLE_PATH ?>style/css/base.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo THEME_STYLE_PATH ?>style/css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo THEME_STYLE_PATH ?>style/css/user.css"/>
	<script type="text/javascript" src="<?php echo THEME_STYLE_PATH ?>style/js/jquery-1.7.2.min.js"></script>
	</head>
	<body class="ui-bg-a">
	     <?php include template('toper','common') ?>
			<!-- logo和搜索部分 -->
			<style>
				#zc-content .user_l_c .u_tab_list li a{ float:none; }
				.l-nav{ width:600px; }
				#header .logo img {
				width: auto;
				height: 52px;
				position: relative;
				top: 50% !important;
				margin-top: -26px;
				}
				.user_l_c .hint_title {
				    height: 100px;
				    line-height: 100px;
				    font-size: 36px;
				    color: gray;
				    text-align: center;
				    padding-top: 84px;
				    background: url(<?php echo THEME_STYLE_PATH ?>style/img/x.png) no-repeat center top;
				    }

				}

			</style>
			<div id="header" class="user-header clear">
				<div class="wrap-and clear">
					<div class="fl logo">
						<a href="/"><img src="<?php echo  C('SITE_LOGO_ZHU')	?>" alt="<?php echo C('WEBNAME') ?>"  /></a>
					</div>
					<h2 class="user-header-hint fl">提示信息</h2>
				</div>
			</div>
			<div id="zc-content">
				<div class="wrap">
					<div class="user_l_c">
						<h2 class="hint_title"><?php echo $error; ?></h2>
						<ul class="hint_txt">
							<li>您现在可以进行以下操作：</li>
							<li class="mt">
								<a href="/">返回首页</a>
								<a href="<?php echo U('member/profile/index') ?>">个人中心</a>
							</li>
							<li><a href="<?php echo($jumpUrl); ?>" id="href">[点这里返回上一页，<b id="wait"><?php echo($waitSecond); ?></b>秒后自动跳转]</a>
							</li>
						</ul>
					</div>
				</div>	
			</div>
	   <?php include template('v2_register_footer','member/common') ?>
	</body>
	<script style="text/javascript">
	function close_dialog() {
		window.top.right.location.reload();
		var list = window.top.art.dialog.list;
		for (var i in list) {
		    list[i].close();
		}
	}

	(function(){
	var wait = document.getElementById('wait'),href = document.getElementById('href').href;
	var interval = setInterval(function(){
		var time = --wait.innerHTML;
		if(time <= 0) {
			location.href = href;
			clearInterval(interval);
		};
	}, 1000);
	})();
	</script>
</html>