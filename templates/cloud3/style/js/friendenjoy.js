/*var Clicktheshow = function(){
	var sharz = document.getElementsByClassName("sharz-type");
	var qqroomz = document.getElementsByClassName("qqroomz");
	var sinaz = document.getElementsByClassName("sinaz");
	var qqz = document.getElementsByClassName("qqz");
	var renrenz = document.getElementsByClassName("renrenz");
}
*/

/*function onmsorshow (){
	var alistz = document.getElementsByClassName("sharz-type").getElementsByTagName("a");
	var plistz =document.getElementsByClassName("sharz-bottomz").getElementsByTagName("p");
	for(i=0;i<alistz.length;i++){
		alistz[i].onmouseover = function(){
			
			plistz[i].style.display="block";
		}
	}
}*/
var sharzbottomzdiv = document.getElementById("sharzbottomz");
var sharztypediv = document.getElementById("sharztype");
var  sharztype = document.getElementById("sharztype").getElementsByTagName("a");
var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");

		for (this.i = 0; i < sharztype.length; i++) {
			 if(i == 0) {sharztype[i].onmousemove = function(){
         	
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		
          		sharzbottomz[0].style.display = "block";
          		sharzbottomz[1].style.display = "none";
          		sharzbottomz[2].style.display = "none";
          		sharzbottomz[3].style.display = "none";
          		
          	
          };
         }
          if(i == 1) {sharztype[i].onmousemove = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[1].style.display = "block";
          		sharzbottomz[0].style.display = "none";
          		sharzbottomz[2].style.display = "none";
          		sharzbottomz[3].style.display = "none";
          	
         };
        }
         if(i == 2) {sharztype[i].onmousemove = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[2].style.display = "block";
          		sharzbottomz[0].style.display = "none";
          		sharzbottomz[1].style.display = "none";
          		sharzbottomz[3].style.display = "none";
          	
         };
        }
         if(i == 3) {sharztype[i].onmousemove = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[3].style.display = "block";
          		sharzbottomz[0].style.display = "none";
          		sharzbottomz[1].style.display = "none";
          		sharzbottomz[2].style.display = "none";
          	
         };
		}
       }
		sharzbottomzdiv.onmouseout = function(){
		sharzbottomz[0].style.display = "block";
		sharzbottomz[1].style.display = "none";
		sharzbottomz[2].style.display = "none";
		sharzbottomz[3].style.display = "none";
	};
	sharztypediv.onmouseover = function(){
		sharzbottomz[0].style.display = "none";
		sharzbottomz[1].style.display = "none";
		sharzbottomz[2].style.display = "none";
		sharzbottomz[3].style.display = "none";
	};


	
	/*var abc= function(){sharztypediv.onmousemove = function(){
		sharzbottomz[0].style.display = "none";
	}()}*/
	
	/*sharztypediv.onmouseout = function(){
		sharzbottomz[0].style.display = "block";
	};
	
	
	for (this.i = 0; i < sharztype.length; i++) {
           
         
         if(i == 0) {sharztype[i].onmousemove = function(){
         	
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		
          		sharzbottomz[0].style.display = "block";
          		
          	
          };
          
         	sharztype[i].onmouseout = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[0].style.display = "none";
          	
          };
         }
         if(i == 1) {sharztype[i].onmousemove = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[1].style.display = "block";
          		sharzbottomz[0].style.display = "none";
          	
         };
          sharztype[i].onmouseout = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[1].style.display = "none";
          	
          };
         
         }
         if(i == 2) {sharztype[i].onmousemove = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[2].style.display = "block";
          		sharzbottomz[0].style.display = "none";
          	
         };
         			sharztype[i].onmouseout = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[2].style.display = "none";
          	
          };
         }
         if(i == 3) {sharztype[i].onmousemove = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[3].style.display = "block";
          		sharzbottomz[0].style.display = "none";
          	
         };
         sharztype[i].onmouseout = function(){
          	var  sharzbottomz = document.getElementById("sharzbottomz").getElementsByTagName("p");
          		sharzbottomz[3].style.display = "none";
          	
          };
         }
         
        }*/
	
    