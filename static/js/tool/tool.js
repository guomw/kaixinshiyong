
/*说明此js用于后面工具类动态进行加载 
避免过多浪费浏览器无用资源 按需加载*/

document.write("<script type=\"text\/javascript\" id=\"tool\" ><\/script>");



// 加载百度上传js 和css
document.write('<link href=\"/static/js/webuploader/webuploader.css\" rel="stylesheet" type="text/css">');
document.write("<script type=\"text\/javascript\" src=\"/static/js/webuploader/webuploader.js\"><\/script>");

/*封装js 统一上传插件

用于网站图片上传

封装日期 2015-11-14

引用百度上传插件

需传入参数 1.上传id(必须上传) 2.input的 val()(选传) 3.显示图片的src的id

例如：uploader("#imgurl","#file_url","#imgsrc");  可以传入id .css 和其它dom节点


*/

//图片上传功能
	function uploader(_id,val,src){
       // console.log($("#sc"));
		var uploader = WebUploader.create({
			auto:true,
			fileVal:'Filedata',
		    // swf文件路径
		    swf: '/static/js/webuploader/webuploader.swf',
		    // 文件接收服务端。
		    server: "/index.php?m=Attachment&c=Attachment&a=swfupload",
		    // 选择文件的按钮。可选
		    formData:{
		    	"module":"",
		    	"catid":"",
		    	"userid":"1",
		    	"dosubmit":"1",
		    	"thumb_width":"0",
		    	"thumb_height":"0",
		    	"watermark_enable":"1",
		    	"filetype_post":"jpg|jpeg|gif|png",
		    	"swf_auth_key":"57a39f6f7415ec2cdd2b8afd77b57c3f",
		    	"isadmin":"1",
		    	"groupid":"2"
		    },
		    //内部根据当前运行是创建，可能是input元素，也可能是flash.

		    pick: {
		    	id:_id,
		    	multiple:false
		    	},
		    accept:{
				title: '图片文件',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
		    },
		    thumb:{
		    	width: '110',
		    	height: '110'
		    },
		    chunked: false,
		    chunkSize:1000000,
		    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
		    resize: false

		});

		uploader.addButton({
		    id: '#sc',
		 label: '点击选择图片'
		});
		uploader.onUploadSuccess = function( file, response ) {
			//console.log(response);
			
			var data = response._raw;
			var arr = data.split(',');
              $(val).val(arr[1]);
              $(src).attr('src',arr[1]); 
              return arr[1];

		}

		uploader.onUploadError = function(file, reason) {
			alert('文件上传错误：' + reason);
		}
	}


/*    
邮箱自动补全 
用于邮箱注册的时候自动补全邮箱
只需传入参数邮箱id即可

*/

document.write("<script type=\"text/javascript\" src=\"/static/js/jq-ui/js/jquery.ui.js\"><\/script>");
document.write('<link rel=\"stylesheet\" href=\"/static/js/jq-ui/css/smoothness/jquery.ui.css\" type="text/css" />');

  function email_autocomplete(id){
	$(id).autocomplete({
		delay : 0,
		autoFocus : true,
		source : function (request, response) {
			//获取用户输入的内容
			//alert(request.term);
			//绑定数据源的		
			var hosts = ['qq.com', '163.com', '263.com', 'sina.com.cn','126.com', 'sohu.com'],
				term = request.term,		//获取用户输入的内容
				name = term,				//邮箱的用户名
				host = '',					//邮箱的域名
				ix = term.indexOf('@'),		//@的位置
				result = [];				//最终呈现的邮箱列表
				
				
			result.push(term);
			
			//当有@的时候，重新分别用户名和域名
			if (ix > -1) {
				name = term.slice(0, ix);
				host = term.slice(ix + 1);
			}
			
			if (name) {
				//如果用户已经输入@和后面的域名，
				//那么就找到相关的域名提示，比如bnbbs@1，就提示bnbbs@163.com
				//如果用户还没有输入@或后面的域名，
				//那么就把所有的域名都提示出来
				
				var findedHosts = (host ? $.grep(hosts, function (value, index) {
						return value.indexOf(host) > -1
					}) : hosts),
					findedResult = $.map(findedHosts, function (value, index) {
					return name + '@' + value;
				});
				
				result = result.concat(findedResult);
			}
			
			response(result);
		},	
	});
	
}

