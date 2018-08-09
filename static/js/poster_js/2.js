function PCMSAD(PID) {
  this.ID        = PID;
  this.PosID  = 0; 
  this.ADID		  = 0;
  this.ADType	  = "";
  this.ADName	  = "";
  this.ADContent = "";
  this.PaddingLeft = 0;
  this.PaddingTop  = 0;
  this.Width = 0;
  this.Height = 0;
  this.IsHitCount = "N";
  this.UploadFilePath = "";
  this.URL = "";
  this.SiteID = 0;
  this.ShowAD  = showADContent;
  this.Stat = statAD;
}

function statAD(id) {
	var sp = document.createElement("SCRIPT");
	sp.type = "text/javascript";
	sp.src = "/index.php?m=poster&c=index&a=show_total&siteid="+this.SiteID+"&id="+id+"&spaceid="+this.PosID;
	document.body.appendChild(sp);
}

function showADContent() {
  var content = this.ADContent;
  var isIE=!!window.ActiveXObject;
  var str = "<div id='PCMSAD_"+this.PosID+"'>";
  var AD = eval('('+content+')');
  var count = 0;
  if(AD.ADText.length){
	  count = AD.ADText.length;
  }
  for(var i=0;i<count;i++){
	if (isIE){

		if (document.readyState=="complete"){
			this.Stat(AD.ADText[i].textID);
		} else {
			document.onreadystatechange=function(){
				if(document.readyState=="complete") this.Stat(AD.ADText[i].textID);
			}
		}
	} else {
		this.Stat(AD.ADText[i].textID);
	}
	  str += "<li><a href="+this.URL+"&a=poster_click&siteid="+this.SiteID+"&id="+AD.ADText[i].textID+"&url="+AD.ADText[i].textLinkUrl+" target='_blank' title='"+AD.ADText[i].textContent+"'>"+AD.ADText[i].textContent+"</a></li>";
	}
  str += "</div>";
  document.write(str);
}
 
var cmsAD_2 = new PCMSAD('cmsAD_2'); 
cmsAD_2.PosID = 2; 
cmsAD_2.ADID = 2; 
cmsAD_2.ADType = "text"; 
cmsAD_2.ADName = "首页分类推荐文字广告"; 
cmsAD_2.ADContent = "{'ADText':[  {'textID':'2','textContent':'积分可换礼，万件商品任你选','textLinkUrl':'%2Findex.php%3Fm%3Dshop%26amp%3Bc%3Dindex%26amp%3Ba%3Dindex'} ]}"; 
cmsAD_2.URL = "/index.php?m=poster&c=index"; 
cmsAD_2.SiteID = 1; 
cmsAD_2.Width = 160; 
cmsAD_2.Height = 12; 
cmsAD_2.UploadFilePath = ""; 
cmsAD_2.ShowAD();
