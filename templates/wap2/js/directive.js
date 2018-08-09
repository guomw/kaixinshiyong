angular.module('starter.directive', [])



/**
 * @name [验证两次密码是否一致]
 */

.directive("repeat", [function() {
    return {
        restrict: 'A',
        require: "ngModel",
        link: function(scope, element, attrs, ctrl) {
            if (ctrl) {
                var otherInput = element.inheritedData("$formController")[attrs.repeat];

                var repeatValidator = function(value) {


                    //console.log(otherInput.$viewValue);

                    var validity = value === otherInput.$viewValue;
                    ctrl.$setValidity("repeat", validity);
                    return validity ? value : undefined;
                };

                ctrl.$parsers.push(repeatValidator);
                ctrl.$formatters.push(repeatValidator);

                otherInput.$parsers.push(function(value) {
                    ctrl.$setValidity("repeat", value === ctrl.$viewValue);
                    return value;
                });
            }
        }
    };
}])




/*
 name [隐藏底部菜单栏]
 */

.directive('hideTabs',['$rootScope',function($rootScope) {
    return {
        restrict: 'AE',
        link: function($scope) {
            $rootScope.hideTabs = 'tabs-item-hide';
             $scope.$on('$destroy', function() {
                $rootScope.hideTabs = '';
            })
        }
    }
}])

/*.directive('hideTabs', function($rootScope, $location) {
    return {
        restrict: 'AE',
        link: function(scope, element, attributes) {
            scope.$on('$ionicView.beforeEnter', function() {
                scope.$watch(attributes.hideTabs, function(value) {
                    $rootScope.hideTabs = value;
                    if ($location.path() == '/main_tab/mian' || $location.path() == '/main_tab/mian') {

                    }

                })
            })
        }
    }
})*/


/**
 * @name [为银行添加空格]
 */


.directive("bank", function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, elm, attrs,ctrl) {
            elm.bind('keyup',function() {
                 var  BankNo = elm[0]; 

                    if (BankNo.value == "") return;
                    var account = new String(BankNo.value);
                    account = account.substring(0, 23); /*帐号的总数, 包括空格在内 */
                    if (account.match(".[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{7}") == null) {
                        /* 对照格式 */
                        if (account.match(".[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{7}|" + ".[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{7}|" +
                                ".[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{7}|" + ".[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{7}") == null) {
                            var accountNumeric = accountChar = "",
                                i;
                            for (i = 0; i < account.length; i++) {
                                accountChar = account.substr(i, 1);
                                if (!isNaN(accountChar) && (accountChar != " ")) accountNumeric = accountNumeric + accountChar;
                            }
                            account = "";
                            for (i = 0; i < accountNumeric.length; i++) { /* 可将以下空格改为-,效果也不错 */
                                if (i == 4) account = account + " "; /* 帐号第四位数后加空格 */
                                if (i == 8) account = account + " "; /* 帐号第八位数后加空格 */
                                if (i == 12) account = account + " "; /* 帐号第十二位后数后加空格 */
                                if (i == 16 ) account = account + " ";
                                account = account + accountNumeric.substr(i, 1)
                            }
                        }
                    } else {
                        account = " " + account.substring(1, 5) + " " + account.substring(6, 10) + " " + account.substring(14, 18) + "-" + account.substring(18, 25);
                    }
                    if (account != BankNo.value) BankNo.value = account;
                }

            );
        }
    };
})



/**
 * @name [根据身份证号 计算出生年月日 年龄 和日期]
 */

.directive("datepicker", function() {
    return {
        restrict: "A",
        link: function(scope, element, attr) {
            element.bind("blur", function() {
                //console.log(element);
                var UUserCard = element[0].value;
                if (!UUserCard) return false;
                //问题在这里，无法取到变化的值，而且加上这个，连初始的值都无法显示出来。
                    //console.log(newValue);
                    //获取出生日期 
                    scope.Year_birth = UUserCard.substring(6, 10); //出生年
                    scope.Birth_month = UUserCard.substring(10, 12);
                    scope.Date_birth = UUserCard.substring(12, 14);
                    //获取性别 

                    scope.rq = UUserCard.substring(6, 10) + "-" + UUserCard.substring(10, 12) + "-" + UUserCard.substring(12, 14);
                    //获取性别 
                    if (parseInt(UUserCard.substr(16, 1)) % 2 == 1) {

                        scope.Gender = "男";

                        //是男则执行代码 ... 
                    } else {
                        scope.Gender = "女";
                        //是女则执行代码 ... 
                    }

                    //获取年龄 
                    var myDate = new Date();
                    var month = myDate.getMonth() + 1;
                    var day = myDate.getDate();
                    var age = myDate.getFullYear() - UUserCard.substring(6, 10) - 1;
                    if (UUserCard.substring(10, 12) < month || UUserCard.substring(10, 12) == month && UUserCard.substring(12, 14) <= day) {
                        age++;
                    }

                    scope.age = age;
                    // alert(age); 


            })
        }
    }
})


/**
 * @name [验证手机号是否被占用]
 */

.directive('repeatphone', ['User_registerFactory',function(User_registerFactory) {
    return {

        restrict: 'A',
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            elm.bind('blur',
                function() {

                    var r = elm[0].value;

                    //发起后端请求
                    User_registerFactory.getsms(r);

                    // 接收请求结果通知
                    scope.$on('NewsContent.getsms',
                        function() {
                            var phone_status = User_registerFactory.setsms();
                            //console.log(phone_status);

                            if (phone_status === 1) {

                                ctrl.$setValidity('repeatphone', true);

                            } else if (phone_status === 0) {

                                ctrl.$setValidity('repeatphone', false);

                            }

                        });

                }

            );
        }
    };
}])



/**
 * @name [验证手机号,并获取手机验证码]
 */

.directive('userphone', ['$ionicLoading', '$interval', 'User_registerFactory', 'User_registerFactory',function($ionicLoading, $interval, User_registerFactory, User_registerFactory) {
    return {

        restrict: 'A',
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            elm.bind('click', function() {
                    var phone = elm[0].value;
                    //console.log(elm);
                    //请求服务器验证手机号
                    User_registerFactory.getsms(phone);

                    //收到结果通知
                    scope.$on('NewsContent.getsms', function() {
                        var r = User_registerFactory.setsms();
                        if (r.status == 0) {
                            scope.isDisabled = false;
                            scope.user_phone_state = true;

                            $ionicLoading.show({
                                noBackdrop: true,
                                template: r.msg,
                                duration: 1000
                            });


                        } else if (r.status == 1) {
                            User_registerFactory.send_sms(phone);

                            scope.$on('NewsContent.sendsms', function() {

                                var setsms = User_registerFactory.get_send_sms();

                                if (setsms.status == 0) {

                                    // 返回失败原因
                                    $ionicLoading.show({
                                        noBackdrop: true,
                                        template: setsms.msg,
                                        duration: 1500
                                    });
                                    scope.isDisabled = true;
                                    return;

                                } else if (setsms.status == 1) {
                                    //console.log("发送成功");

                                    $ionicLoading.show({
                                        noBackdrop: true,
                                        template: "验证码已发送到您手机！",
                                        duration: 1000
                                    });

                                    var iNow = 60;
                                    scope.text = '请等待' + iNow + '秒';
                                    scope.isDisabled = true;

                                    var timer = $interval(function() {
                                            iNow--;
                                            scope.text = '请等待' + iNow + '秒';

                                            if (iNow == 0) {
                                                $interval.cancel(timer);
                                                scope.text = '再次获取';
                                                scope.isDisabled = false;
                                            }

                                        },
                                        1000);

                                }

                            });

                        } else {

                            $ionicLoading.show({
                                noBackdrop: true,
                                template: "发送失败，请重新获取！",
                                duration: 1000
                            });

                            scope.isDisabled = false;

                        }

                    });

                }

            );
        }
    };
}])

//强制刷新当前页面

.directive('diHref', ['$location', '$route',function($location, $route) {
       return function(scope, element, attrs) {
             scope.$watch('diHref', function() {
                if(attrs.diHref) {
                   element.attr('href', attrs.diHref);
                   element.bind('click', function(event) {
                      scope.$apply(function(){
                           if($location.path() == attrs.diHref) $route.reload();
                      });
                   });
                }
            });
        }
    }])


/**
 * @name [验证短信验证码]
 */

.directive('smsauthentication', ['$parse', 'User_registerFactory',function($parse, User_registerFactory) {
    return {

        scope: {
            number: '=',

        },

        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            elm.bind('keyup', function() {

                    // //console.log(elm[0].value);
                    //console.log(elm);
                    // //console.log(attrs);
                    //console.log(elm.smsauthentication);

                    //  var getter = $parse(smsauthentication);    

                    // options = scope.$eval(attrs.smsauthentication);

                    //console.log(getter.assign);
                    //发起后端请求
                    User_registerFactory.register_check_sms();

                    // 接收请求结果通知
                    scope.$on('NewsContent.registerchecksms', function() {
                        var sms_state = User_registerFactory.get_register_check_sms();
                        //console.log(sms_state);

                        if (sms_state === 0) {

                            ctrl.$setValidity('sms', true);


                        } else if (sms_state === 1) {

                            ctrl.$setValidity('sms', false);


                        }

                    });

                }


            );
        }
    };
}])

.directive('fileModel', ['$parse', function ($parse) {
  return {
    restrict: 'A',
    link: function(scope, element, attrs, ngModel) {
      var model = $parse(attrs.fileModel);
      var modelSetter = model.assign;
      element.bind('change', function(event){
        scope.$apply(function(){
          modelSetter(scope, element[0].files[0]);
        });
        //附件预览
        scope.file = (event.srcElement || event.target).files[0];
        scope.onFileSelect(scope.file);
      });
    }
  };
}]);
