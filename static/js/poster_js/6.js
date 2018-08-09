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
  if(AD.ADImage.length){
	  count = AD.ADImage.length;
  }
  for(var i=0;i<count;i++){
	if (isIE){

		if (document.readyState=="complete"){
			this.Stat(AD.ADImage[i].imgID);
		} else {
			document.onreadystatechange=function(){
				if(document.readyState=="complete") this.Stat(AD.ADImage[i].imgID);
			}
		}
	} else {
		this.Stat(AD.ADImage[i].imgID);
	}
	  str += "<li><a href='"+this.URL+"&siteid="+this.SiteID+"&id="+AD.ADImage[i].imgID+"&url="+AD.ADImage[i].imgADLinkUrl+"' target='_blank'><img alt='"+AD.ADImage[i].imgADAlt+"' title='"+AD.ADImage[i].imgADAlt+"' src='"+this.UploadFilePath+AD.ADImage[i].ImgPath+"' ";
	  var sizeStr = "";
	  if(this.Width==0&&this.Height>0){
		  sizeStr = " height='"+this.Height+"' ";
	  }else if(this.Width>0&&this.Height==0){
		  sizeStr = " width='"+this.Width+"' ";
	  }else{
		  sizeStr = (this.Width < this.Height) ? " width='"+this.Width+"' " : " height='"+this.Height+"' ";
	  }
	  str += sizeStr;
	  str += " style='border:0px;'/></a></li>";
	}
  str += "</div>";
  document.write(str);
}
 
var cmsAD_14 = new PCMSAD('cmsAD_14'); 
cmsAD_14.PosID = 6; 
cmsAD_14.ADID = 14; 
cmsAD_14.ADType = "images"; 
cmsAD_14.ADName = "广告列表1"; 
cmsAD_14.ADContent = "{'ADImage':[  {'imgID':'14','imgADLinkUrl':'http%3A%2F%2F127.0.0.1%2Fcn.xuwel.framework.tpcms%2Findex.php','imgADAlt':'','ImgPath':'./uploadfile/2014/0829/20140829084206904.jpg','imgADLinkTarget':'New','showAlt':'Y'} , {'imgID':'9','imgADLinkUrl':'http%3A%2F%2F127.0.0.1%2Fcn.xuwel.framework.tpcms%2Findex.php','imgADAlt':'','ImgPath':'./uploadfile/2014/0828/20140828105916401.jpg','imgADLinkTarget':'New','showAlt':'Y'}]}"; 
cmsAD_14.URL = "/cn.xuwel.framework.tpcms/index.php?m=poster&c=index&a=poster_click"; 
cmsAD_14.SiteID = 1; 
cmsAD_14.Width = 600; 
cmsAD_14.Height =300; 
cmsAD_14.UploadFilePath = ""; 
cmsAD_14.ShowAD();
