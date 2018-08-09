var configMod=angular.module("starter.config", []);
	configMod.config(["$httpProvider" ,function($httpProvider) {  
		// Use x-www-form-urlencoded Content-Type
		              $httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
		              $httpProvider.defaults.headers.put["Content-Type"] = "application/x-www-form-urlencoded";

		              // Override $http service"s default transformRequest
		              $httpProvider.defaults.transformRequest = [function(data)
		              {
		                  /**
		                   * The workhorse; converts an object to x-www-form-urlencoded serialization.
		                   * @param {Object} obj
		                   * @return {String}
		                   */
		                  var param = function(obj)
		                  {
		                      var query = "";
		                      var name, value, fullSubName, subName, subValue, innerObj, i;
		                      //console.log(obj);


		                      for(name in obj)
		                      {
		                          value = obj[name];
		                         // //console.log(value);


		                          if(value instanceof Array)
		                          {
		                              //console.log("Array");
		                              for(i=0; i<value.length; ++i)
		                              {
		                                  subValue = value[i];
		                                  fullSubName = name + "[" + i + "]";
		                                  innerObj = {};
		                                  innerObj[fullSubName] = subValue;
		                                  query += param(innerObj) + "&";
		                              }
		                          }
		                          else if(value instanceof Object)
		                          {
		                               //console.log("object");
		                              for(subName in value)
		                              {


		                                  subValue = value[subName];
		                                  if(subValue != null){
		                                      // fullSubName = name + "[" + subName + "]";
		                                      //user.userName = hmm & user.userPassword = 111
		                                      fullSubName = name + "." + subName;
		                                      // fullSubName =  subName;
		                                      innerObj = {};
		                                      innerObj[fullSubName] = subValue;
		                                      query += param(innerObj) + "&";
		                                  }
		                              }
		                          }
		                          else if(value !== undefined ) //&& value !== null
		                          {
		                       //       //console.log("undefined");
		                              query += encodeURIComponent(name) + "=" + encodeURIComponent(value) + "&";
		                          }
		                      }


		                      return query.length ? query.substr(0, query.length - 1) : query;
		                  };


		                  return angular.isObject(data) && String(data) !== "[object File]" ? param(data) : data;
		              }]


		              $httpProvider.defaults.useXDomain = true;
		              // delete $httpProvider.defaults.headers.common["X-Requested-With"];
		          }])
	configMod.constant("ENV", {
	    "name": "production",
	    "debug": false,
	    "api": "http://www.baicai.net/index.php?m=api&c=app",
	    "siteUrl":"http://www.baicai.net",
	    "imgUrl":"http://www.baicai.net",
	});