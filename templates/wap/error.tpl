<?php include template('header','common');?>
	<style>
		.user{
			display:table-cell;
			width:16.875%;
			height:1.1em;
			position: absolute;
			top: 0.6875em;
			right: 0;
		}
			
		.user a{
			display:block;
			width:50%;
			margin:0 auto;
			height:100%;
			background:url(<?php echo THEME_STYLE_PATH;?>style/default/images/user.png) no-repeat center center;
			background-size:auto 100%;
		}
    </style>
	<div id="wrapper">
		<div id="header-style">
			<a href="javascript:;" onclick="javascript:history.back(-1);" class="skip fl"></a>
			<strong class="d-block uset-t-text ">提示信息</strong>
			<div class="user">
                <a href="<?php echo U('Member/Profile/index');?>"></a>
			</div>
		</div>
		<div class="content bg-dedede">
			<div class="approve">
				<!-- ap-on  ap-off -->
				<p class="app-s-h approve-hint ap-off ap3 c-bb000d set-p-bottom">
                    <?php echo($error); ?>
				</p>
				<ul class="btn-wrap btn-w2 p-left clear">
                    <p class="at-login">
						<a class="btn-s-01" href="<?php echo($jumpUrl); ?>">返回</a>
					</p>
				</ul>
			</div>
		</div>		
	</div>
	</div>
</body>
</html>