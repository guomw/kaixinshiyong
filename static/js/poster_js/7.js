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
	sp.src = "/cn.xuwel.framework.tpcms/index.php?m=poster&c=index&a=show&siteid="+this.SiteID+"&id="+id+"&spaceid="+this.PosID;
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
 
var cmsAD_10 = new PCMSAD('cmsAD_10'); 
cmsAD_10.PosID = 7; 
cmsAD_10.ADID = 10; 
cmsAD_10.ADType = "text"; 
cmsAD_10.ADName = "文字广告"; 
cmsAD_10.ADContent = "{'ADText':[  {'textID':'10','textContent':'ＸＸＸＸＸＸＸＸＸＸＸＸＸＸＸＸ','textLinkUrl':'http%3A%2F%2F127.0.0.1%2Fcn.xuwel.framework.tpcms%2Findex.php'} ]}"; 
cmsAD_10.URL = "/cn.xuwel.framework.tpcms/index.php?m=poster&c=index"; 
cmsAD_10.SiteID = 1; 
cmsAD_10.Width = 0; 
cmsAD_10.Height = 0; 
cmsAD_10.UploadFilePath = ""; 
cmsAD_10.ShowAD();
