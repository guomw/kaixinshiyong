/**
 * Created by htzhanglong on 2015/8/2.
 */
angular
  .module('starter.controllers', [])

  /**
   * @name [平台启动控制器]
   */

  /*
    name 首页控制器
   */

  .controller('HomeCtrl', [
    '$rootScope',
    '$state',
    '$scope',
    '$ionicSlideBoxDelegate',
    '$timeout',
    'ENV',
    'trial_listFactory',
    'StorageFactory',
    'configFactory',
    function(
      $rootScope,
      $state,
      $scope,
      $ionicSlideBoxDelegate,
      $timeout,
      ENV,
      trial_listFactory,
      StorageFactory,
      configFactory
    ) {
      //获取首页幻灯片信息
      $scope.showloading = true
      $scope.showscroll = true
      $scope.tj_showloading = true

      $scope.quyu = 1

      //获取首页推荐商品
      //获取网站基本信息
      configFactory.set_webinfo()
      configFactory.set_focus()
      trial_listFactory.set_goods_isrecommend('trial')

      // 首页下拉拖动刷新 更新幻灯片  更新推荐 更新最新

      $scope.doRefresh = function() {
        $scope.$broadcast('scroll.refreshComplete')
        //广播通知
      }

      $scope.qidongyu = function() {
        $scope.quyu = 1
        $rootScope.hideTabs = ''
      }

      $scope.go_changed = function(index) {
        if (index == 2) {
          var timer = $timeout(function() {
            $scope.quyu = 1
            $rootScope.hideTabs = ''
          }, 3000)
        }
      }

      //首页上拉加载更多最新上线

      //上拉加载
      $scope.index_loadMore = function() {
        trial_listFactory.getMoreTopics()
      }

      $scope.jifen = function() {
        $state.go('tab.jifenduihuan') //路由跳转登录
      }

      $scope.dianji = function() {
        $state.go('tab.task') //积分兑换
      }

      trial_listFactory.getTopTopics()

      //接收推荐商品通知

      $scope.$on('trial_listFactory.set_goods_isrecommend', function() {
        $scope.tj_goods = trial_listFactory.get_goods_isrecommend()
        $scope.tj_showloading = false

        $scope.tj_goods2 = $scope.tj_goods.slice(3, 6)
        $scope.tj_goods3 = $scope.tj_goods.slice(6, 9)
      })

      //接收最新商品传过来的通知
      $scope.$on('PortalList.portalsUpdated', function() {
        $scope.goods = trial_listFactory.getArticles()
        $scope.showloading = false
        $scope.$broadcast('scroll.infiniteScrollComplete')
        $scope.index_hasNextPage = trial_listFactory.hasNextPage()

        //console.log($scope.goods);
      })

      $scope.$on('configFactory.set_webinfo', function() {
        $scope.webinfo = configFactory.get_webinfo()
      })

      $scope.$on('configFactory.set_focus', function() {
        $scope.focus = configFactory.get_focus()
      })
    }
  ])

  /**
   * @param  {[type]}
   * @param  {[type]}
   * @param  {String}
   * @return {[type]}
   */
  .controller('ArticleCtrl', [
    '$scope',
    'goodslists',
    'ENV',
    function($scope, goodslists, ENV) {
      $scope.name = 'ArticleCtrl'
      $scope.ENV = ENV

      //免费试用数据

      goodslists.getTopTopics()
      //接收到刚才传过来的通知
    }
  ])

  //获取所有活动分类
  .controller('categorylists', ['$scope', function($scope) {}])

  .controller('rebate_list', [
    '$rootScope',
    '$scope',
    'ENV',
    '$timeout',
    '$ionicPopover',
    'categorylistsFactory',
    'rebate_listFactory',
    '$stateParams',
    '$ionicSideMenuDelegate',
    function(
      $rootScope,
      $scope,
      ENV,
      $timeout,
      $ionicPopover,
      categorylistsFactory,
      rebate_listFactory,
      $stateParams,
      $ionicSideMenuDelegate
    ) {
      //console.log($stateParams);
      $scope.ENV = ENV
      //显示底部tabs
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = ''
      })

      // 显示加载中图标
      $scope.showloading = true

      $scope.rebate_hasNextPage = false

      //全部分类展示

      $scope.toggleLeft = function() {
        $ionicSideMenuDelegate.toggleLeft()
      }

      $scope.hide = function() {
        $ionicLoading.hide()
      }

      //请求返利商品列表
      rebate_listFactory.getTopTopics()

      //接收到刚才传过来的通知
      $scope.$on('PortalList.portalsUpdated', function() {
        $scope.goods = rebate_listFactory.getArticles()
        $scope.showloading = false
        $scope.rebate_hasNextPage = rebate_listFactory.hasNextPage()
        $scope.$broadcast('scroll.infiniteScrollComplete')
      })

      //选择指定栏目的时候操作
      $scope.good1 = function(catid) {
        rebate_listFactory.goods_catid_list(catid)
        $scope.showloading = true
      }

      //下拉刷新
      $scope.doRefresh = function() {
        rebate_listFactory.getTopTopics()

        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载
      $scope.loadMore = function() {
        rebate_listFactory.getMoreTopics()
      }

      // 商品排序
      $scope.changeTab = function(order_id, index) {
        var a = document.getElementById('sub_header_list').getElementsByTagName('a')
        for (var i = 0; i < 4; i++) {
          a[i].className = 'button button-clear '
        }
        a[index].className = 'button button-clear sub_button_select'

        //console.log(order_id);
        $scope.showloading = true

        //数据请求
        rebate_listFactory.goods_catid_list_order(order_id)
      }

      //获取产品分类

      categorylistsFactory.category_list()
      //获取产品分类

      //接收产品分类通知

      $scope.$on('categorylistsFactory.category_list', function() {
        $scope.categorylists = categorylistsFactory.get_listdata()
        //console.log($scope.categorylists);
      })
    }
  ])

  // 获取免费试用列表数据
  .controller('trial_list', [
    '$rootScope',
    '$scope',
    'ENV',
    '$timeout',
    '$ionicPopover',
    '$ionicLoading',
    'trial_listFactory',
    '$stateParams',
    '$ionicSideMenuDelegate',
    'categorylistsFactory',
    function(
      $rootScope,
      $scope,
      ENV,
      $timeout,
      $ionicPopover,
      $ionicLoading,
      trial_listFactory,
      $stateParams,
      $ionicSideMenuDelegate,
      categorylistsFactory
    ) {
      //console.log($stateParams);
      $scope.ENV = ENV
      //显示底部tabs
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = ''
      })

      // 显示加载中图标
      $scope.showloading = true

      $scope.trial_list_hasNextPage = false

      //全部分类展示

      $scope.toggleLeft = function() {
        $ionicSideMenuDelegate.toggleLeft()
      }

      $scope.hide = function() {
        $ionicLoading.hide()
      }

      //请求试用商品列表
      trial_listFactory.getTopTopics()
      //接收到刚才传过来的通知
      $scope.$on('PortalList.portalsUpdated', function() {
        $scope.goods = trial_listFactory.getArticles()
        $scope.showloading = false
        $scope.$broadcast('scroll.infiniteScrollComplete')
        $scope.trial_list_hasNextPage = trial_listFactory.hasNextPage()
      })

      //选择指定栏目的时候操作
      $scope.good1 = function(catid) {
        $scope.showloading = true

        trial_listFactory.goods_catid_list(catid)
      }

      //下拉刷新
      $scope.doRefresh = function() {
        trial_listFactory.getTopTopics()

        // $scope.goods = $scope.goods.unshift(items);
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载
      $scope.loadMore = function() {
        trial_listFactory.getMoreTopics()
      }

      // 获取是否有下一页
      $scope.hasNextPage = function() {
        return trial_listFactory.hasNextPage()
      }

      // 商品排序
      $scope.changeTab = function(order_id, index) {
        var a = document.getElementById('sub_header_list').getElementsByTagName('a')

        for (var i = 0; i < a.length; i++) {
          a[i].className = 'button button-clear '
        }
        a[index].className = 'button button-clear sub_button_select'

        $scope.showloading = true

        //数据请求
        trial_listFactory.goods_catid_list_order(order_id)
      }

      categorylistsFactory.category_list()
      //获取产品分类

      //接收产品分类通知

      $scope.$on('categorylistsFactory.category_list', function() {
        $scope.categorylists = categorylistsFactory.get_listdata()
        //console.log($scope.categorylists);

        //  alert('产品分类通知来了');
      })
    }
  ])

  // 获取免费试用红包的列表数据
  .controller('trial_list-red', [
    '$rootScope',
    '$scope',
    'ENV',
    '$timeout',
    '$ionicPopover',
    '$ionicLoading',
    'trial_listFactory',
    '$stateParams',
    '$ionicSideMenuDelegate',
    'categorylistsFactory',
    function(
      $rootScope,
      $scope,
      ENV,
      $timeout,
      $ionicPopover,
      $ionicLoading,
      trial_listFactory,
      $stateParams,
      $ionicSideMenuDelegate,
      categorylistsFactory
    ) {
      //console.log($stateParams);
      $rootScope.ENV = ENV

      // 显示加载中图标
      $scope.showloading = true

      $scope.trial_list_hasNextPage = false

      //显示底部tabs
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = ''
      })

      //请求红包试用商品列表
      trial_listFactory.getTopTopics_red()

      //接收到刚才传过来的通知
      $scope.$on('PortalList.portalsUpdated', function() {
        $scope.goods = trial_listFactory.getArticles()
        $scope.showloading = false
        $scope.$broadcast('scroll.infiniteScrollComplete')
        $scope.trial_list_hasNextPage = trial_listFactory.hasNextPage()
      })

      //下拉刷新
      $scope.doRefresh = function() {
        trial_listFactory.getTopTopics_red()

        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载
      $scope.loadMore = function() {
        trial_listFactory.getMoreTopics_red()
      }

      // 获取是否有下一页
      $scope.hasNextPage = function() {
        return trial_listFactory.hasNextPage()
      }

      // 商品排序
      $scope.changeTab = function(order_id, index) {
        var a = document.getElementById('sub_header_list').getElementsByTagName('a')

        for (var i = 0; i < a.length; i++) {
          a[i].className = 'button button-clear '
        }
        a[index].className = 'button button-clear sub_button_select'

        $scope.showloading = true

        //数据请求
        trial_listFactory.goods_catid_list_order(order_id)
      }
    }
  ])

  .controller('articlehelplist', [
    '$scope',
    'articlehelplistFactory',
    function($scope, articlehelplistFactory) {
      articlehelplistFactory.all(2)

      $scope.$on('articlehelplistFactory.all', function() {
        $scope.articlehelplist = articlehelplistFactory.get_lists()
      })

      //获取帮助中心列表
    }
  ])

  .controller('article_catid_helplist', [
    '$scope',
    'articlehelplistFactory',
    '$stateParams',
    function($scope, articlehelplistFactory, $stateParams) {
      $scope.title = '帮助中心2'
      $scope.articlehelplist = articlehelplistFactory.catid_helplist($stateParams.catid)

      // //console.log($scope.articlehelplist);

      //获取帮助中心指定栏目列表
    }
  ])

  .controller('article_catid_show_list', [
    '$scope',
    'articlehelplistFactory',
    '$stateParams',
    function($scope, articlehelplistFactory, $stateParams) {
      $scope.title = '帮助中心2'
      $scope.articlehelplist = articlehelplistFactory.article_catid_show_list($stateParams.catid)

      //console.log($stateParams);

      //获取帮助中心指定栏目文章列表
    }
  ])

  //获取帮助中心单个文章内容

  .controller('article_show', [
    '$scope',
    'article_showFactory',
    '$stateParams',
    function($scope, article_showFactory, $stateParams) {
      $scope.title = '帮助中心'

      var aid = $stateParams.id

      article_showFactory.get(aid)
      //console.log($stateParams.aid);

      $scope.$on('NewsContent.articleshow', function() {
        $scope.articledata = article_showFactory.getArticle()
        //console.log($scope.articledata);
      })
    }
  ])

  //获取网站产品分类

  .controller('article_catid_helplist', [
    '$scope',
    'articlehelplistFactory',
    '$stateParams',
    function($scope, articlehelplistFactory, $stateParams) {
      $scope.title = '帮助中心2'
      $scope.articlehelplist = articlehelplistFactory.catid_helplist($stateParams.catid)

      // //console.log($scope.articlehelplist);

      //获取帮助中心指定栏目列表
    }
  ])

  .controller('Userhome', [
    '$rootScope',
    '$scope',
    '$ionicLoading',
    '$state',
    'StorageFactory',
    'UserPersonalFactory',
    'qiandaoFactory',
    function($rootScope, $scope, $ionicLoading, $state, StorageFactory, UserPersonalFactory, qiandaoFactory) {
      //每次加载前 判断是否登录

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = ''
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.userInfo = ''
          $scope.logo_status = 0
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid
          //获取用户签名
          random = StorageFactory.get('user').data.random
          $scope.user = StorageFactory.get('user')
          UserPersonalFactory.set_userinfo(userid, random)
          $scope.logo_status = 1
        }
      })

      $scope.user_showloading = false
      // 第二步 接收请求通知结果
      $scope.$on('UserPersonal.set_userinfo', function() {
        $scope.userInfo = UserPersonalFactory.get_userinfo()

        //console.log($scope.userInfo);
        if ($scope.userInfo.status == 1) {
          //将用户信息写入本地缓存
          StorageFactory.set('profile', $scope.userInfo)
          $scope.logo_status = 1 //会员登录状态
          $scope.user_showloading = false
        } else {
          $scope.logo_status = 0
          StorageFactory.remove2()
        }
      })

      //我的设置
      $scope.user_profile = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.user_profile')
      }

      //我的试用

      $scope.user_order_trial = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.trial_order')
      }

      //我的闪电试用

      $scope.user_order_commission = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.commission_order')
      }

      //我的返利
      $scope.user_order_rebate = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.rebate_order')
      }

      //我的日赚任务
      $scope.user_order_task = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.task_order')
      }

      //我的积分兑换
      $scope.user_order_jifen = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.user_jifen_order')
      }

      //余额提现
      $scope.user_deposite = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        if ($scope.userInfo.name_status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您的实名认证正在审核当中,请耐心等待,认证通过即可申请提现！',
            duration: 3000
          })
          return false
        } else if ($scope.userInfo.name_status == -1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您的实名认证审核失败了,请修改后再次提交,认证通过即可申请提现！',
            duration: 3000
          })

          //跳转实名认证
          $state.go('tab.user_profile_card')
          return false
        } else if ($scope.userInfo.name_status == '' || $scope.userInfo.name_status == null) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您还未进行实名认证,为了您的账户和资金安全,请先进行实名认证,认证通过即可申请提现！',
            duration: 3000
          })

          //跳转实名认证
          $state.go('tab.user_profile_card')
          return false
        }

        if ($scope.userInfo.bank_status == 1 || $scope.userInfo.alipay_status == 1) {
          $state.go('tab.user_deposite')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您还未绑定银行卡或支付宝,绑定之后即可申请提现！',
            duration: 3000
          })
          $state.go('tab.user_profile')
          return false
        }
      }

      //账户明细

      $scope.getmessage = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.user_log') //路由跳转账户明细页面
      }

      //站内信
      $scope.announce = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }
        $state.go('tab.user_announce')
      }

      //修改头像和昵称
      $scope.user_nickname = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }
        $state.go('tab.user_profile_nickname')
      }

      //每日签到
      $scope.user_qiandao = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        //发起后端请求
        qiandaoFactory.set_sign(userid, random)
      }

      $scope.$on('qiandaoFactory.set_sign', function() {
        $scope.sign = qiandaoFactory.get_sign()
        $ionicLoading.show({
          noBackdrop: true,
          template: $scope.sign.msg,
          duration: 1500
        })
        //改变签到状态
      })
    }
  ])

  // 我的信息 站内信

  .controller('user_announce', [
    '$rootScope',
    '$scope',
    '$state',
    'user_announceFactory',
    'StorageFactory',
    function($rootScope, $scope, $state, user_announceFactory, StorageFactory) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'

        // 获取用户id
        userid = StorageFactory.get('user').data.userid

        //获取用户签名
        random = StorageFactory.get('user').data.random
      })

      //如果不存在会员信息 则跳转登陆页面
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        $state.go('tab.user_login') //路由跳转登录
        return false
      }

      // 显示加载中图标
      $scope.showloading = true

      $scope.zz_hasNextPage = false

      //默认显示普通消息

      $scope.zz_type = 1

      // 获取服务器数据
      user_announceFactory.announce_list(userid, random, 1)

      //下拉刷新
      $scope.doRefresh = function() {
        user_announceFactory.announce_list(userid, random, $scope.zz_type)
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载
      $scope.loadMore = function() {
        user_announceFactory.getMoreTopics(userid, random, $scope.zz_type)
      }

      // 获取是否有下一页
      $scope.hasNextPage = function() {
        return user_announceFactory.hasNextPage()
      }

      //接收通知

      $scope.$on('user_announceFactory.announce_list', function() {
        $scope.announcedata = user_announceFactory.get_listdata()
        $scope.showloading = false
        //console.log($scope.announcedata);
        $scope.zz_hasNextPage = user_announceFactory.hasNextPage()
        $scope.$broadcast('scroll.infiniteScrollComplete')
      })

      //获取单条信息，并且标记状态为已读

      $scope.changeTab = function(index, type) {
        $scope.zz_type = type

        //console.log(type);
        //数据请求
        user_announceFactory.announce_list(userid, random, $scope.zz_type)
        $scope.showloading = true
      }

      $scope.changeshow = function(type, id) {
        $state.go('tab.user_announce_show', { type: type, id: id })
      }

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })
    }
  ])

  //获取站内信详情

  .controller('announce_show', [
    '$scope',
    '$rootScope',
    '$stateParams',
    'user_announceFactory',
    function($scope, $rootScope, $stateParams, user_announceFactory) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      var aid = $stateParams.id
      var type = $stateParams.type
      $scope.showloading = true

      user_announceFactory.set_announce_show(type, aid)

      $scope.$on('user_announceFactory.set_announce_show', function() {
        $scope.show_announce = user_announceFactory.get_set_announce_show()

        //console.log($scope.show_announce);
        $scope.showloading = false
      })
    }
  ])

  /**
   * @name [用户登陆]
   */

  .controller('User_login', [
    '$rootScope',
    '$scope',
    '$state',
    '$location',
    '$ionicHistory',
    '$ionicLoading',
    '$timeout',
    'UserloginFactory',
    'StorageFactory',
    'UserPersonalFactory',
    'configFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $state,
      $location,
      $ionicHistory,
      $ionicLoading,
      $timeout,
      UserloginFactory,
      StorageFactory,
      UserPersonalFactory,
      configFactory,
      ENV
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'

        url = !$ionicHistory.viewHistory().forwardView ? '' : $ionicHistory.viewHistory().forwardView.url

        //清除本地所有缓存数据。
        StorageFactory.remove('user')
        StorageFactory.remove('profile')
        StorageFactory.remove('taobao')

        //获取网站logo
        configFactory.set_webinfo()
      })

      $scope.$on('configFactory.set_webinfo', function() {
        $scope.webinfo = configFactory.get_webinfo()

        $scope.logo = ENV.imgUrl + $scope.webinfo.logo
      })

      var jilu = $location.path()

      $scope.user = {
        name: '',
        password: ''
      }

      $scope.login = function() {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在登录...',
          duration: 1000
        })

        UserloginFactory.login($scope.user.name, $scope.user.password)
      }

      $scope.qqweibo = function() {
        window.plugins.Pgbaidulogin.qqweibo(
          function(success) {
            // 将信息传入后台查询
            var data = JSON.parse(success)
            UserloginFactory.pingtai_login(data)
          },
          function(fail) {
            alert('encoding failed: ' + fail)
          }
        )
      }

      $scope.qq = function() {}
      $scope.sina = function() {
        window.plugins.Pgbaidulogin.sina(
          function(success) {
            var data = JSON.parse(success)
            UserloginFactory.pingtai_login(data)
          },
          function(fail) {
            alert('encoding failed: ' + fail)
          }
        )
      }

      $scope.$on('User.loginUpdated', function() {
        var userRel = UserloginFactory.getCurrentUser()
        if (userRel.status == 0) {
          //登陆失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '登录成功',
            duration: 1000
          })
          //会员信息记录到缓存
          StorageFactory.set('user', userRel)
          UserPersonalFactory.set_userinfo(userRel.data.userid, userRel.data.random)

          // jpushService._setAlias(userRel.data.userid);

          // $ionicLoading.show({
          //   noBackdrop: true,
          //   template: "即将为您返回之前页面...",
          //   duration: 1000
          // });

          var timer = $timeout(function() {

              if(url =="/tab/user/forget" || url == "/tab/user/forget_2"  ){
                $state.go('tab.user'); //路由跳转登录
                return false;

              }
                $ionicHistory.goBack(-1);
              },100);

          // $state.go('tab.user') //登录成功 跳转会员中心
          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '非法登录请求',
            duration: 1500
          })

          return false
        }
      })
    }
  ])

  /**
   * @name [找回密码]
   */

  .controller('Userforget', [
    '$scope',
    '$rootScope',
    '$state',
    '$ionicLoading',
    '$ionicHistory',
    'UserForgetFactory',
    function($scope, $rootScope, $state, $ionicLoading, $ionicHistory, UserForgetFactory) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $ionicHistory.nextViewOptions({
          disableBack: true
        })
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      //  获取用户名的验证码
      $scope.set_username = function(user_name) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/
        var filter2 = /^1[3|4|5|8][0-9]\d{4,8}$/

        if (filter.test(user_name)) {
          username_type = '邮箱:' + user_name
        } else if (filter2.test(user_name)) {
          username_type = '手机' + user_name
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您输入不是手机或者邮箱',
            duration: 1000
          })
          return false
        }

        //第二步请求后台验证当前用户名
        UserForgetFactory.set_username(user_name)

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })
        //console.log(user_name);
      }

      // 接收请求处理
      $scope.$on('Userforget.set_username', function() {
        var userRel = UserForgetFactory.get_username()
        //console.log(userRel);
        //console.log(typeof userRel);
        if (userRel.status == 0) {
          //登陆失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          //发送成功

          $ionicLoading.show({
            noBackdrop: true,
            template: '验证码已成功发送至您' + username_type,
            duration: 2000
          })
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })

      //第三步 后台请求验证手机验证码是否正确

      $scope.set_username_code = function(user_name, sms) {
        $rootScope.user_name = user_name

        //第二步请求后台验证当前用户名
        UserForgetFactory.set_username_code(user_name, sms)

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })
        //console.log(user_name);
      }

      // 接收用户名和验证码验证结果
      $scope.$on('Userforget.set_username_code', function() {
        var userRel = UserForgetFactory.get_username_code()
        //console.log(userRel);
        //console.log(typeof userRel);
        if (userRel.status == 0) {
          //验证失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          return false
        } else if (userRel.status == 1) {
          //验证成功

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          $state.go('tab.user_forget2') //验证成功，跳转重置密码页面
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })

      //重置用户密码

      $scope.set_username_password = function(password, repeat_password) {
        var user = UserForgetFactory.get_username_code()

        //console.log(user);

        var userid = user.data.userid

        var random = user.data.random

        //console.log(userid, random);

        //console.log($rootScope.user_name);

        if (password !== repeat_password) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲 两次密码不一致,请检查',
            duration: 2000
          })
          return false
        }

        //第二步请求后台 传入用户名 密码 签名验证
        UserForgetFactory.set_username_password($rootScope.user_name, userid, password, random)

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })
      }

      //接收后台重置密码请求结果
      $scope.$on('Userforget.set_username_password', function() {
        var userRel = UserForgetFactory.get_username_password()

        //console.log(userRel);

        if (userRel.status == 0) {
          //验证失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          return false
        } else if (userRel.status == 1) {
          //重置成功

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          $state.go('tab.user_login') //验证成功，跳转登录页面
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })

          return false
        }
      })
    }
  ])

  /**
   * @name [用户注册]
   */

  .controller('User_register', [
    '$ionicPlatform',
    '$rootScope',
    '$scope',
    '$state',
    '$ionicLoading',
    '$interval',
    'User_registerFactory',
    'StorageFactory',
    'User_register1Factory',
    function(
      $ionicPlatform,
      $rootScope,
      $scope,
      $state,
      $ionicLoading,
      $interval,
      User_registerFactory,
      StorageFactory,
      User_register1Factory
    ) {
      //如果存在会员信息 则不允许注册

      //提交注册请求第一步
      $scope.register = function(user_phone, user_sms, password, repeat_password) {
        //console.log(password, repeat_password);

        if (password !== repeat_password) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '两次输入的密码不匹配...',
            duration: 1000
          })

          return false
        }

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })

        //发起后端注册请求

        User_register1Factory.set_register(user_phone, user_sms, password)
      }

      //收到注册结果结果通知
      $scope.$on('User_register1Factory.setregister', function() {
        var reg_status = User_register1Factory.get_register()

        if (reg_status.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: reg_status.msg,
            duration: 1000
          })
        } else if (reg_status.status == 1) {
          //注册成功
          $ionicLoading.show({
            noBackdrop: true,
            template: reg_status.msg,
            duration: 1000
          })

          $state.go('tab.user')
          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲程序员哥哥正在抢修,请稍后',
            duration: 1000
          })
        }
      })

      $scope.text = '获取验证码'

      $scope.getsms = function(phone_id) {
        // console.log(phone_id);

        $scope.isDisabled = true
        //请求服务器验证手机号
        User_registerFactory.getsms(phone_id)
        //收到结果通知
        $scope.$on('NewsContent.getsms', function() {
          var r = User_registerFactory.setsms()
          if (r.status == 0) {
            $scope.isDisabled = false

            $ionicLoading.show({
              noBackdrop: true,
              template: r.msg,
              duration: 1000
            })

            return false
          } else if (r.status == 1) {
            //执行验证码发送操作
            User_registerFactory.send_sms(phone_id)

            $scope.$on('NewsContent.sendsms', function() {
              var setsms = User_registerFactory.get_send_sms()

              if (setsms.status == 0) {
                // 返回失败原因
                $ionicLoading.show({
                  noBackdrop: true,
                  template: setsms.msg,
                  duration: 1500
                })
                $scope.isDisabled = true

                return false
              } else if (setsms.status == 1) {
                //console.log("发送成功");

                $ionicLoading.show({
                  noBackdrop: true,
                  template: '验证码已发送到您手机:' + phone_id,
                  duration: 1000
                })

                var iNow = 60
                $scope.text = '请等待' + iNow + '秒'
                $scope.isDisabled = true

                var timer = $interval(function() {
                  iNow--
                  $scope.text = '请等待' + iNow + '秒'

                  if (iNow == 0) {
                    $interval.cancel(timer)
                    $scope.text = '再次获取'
                    $scope.isDisabled = false
                  }
                }, 1000)
              }
            })
          } else {
            $ionicLoading.show({
              noBackdrop: true,
              template: '发送失败，请重新获取！',
              duration: 1000
            })

            $scope.isDisabled = false
          }
        })
      }
    }
  ])

  /*
    name [用户设置 ]
   */

  .controller('UserPersonal', [
    '$scope',
    '$rootScope',
    '$ionicLoading',
    '$state',
    '$ionicActionSheet',
    '$timeout',
    'UserloginFactory',
    'StorageFactory',
    'UserPersonalFactory',
    function(
      $scope,
      $rootScope,
      $ionicLoading,
      $state,
      $ionicActionSheet,
      $timeout,
      UserloginFactory,
      StorageFactory,
      UserPersonalFactory
    ) {
      var storageKey = 'user'

      //页面加载之前的事件
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        //如果不存在会员信息 则跳转登陆页面
        if (
          !StorageFactory.get(storageKey) ||
          (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
        ) {
          $state.go('tab.user_login') //路由跳转
          return false
        }
        userid = StorageFactory.get(storageKey).data.userid
        random = StorageFactory.get(storageKey).data.random
        //发起后端请求更新详细信息
        UserPersonalFactory.set_userinfo(userid, random)

        $scope.random = StorageFactory.get('user').data
      })

      $scope.showloading = true //显示加载中等待页面

      // 第二步 接收请求通知结果
      $scope.$on('UserPersonal.set_userinfo', function() {
        $scope.userInfo = UserPersonalFactory.get_userinfo()

        if ($scope.userInfo.status == 0) {
          //清除信息
          $state.go('tab.user_login') //路由跳转
          StorageFactory.remove2()
          return false
        }
        //将用户信息写入本地缓存
        StorageFactory.set('profile', $scope.userInfo)
        $scope.showloading = false
      })

      //下拉更新用户信息
      $scope.doRefresh = function() {
        UserPersonalFactory.set_userinfo(userid, random)
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //我的昵称
      $scope.profile_nickname = function() {
        $state.go('tab.user_profile_nickname')
      }

      // 我的手机
      $scope.profile_phone = function() {
        $state.go('tab.user_profile_nickname')
      }

      // 我的qq
      $scope.profile_qq = function() {
        $state.go('tab.user_profile_qq')
      }

      // 我的邮箱
      $scope.profile_email = function() {
        $state.go('tab.user_profile_email')
      }

      // 收货地址
      $scope.profile_address = function() {
        $state.go('tab.user_profile_address')
      }

      // 修改密码
      $scope.profile_password = function() {
        $state.go('tab.user_profile_password')
      }

      // 淘宝帐号
      $scope.profile_taobao = function() {
        $state.go('tab.user_profile_taobao')
      }

      // 实名认证
      $scope.profile_card = function() {
        $state.go('tab.user_profile_card')
      }

      // 支付宝绑定
      $scope.profile_allpay = function() {
        if ($scope.userInfo.name_status == 1) {
          $state.go('tab.user_profile_alipay')
        } else if ($scope.userInfo.name_status == '' || $scope.userInfo.name_status == null) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您还未进行实名认证,请先认证',
            duration: 1500
          })
          $state.go('tab.user_profile_card')
        } else if ($scope.userInfo.name_status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您的实名认证正在审核,请耐心等待,审核通过即可绑定',
            duration: 1500
          })
          return false
        } else if ($scope.userInfo.name_status == -1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您的实名认证未通过审核,请先进行修改 ',
            duration: 1500
          })
          $state.go('tab.user_profile_card')
        }
      }

      // 银行卡绑定
      $scope.profile_bankcard = function() {
        if ($scope.userInfo.name_status == 1) {
          $state.go('tab.user_profile_bankcard')
        } else if ($scope.userInfo.name_status == '' || $scope.userInfo.name_status == null) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您还未进行实名认证,请先认证',
            duration: 1500
          })
          $state.go('tab.user_profile_card')
        } else if ($scope.userInfo.name_status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您的实名认证正在审核,请耐心等待,审核通过即可绑定',
            duration: 1500
          })
          return false
        } else if ($scope.userInfo.name_status == -1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您的实名认证未通过审核,请先进行修改 ',
            duration: 1500
          })
          $state.go('tab.user_profile_card')
        }
      }

      //退出登录

      $scope.logout = function() {
        // Show the action sheet
        var hideSheet = $ionicActionSheet.show({
          destructiveText: '退出登录',
          titleText: '确定退出当前登录账号么？',
          cancelText: '取消',
          cancel: function() {
            // add cancel code..
          },
          destructiveButtonClicked: function() {
            logout()
            return true
          }
        })

        // For example's sake, hide the sheet after two seconds
        $timeout(function() {
          hideSheet()
        }, 3000)
      }
      //退出登录的方法
      var logout = function() {
        //console.log('logout');
        UserloginFactory.logout()
        $state.go('tab.user') //路由跳转
      }

      //页面加载完毕 广播事件
      $scope.$on('$ionicView.afterEnter', function() {
        $scope.showloading = false
      })
    }
  ])

  .controller('UserProfileHobby', [
    '$scope',
    '$state',
    '$ionicModal',
    function($scope, $state, $ionicModal) {
      $ionicModal
        .fromTemplateUrl('b.html', {
          scope: $scope,
          animation: 'slide-in-up'
        })
        .then(function(modal) {
          $scope.modal = modal
        })

      $scope.Hobby = function() {
        $scope.modal.show()
      }

      $scope.openModal = function() {
        $scope.modal.show()
      }
      $scope.closeModal = function() {
        $scope.modal.hide()
      }
      //当我们用到模型时，清除它！
      $scope.$on('$destroy', function() {
        $scope.modal.remove()
      })
      // 当隐藏的模型时执行动作
      $scope.$on('modal.hide', function() {
        // 执行动作
      })
      // 当移动模型时执行动作
      $scope.$on('modal.removed', function() {
        // 执行动作
      })
    }
  ])

  /**
   * @name [修改用户头像和昵称]
   */
  .controller('UserProfileNiknname', [
    '$rootScope',
    '$scope',
    '$ionicLoading',
    '$state',
    '$ionicModal',
    '$ionicActionSheet',
    'StorageFactory',
    'UserProfileFactory',
    'ENV',
    'Upload',
    'uploadFactory',
    function(
      $rootScope,
      $scope,
      $ionicLoading,
      $state,
      $ionicModal,
      $ionicActionSheet,
      StorageFactory,
      UserProfileFactory,
      ENV,
      Upload,
      uploadFactory
    ) {
      //如果不存在会员信息 则跳转登陆页面
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        //如果不存在会员信息 则跳转登陆页面
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          alert()
          $state.go('tab.user_login') //路由跳转
          StorageFactory.remove2()
          return false
        }
        // 获取用户id
        userid = StorageFactory.get('user').data.userid
        //获取用户签名
        random = StorageFactory.get('user').data.random
      })

      var imgUrl = ENV.imgUrl
      $scope.user_avatar = imgUrl + StorageFactory.get('profile').avatar

      $scope.user_nickname = {
        nickname: StorageFactory.get('profile').nickname
      }

      //接收通知
      $scope.$on('UserProfileFactory.set_nickname', function() {
        $scope.data_nickname = UserProfileFactory.get_nickname()
        $ionicLoading.show({
          noBackdrop: true,
          template: $scope.data_nickname.msg,
          duration: 1500
        })

        $scope.showloading = false
        $state.go('tab.user_profile')
      })

      $scope.imgurl = function($files) {
        return $('#File1').click()
      }

      $scope.onFileSelect = function($files) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在上传 请稍后...',
          duration: 1000
        })

        uploadFactory.set_upload($files, 2, userid, random)
      }

      //接收文件上传通知
      $scope.$on('uploadFactory.set_upload', function() {
        var data = uploadFactory.get_upload()

        if (data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '头像上传完成',
            duration: 1000
          })

          $scope.suser_avatar = ENV.imgUrl + data.url
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传失败',
            duration: 1000
          })
        }
      })

      $scope.userInfo_nickname = function() {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1500
        })
        UserProfileFactory.set_nickname(userid, random, $scope.user_nickname.nickname, $scope.user_avatar2)
      }
    }
  ])

  /**
   * @name [修改用户手机号]
   */

  .controller('UserProfilePhone', [
    '$scope',
    '$state',
    '$interval',
    '$ionicLoading',
    'StorageFactory',
    'User_registerFactory',
    'UserProfileFactory',
    function($scope, $state, $interval, $ionicLoading, StorageFactory, User_registerFactory, UserProfileFactory) {
      var storageKey = 'user'
      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid

      //获取用户签名
      var random = StorageFactory.get(storageKey).data.random

      // 获取用户手机认证状态
      $scope.user_profile = StorageFactory.get('profile') // 0待认证 1已认证

      $scope.text = '获取验证码'
      $scope.isDisabled = false
      $scope.UserPersonal_getsms = function(phone) {
        if (phone == $scope.user_profile.phone && $scope.user_profile.phone_status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,您修改的手机号不能和旧手机一致',
            duration: 1000
          })

          return false
        }

        $scope.isDisabled = true
        //console.log(phone);
        //请求服务器验证手机号
        // var enum ="phone";
        User_registerFactory.getsms(phone)

        //收到结果通知
        $scope.$on('NewsContent.getsms', function() {
          var r = User_registerFactory.setsms()
          if (r.status == 0) {
            $scope.isDisabled = false
            $scope.user_phone_state = true

            $ionicLoading.show({
              noBackdrop: true,
              template: r.msg,
              duration: 1000
            })
          } else if (r.status == 1) {
            User_registerFactory.send_sms(phone)

            $scope.$on('NewsContent.sendsms', function() {
              var setsms = User_registerFactory.get_send_sms()

              if (setsms.status == 0) {
                // 返回失败原因
                $ionicLoading.show({
                  noBackdrop: true,
                  template: setsms.msg,
                  duration: 1500
                })
                $scope.isDisabled = true
                return
              } else if (setsms.status == 1) {
                $ionicLoading.show({
                  noBackdrop: true,
                  template: '验证码已发送到您手机！' + phone,
                  duration: 1000
                })

                var iNow = 60
                $scope.text = '请等待' + iNow + '秒'
                $scope.isDisabled = true

                var timer = $interval(function() {
                  iNow--
                  $scope.text = '请等待' + iNow + '秒'

                  if (iNow == 0) {
                    $interval.cancel(timer)
                    $scope.text = '再次获取'
                    $scope.isDisabled = false
                  }
                }, 1000)
              }
            })
          } else {
            $ionicLoading.show({
              noBackdrop: true,
              template: '发送失败，请重新获取！',
              duration: 1000
            })

            $scope.isDisabled = false
          }
        })
      }

      //提交后台修改用户手机号

      $scope.edit_phone = function(phone, sms) {
        //console.log(sms);

        if (phone == $scope.user_profile.phone && $scope.user_profile.phone_status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,您修改的手机号不能和旧手机一致',
            duration: 1000
          })

          return false
        }

        // 后端请求修改手机号
        UserProfileFactory.set_edit_phone(phone, sms, userid, random)

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })

        $state.go('tab.user_profile')
      }

      //接收修改手机号成功通知
      $scope.$on('UserProfileFactory.set_edit_phone', function() {
        var userRel = UserProfileFactory.get_edit_phone()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          //修改成功

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })
    }
  ])

  /**
   * @name [修改用户QQ]
   */

  .controller('UserProfileQQ', [
    '$scope',
    '$state',
    '$interval',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    function($scope, $state, $interval, $ionicLoading, StorageFactory, UserProfileFactory) {
      var storageKey = 'user'
      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid

      //获取用户签名
      var random = StorageFactory.get(storageKey).data.random

      $scope.edit_qq = function(qq) {
        if (qq == StorageFactory.get('profile').qq) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，要修改的qq不能和老qq一样,不修改请直接返回',
            duration: 1500
          })

          return false
        }
        // 后端请求修改

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })

        //提交后台修改用户QQ
        UserProfileFactory.set_username_qq(qq, userid, random)
      }

      //接收通知
      $scope.$on('UserProfileFactory.set_username_qq', function() {
        userRel = UserProfileFactory.get_username_qq()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          return false
        } else if (userRel.status == 1) {
          //修改成功

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
          //跳转个人设置页面
          $state.go('tab.user_profile') //路由跳转
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })

          return false
        }
      })
    }
  ])

  /**
   * @name [修改用户邮箱]
   */

  .controller('UserProfileEmail', [
    '$scope',
    '$state',
    '$interval',
    '$ionicLoading',
    'UserForgetFactory',
    'StorageFactory',
    'UserProfileFactory',
    function($scope, $state, $interval, $ionicLoading, UserForgetFactory, StorageFactory, UserProfileFactory) {
      var storageKey = 'user'
      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid

      // 获取用户邮箱认证状态
      $scope.user_email_state = StorageFactory.get('profile').email_status // -1不存在 0待认证 1已认证

      //获取用户旧邮箱
      $scope.user_old_email = StorageFactory.get('profile').email

      $scope.user_email = $scope.user_email_state == 1 ? '' : $scope.user_old_email

      //获取用户签名
      var random = StorageFactory.get(storageKey).data.random

      //第一步发送邮件验证码
      $scope.get_emailsms = function(email) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/

        if (filter.test(email)) {
          username_type = '邮箱:' + email
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '您输入的不是邮箱',
            duration: 1000
          })
          return false
        }

        //判断新旧邮箱是否一致
        if (email == $scope.user_old_email && $scope.user_email_state == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲 新旧邮箱不能一致！',
            duration: 1000
          })
          return false
        }

        var title = '邮箱认证码'
        UserProfileFactory.set_send_email_code(email, title, userid, random)

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })
        //console.log(email, userid, random);
      }

      //接收邮箱验证码成功通知。

      //接收通知
      $scope.$on('UserProfileFactory.set_send_email_code', function() {
        userRel = UserProfileFactory.get_send_email_code()

        if (userRel == undefined) return false

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          //成功提示原因

          $ionicLoading.show({
            noBackdrop: true,
            template: '验证码已成功发送到' + username_type,
            duration: 2000
          })
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })

      //后台请求验证修改邮箱

      $scope.edit_email = function(email, sms) {
        //判断新旧邮箱是否一致
        if (email == $scope.user_old_email && $scope.user_email_state == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲 新旧邮箱不能一致！',
            duration: 1000
          })
          return false
        }
        //发起请求
        UserProfileFactory.set_username_email(email, sms, userid, random)

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })
      }

      //接收后台修改邮箱返回结果

      $scope.$on('UserProfileFactory.set_username_email', function() {
        userRel = UserProfileFactory.get_username_email()

        if (userRel == undefined) return false

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          //成功提示原因

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          //成功返回 个人设置
          $state.go('tab.user_profile') //路由跳转
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })
    }
  ])

  /**
   * @name [修改用户收货地址]
   */

  .controller('UserProfileAddress', [
    '$scope',
    '$state',
    '$interval',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    function($scope, $state, $interval, $ionicLoading, StorageFactory, UserProfileFactory) {
      var storageKey = 'user'
      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      //如果用户已经设置了收货地址 则获取用户收货地址的 当前菜单name id 上级id

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid
      //获取用户签名
      var random = StorageFactory.get(storageKey).data.random

      // 获取用户当前收货地址
      UserProfileFactory.set_username_Profile_address(userid, random)

      // 接收返回信息 收货地址
      //接收通知
      $scope.$on('UserProfileFactory.set_username_Profile_address', function() {
        var data_address = UserProfileFactory.get_username_Profile_address()
        $scope.data_address = data_address.data
        var address = data_address.data
        //console.log(data_address);
        $scope.d1 = address.provice_name ? address.provice_name : '请选择省' //用户收货地址 第一级省
        $scope.d2 = address.city_name ? address.city_name : '请选择市' //用户收货地址 第二级市
        $scope.d3 = address.area_name ? address.area_name : '请选择县/区' //用户收货地址 第三级县

        var provice_id = address.provice //已选省id
        var city_id = address.area //已选市id

        province(provice_id)

        if (city_id) city(city_id)

        //定义第三层样式 笨方法

        if (address.area == '') {
          $scope.b3 = false
        } else {
          $scope.b3 = true
        }
      })

      //获取后台联动 第一级 菜单数据
      $scope.CityData = UserProfileFactory.set_username_address(0)

      //console.log($scope.CityData);

      //获取后台联动菜单 第二级菜单数据
      function province(provice_id) {
        $scope.province = UserProfileFactory.set_username_address(provice_id)
      }

      // 获取后台联动菜单第三级菜单数据
      function city(city_id) {
        $scope.city = UserProfileFactory.set_username_address(city_id)
      }

      var vm = ($scope.vm = {})

      //监听用户选择省市
      $scope.$watch('vm.country', function(province) {
        vm.city = null
        if (!province) return false
        var data = province
        //console.log(data);
        // 后台请求下级
        $scope.province = UserProfileFactory.set_username_address(data.linkageid)

        $scope.d2 = '请选择市'
        $scope.d3 = '请选择县/区'
        $scope.b3 = false

        //获取用户当前选择的省id
        $scope.country_linkageid = data.linkageid
        //console.log($scope.country_linkageid);
      })

      //监听用户选择市区
      $scope.$watch('vm.province', function(province) {
        $scope.city_linkageid = ''
        if (!province) return false
        var city_r = UserProfileFactory.set_username_address(province.linkageid)
        $scope.city = UserProfileFactory.set_username_address(province.linkageid)
        $scope.d3 = '请选择县/区'
        //获取用户当前选择的市id
        $scope.province_linkageid = province.linkageid
        //console.log($scope.province_linkageid);
      })

      //监听用户选择第三级 县
      $scope.$watch('vm.city', function(province) {
        if (!province) return false
        //获取用户当前选择的县id
        $scope.city_linkageid = province.linkageid
        //console.log($scope.city_linkageid);
      })

      //提交后台更新或者修改收货地址

      $scope.edit_address = function(r1, r2, r3, r4, r5, r6) {
        var address = UserProfileFactory.get_username_Profile_address()
        // 获取用户之前的收货地址

        r1 = r1 ? r1 : address.data.provice
        r2 = r2 ? r2 : address.data.city
        r3 = r3 ? r3 : ''

        // 后端请求修改
        UserProfileFactory.set_username_Profile_edit_address(r1, r2, r3, r4, r5, r6, userid, random)

        //console.log(r1, r2, r3);
        //console.log(r1, r2, r3, r4, r5, r6);
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 2000
        })
      }

      //接收修改成功通知
      $scope.$on('UserProfileFactory.set_username_Profile_edit_address', function() {
        var userRel = UserProfileFactory.get_username_Profile_edit_address()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          return false
        } else if (userRel.status == 1) {
          //修改成功

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          //返回我的信息页面
          $state.go('tab.user_profile')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您没有修改您的地址信息,不修改请直接返回！',
            duration: 1500
          })
        }
      })
    }
  ])

  /**
   * @name [修改用户密码]
   */

  .controller('UserProfilePassword', [
    '$scope',
    '$state',
    '$interval',
    '$ionicLoading',
    'UserloginFactory',
    'StorageFactory',
    'UserProfileFactory',
    function($scope, $state, $interval, $ionicLoading, UserloginFactory, StorageFactory, UserProfileFactory) {
      var storageKey = 'user'
      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid

      //获取用户签名

      var random = StorageFactory.get(storageKey).data.random

      //提交后台修改用户密码

      $scope.edit_password = function(oldpass, password) {
        if (oldpass == password) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，旧密码和新密码不能相同',
            duration: 1000
          })
          return false
        }

        // 后端请求修改
        UserProfileFactory.set_update_pwd(oldpass, password, userid, random)

        //console.log(oldpass, password, userid, random);

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })
      }

      //接收通知
      $scope.$on('UserProfilePassword.set_update_pwd', function() {
        var userRel = UserProfileFactory.get_update_pwd()
        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          //修改成功

          $ionicLoading.show({
            noBackdrop: true,
            template: '修改密码成功,快来登录验证一下',
            duration: 2000
          })

          //让用户退出登录
          UserloginFactory.logout()

          //跳转到登录页面  验证密码
          $state.go('tab.user_login')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })
    }
  ])

  /**
   * @name [绑定淘宝账号]
   */

  .controller('UserProfiletaobao', [
    '$scope',
    '$rootScope',
    '$ionicActionSheet',
    '$timeout',
    '$state',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    function(
      $scope,
      $rootScope,
      $ionicActionSheet,
      $timeout,
      $state,
      $ionicLoading,
      StorageFactory,
      UserProfileFactory
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        //如果不存在会员信息 则跳转登陆页面
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $state.go('tab.user_login') //路由跳转
          return false
        }

        $rootScope.hideTabs = 'tabs-item-hide'

        // 获取用户id
        userid = StorageFactory.get('user').data.userid

        //获取用户签名

        random = StorageFactory.get('user').data.random

        //获取后台绑定淘宝帐号数量设置

        //获取当前用户已绑定淘宝帐号数量
        UserProfileFactory.set_username_taobao(userid, random)
      })

      //接收通知 当前用户已绑定淘宝帐号
      $scope.$on('UserProfileFactory.set_username_taobao', function() {
        $scope.data_bind_taobao = UserProfileFactory.get_username_taobao()
        //console.log($scope.data_bind_taobao);
      })

      //用户删除 设为默认淘宝帐号

      $scope.delete_taobao = function(taobao) {
        var taobao_id = taobao //全局变量

        // Show the action sheet
        var hideSheet = $ionicActionSheet.show({
          buttons: [
            {
              text: '<i class="icon ion-share"></i> 设为默认'
            },
            {
              text: '<i class="icon ion-arrow-move"></i> 解除绑定'
            },
            {
              text: '<i class="icon ion-arrow-move"></i>取消操作 '
            }
          ],
          titleText: '您想要进行的操作?',
          cancelText: '取消',
          cancel: function() {
            // add cancel code..
          },
          destructiveButtonClicked: function() {
            return true
          },
          buttonClicked: function(index) {
            if (index == 0) {
              //执行 后台设为默认
              UserProfileFactory.set_bind_taobao_setdefault(taobao_id, userid, random)

              $ionicLoading.show({
                noBackdrop: true,
                template: '正在提交,请稍等...'
              })

              return true
            } else if (index == 1) {
              //执行后台解绑删除操作
              UserProfileFactory.set_bind_del_tb(taobao_id, userid, random)

              $ionicLoading.show({
                noBackdrop: true,
                template: '正在提交...',
                duration: 1000
              })

              return true
            } else if (index == 2) {
              return true
            }
          }
        })
      }

      //提交后台绑定新淘宝账号

      $scope.bind_taobao = function(oaccount) {
        //console.log(oaccount);

        $ionicLoading.show({
          noBackdrop: true,
          template: '查询中,请稍等...'
        })

        // 后端请求绑定淘宝账号
        UserProfileFactory.set_bind_taobao(oaccount, userid, random)
      }

      //接收通知 绑定帐号结果通知
      $scope.$on('UserProfileFactory.set_bind_taobao', function() {
        var userRel = UserProfileFactory.get_bind_taobao()
        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else if (userRel.status == 1) {
          //请求服务器获得最新已绑定帐号
          UserProfileFactory.set_username_taobao2(userid, random)

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
        }
      })

      //接收设为默认结果通知
      $scope.$on('UserProfileFactory.set_bind_taobao_setdefault', function() {
        var data_bind_taobao_setdefault = UserProfileFactory.get_bind_taobao_setdefault()
        UserProfileFactory.set_username_taobao(userid) //重新获取淘宝帐号

        $ionicLoading.show({
          noBackdrop: true,
          template: data_bind_taobao_setdefault.msg,
          duration: 1000
        })

        return true
      })

      //接收解除绑定结果通知
      $scope.$on('UserProfileFactory.set_bind_del_tb', function() {
        var data_bind_del_tb = UserProfileFactory.get_bind_del_tb()
        if (data_bind_del_tb.status == 1) {
          UserProfileFactory.set_username_taobao2(userid, random) //重新获取淘宝帐号
        }

        $ionicLoading.show({
          noBackdrop: true,
          template: data_bind_del_tb.msg,
          duration: 1000
        })
      })
    }
  ])

  /*
   name 实名认证
   */

  .controller('UserProfileCard', [
    '$rootScope',
    '$scope',
    '$state',
    '$interval',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $state,
      $interval,
      $ionicLoading,
      StorageFactory,
      UserProfileFactory,
      ENV,
      uploadFactory
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        //如果不存在会员信息 则跳转登陆页面
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $state.go('tab.user_login') //路由跳转
          return false
        }
        // 获取用户id
        userid = StorageFactory.get('user').data.userid
        //获取用户签名
        random = StorageFactory.get('user').data.random

        // 获取用户实名认证状态
        $scope.name_status = StorageFactory.get('profile').name_status
      })

      if ($scope.name_status == 1) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '您已经通过实名认证，无需在认证！',
          duration: 2000
        })

        $state.go('tab.user_profile')
        return false
      } else if ($scope.name_status == 0) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '您的实名认证正在审核中，请耐心等待！',
          duration: 2000
        })

        $state.go('tab.user_profile')
        return false
      }

      //身份证正面图片
      $scope.face_img = 'img/face_img.jpg'

      //身份证反面
      $scope.back_img = 'img/back_img.jpg'

      //手持身份证
      $scope.person_img = 'img/person_img.jpg'

      //接收文件上传通知
      $scope.$on('uploadFactory.set_upload', function() {
        $ionicLoading.show({
          noBackdrop: true,
          template: '图片已成功上传',
          duration: 2000
        })

        //服务器远程图片地址
        $scope.response = uploadFactory.get_upload()
        var imgUrl = ENV.imgUrl
        for (x in $scope.response) {
          if (x == 1) $scope.face_img = imgUrl + $scope.response[x]
          if (x == 2) $scope.back_img = imgUrl + $scope.response[x]
          if (x == 3) $scope.person_img = imgUrl + $scope.response[x]
        }
      })

      //后台请求修改身份证信息

      $scope.edit_identity = function(
        user_Full_name,
        user_ID_number,
        face_img,
        back_img,
        person_img,
        sex,
        year,
        month,
        day,
        age
      ) {
        if ($scope.face_img == 'img/face_img.jpg') {
          /*         $ionicLoading.show({
           noBackdrop: true,
           template: '请上传身份证正面',
           duration: 2000
         });
         return false;*/
          $scope.face_img == ''
        }

        if ($scope.fback_img == 'img/back_img.jpg') {
          /*         $ionicLoading.show({
           noBackdrop: true,
           template: '请上传身份证反面',
           duration: 2000
         });
         return false;*/
          $scope.fback_img == ''
        }

        if ($scope.person_img == 'img/person_img.jpg') {
          /*         $ionicLoading.show({
           noBackdrop: true,
           template: '请上传手持身份证图片',
           duration: 2000
         });
         return false;*/

          $scope.person_img == ''
        }

        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交',
          duration: 2000
        })

        //后台请求修改信息

        UserProfileFactory.set_username_Profile_edit_identity(
          user_Full_name,
          user_ID_number,
          $scope.face_img,
          $scope.fback_img,
          $scope.person_img,
          sex,
          year,
          month,
          day,
          age,
          userid,
          random
        )
      }

      //接收通知
      $scope.$on('UserProfileFactory.set_username_Profile_edit_identity', function() {
        var userRel = UserProfileFactory.get_username_Profile_edit_identity()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
          return false
        } else if (userRel.status == 1) {
          //修改成功

          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          //返回我的设置
          $state.go('tab.user_profile')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
          return false
        }
      })

      $scope.user_ID_number = ''
    }
  ])

  /*
  name 支付宝绑定
   */

  .controller('UserProfileAllpay', [
    '$scope',
    '$state',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    function($scope, $state, $ionicLoading, StorageFactory, UserProfileFactory) {
      var storageKey = 'user'
      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      // 获取用户已实名认证真实姓名
      $scope.user_Real_name = StorageFactory.get('profile').name

      //获取用户实名认证状态
      $scope.name_authentication_status = StorageFactory.get('profile').name_status

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid

      //获取用户签名
      var random = StorageFactory.get(storageKey).data.random

      //获取当前支付宝绑定状态

      if (StorageFactory.get('profile').alipay_status == 1) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '您已绑定支付宝，如需修改请联系平台客服！',
          duration: 2000
        })
        return false
        $state.go('tab.user_profile')
      }

      //用户实名认证状态不为1的时候 不能进入这个页面
      if ($scope.name_authentication_status != 1) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '您还未通过实名认证',
          duration: 1000
        })
        //跳转实名认证
        $state.go('tab.user_profile')
        return false
      }

      //提交后台修改
      $scope.edilt_allpay = function(allpay) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交..',
          duration: 1000
        })

        //发起后端请求
        UserProfileFactory.set_bind_alipay_info(allpay, userid, random)
      }

      //接收绑定成功结果通知
      $scope.$on('UserProfileFactory.set_bind_alipay_info', function() {
        var userRel = UserProfileFactory.get_bind_alipay_info()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
          return false
        } else if (userRel.status == 1) {
          //修改成功
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          //跳转个人设置页面
          $state.go('tab.user_profile')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
          return false
        }
      })
    }
  ])

  /*
  name 银行卡绑定
   */

  .controller('UserProfileBankCard', [
    '$scope',
    '$state',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    function($scope, $state, $ionicLoading, StorageFactory, UserProfileFactory) {
      var storageKey = 'user'

      //如果不存在会员信息 则跳转登陆页面
      if (
        !StorageFactory.get(storageKey) ||
        (StorageFactory.get(storageKey) && StorageFactory.get(storageKey).status != 1)
      ) {
        $state.go('tab.user_login') //路由跳转
        return false
      }

      //获取当前用户银行卡绑定状态

      if (StorageFactory.get('profile').bank_status == 1) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '您已绑定银行卡，如需修改请联系平台客服！',
          duration: 2000
        })
        return false
        $state.go('tab.user_profile')
      }

      // 获取用户id
      var userid = StorageFactory.get(storageKey).data.userid

      // 获取用户已实名认证真实姓名
      $scope.user_Real_name = StorageFactory.get('profile').name

      //获取用户实名认证状态
      $scope.name_authentication_status = StorageFactory.get('profile').name_status

      //获取平台支持的银行

      $scope.bank_info = UserProfileFactory.get_bank_info()

      //console.log($scope.bank_infoo);

      // 获取开户行所在省市县

      //获取后台联动 第一级 菜单数据
      $scope.CityData = UserProfileFactory.set_username_address(0)

      //console.log($scope.CityData);

      //获取后台联动菜单 第二级菜单数据
      function province(provice_id) {
        $scope.province = UserProfileFactory.set_username_address(provice_id)
      }

      // 获取后台联动菜单第三级菜单数据
      function city(city_id) {
        $scope.city = UserProfileFactory.set_username_address(city_id)
      }

      var vm = ($scope.vm = {})

      //监听用户选择银行
      $scope.$watch('vm.bank_name', function(province) {
        if (!province) return false
        $scope.d2 = '请选择市'
        $scope.d3 = '请选择县/区'
        $scope.b3 = false

        //console.log(province);
      })

      //监听用户选择省市
      $scope.$watch('vm.country', function(province) {
        vm.city = null
        if (!province) return false
        var data = province
        //console.log(data);
        // 后台请求下级
        $scope.province = UserProfileFactory.set_username_address(data.linkageid)

        $scope.d2 = '请选择市'
        $scope.d3 = '请选择县/区'
        $scope.b3 = false

        //获取用户当前选择的省id
        $scope.country_linkageid = data.linkageid
        //console.log($scope.country_linkageid);
      })

      //监听用户选择市区
      $scope.$watch('vm.province', function(province) {
        vm.city = null
        $scope.city_linkageid = ''
        if (!province) return false

        //选择市清空选择的县

        var city_r = UserProfileFactory.set_username_address(province.linkageid)
        $scope.city = UserProfileFactory.set_username_address(province.linkageid)
        $scope.d3 = '请选择县/区'
        //获取用户当前选择的市id
        $scope.province_linkageid = province.linkageid
        //console.log($scope.province_linkageid);
      })

      //监听用户选择第三级 县
      $scope.$watch('vm.city', function(province) {
        if (!province) return false
        //获取用户当前选择的县id
        $scope.city_linkageid = province.linkageid
        //console.log($scope.city_linkageid);
      })

      //获取用户签名
      var random = StorageFactory.get(storageKey).data.random

      //提交后台修改银行卡

      $scope.edilt_bank = function(user_bank, bank_id, sub_branch) {
        user_bank = user_bank.replace(/\s/g, '')

        //console.log(user_bank);

        if (!$scope.country_linkageid || !$scope.province_linkageid || !bank_id) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请完善开户银行,所在省,市',
            duration: 1000
          })
          return false
        }
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在提交...',
          duration: 1000
        })

        //console.log(user_bank, bank_id, sub_branch, $scope.country_linkageid, $scope.province_linkageid, $scope.city_linkageid);

        //第二步请求后台修改银行卡
        UserProfileFactory.set_bind_bank_info(
          user_bank,
          bank_id,
          sub_branch,
          $scope.country_linkageid,
          $scope.province_linkageid,
          $scope.city_linkageid,
          userid,
          random
        )
      }

      //接收绑定银行卡结果通知

      $scope.$on('UserProfileFactory.set_bind_bank_info', function() {
        var userRel = UserProfileFactory.get_bind_bank_info()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
          return false
        } else if (userRel.status == 1) {
          //修改成功
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          //跳转个人设置页面
          $state.go('tab.user_profile')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
          return false
        }
      })
    }
  ])

  /*
  name 申请提现

   */

  .controller('UserDeposite', [
    '$rootScope',
    '$scope',
    '$state',
    '$ionicModal',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    'UserPersonalFactory',
    'UserDepositeFactory',
    function(
      $rootScope,
      $scope,
      $state,
      $ionicModal,
      $ionicLoading,
      StorageFactory,
      UserProfileFactory,
      UserPersonalFactory,
      UserDepositeFactory
    ) {
      $scope.showloading = true

      //页面加载之前的事件
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        //如果不存在会员信息 则跳转登陆页面
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $state.go('tab.user_login') //路由跳转
          return false
        }

        //获取后台提现配置
        UserDepositeFactory.set_cash_config_info()

        // 获取用户id
        userid = StorageFactory.get('user').data.userid

        //获取用户签名
        random = StorageFactory.get('user').data.random

        // 获取账户余额
        $scope.money = StorageFactory.get('profile').money

        // 获取用户已实名认证真实姓名
        $scope.user_Real_name = StorageFactory.get('profile').name

        //获取用户实名认证状态
        $scope.name_authentication_status = StorageFactory.get('profile').name_status

        //获取用户支付宝绑定状态

        var profile = StorageFactory.get('profile')

        console.log(profile)

        $scope.alipay_status = profile.alipay_status

        //获取用户银行卡绑定状态
        $scope.bank_status = profile.bank_status

        //获取用户绑定的支付宝 或者 银行卡
        UserDepositeFactory.set_useraccount(userid, random)
      })

      // 定义普通提现和快速提现 0为普通 1为快速
      $scope.Present_method = 0

      // 获取当前用户会员组 1为普通 2为vip

      $scope.Member_group = StorageFactory.get('profile').groupid

      // 默认选择支付宝
      $scope.a_status = 2

      //默认选择普通提现
      $scope.paypal = 1

      // 默认关闭上拉更新 解决ionic不间断加载问题
      $scope.hasNextPage = false

      // 如果用户未绑定支付宝 或者银行卡的时候 跳转至个人设置页面
      //  用户绑定了支付宝可以申请提现  绑定了银行卡也可以进入提现 2者当中必须满足一个条件

      if (StorageFactory.get('profile').bank_status == 1 || StorageFactory.get('profile').alipay_status == 1) {
      } else {
        $ionicLoading.show({
          noBackdrop: true,
          template: '亲,您还未绑定支付宝或者银行卡,先绑定才能进行提现',
          duration: 2000
        })
        $state.go('tab.user_profile')
        return false
      }

      //接收通知 获得用户绑定的支付宝 或者银行卡信息
      $scope.$on('UserDepositeFactory.set_useraccount', function() {
        $scope.Bank_account = UserDepositeFactory.get_useraccount()

        for (var i = $scope.Bank_account.lists.length - 1; i >= 0; i--) {
          if ($scope.Bank_account.lists[i]['type'] == 'alipay') {
            /*用户绑定的支付宝*/
            $scope.alipay = $scope.Bank_account.lists[i]['account']
          }

          if ($scope.Bank_account.lists[i]['type'] == 'bank') {
            /*用户绑定的银行卡*/
            $scope.bank = $scope.Bank_account.lists[i]['account']
          }
        }
      })

      $scope.$on('UserDepositeFactory.set_cash_config_info', function() {
        $scope.showloading = false
        $scope.bank_configure = UserDepositeFactory.get_cash_config_info()

        if (!$scope.bank_configure.cash_type) return false

        var type1 = $scope.bank_configure.cash_type[0]
        var type2 = $scope.bank_configure.cash_type[1]

        if (!type1 && !type2) {
          // 平台暂时未开放提现
          $scope.deposite_status = '平台维护中,暂时关闭提现'
          $scope.d_status = 0
        } else if (type1 == 'bank') {
          //开放银行卡提现
          $scope.deposite_status = '平台支持提现到银行卡'
          $scope.d_status = 1 // 支持银行卡
          $scope.a_status = 1
        } else if (type1 && !type2) {
          //开启支付宝提现
          $scope.deposite_status = '平台支持提现到支付宝'
          $scope.d_status = 2
          $scope.c_status = 1 //开启支付宝
        } else if (type1 && type2) {
          //都已经开启了
          $scope.deposite_status = '平台支持提现到支付宝和银行卡'
          $scope.b_status = 3
          $scope.d_status = 3 // 支持银行卡 和支付宝
          $scope.c_status = 1 //开启支付宝
          if ($scope.allpay_status != 1 && $scope.bank_status == 1) $scope.a_status = 1
        }

        //根据不同会员组计算提现手续费

        if ($scope.Member_group == 0) {
          $scope.service_fee =
            $scope.bank_configure.service_fee == 0
              ? '活动中,免手续费'
              : '每笔收取' + $scope.bank_configure.service_fee + '% 手续费'

          $scope.service_Counter_Fee = $scope.bank_configure.service_fee

          // //console.log($scope.service.Counter_Fee);
        }
        if ($scope.Member_group == 1) {
          $scope.service_fee =
            $scope.bank_configure.vip_service_fee == 0
              ? 'vip尊享,免手续费 '
              : 'vip尊享' + $scope.bank_configure.vip_service_fee + '% 手续费'

          $scope.service_Counter_Fee = $scope.bank_configure.vip_service_fee
        }
      })

      //提交后台申请提现

      $scope.person_cash = function(t_money, a_status, money) {
        t_money = t_money - 1 + 1 //会员提现金额

        money = money - 1 + 1

        if (t_money > $scope.money) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，提现金额不能大于账户余额!',
            duration: 2000
          })
          return false
        }

        if (money < $scope.bank_configure.min_money) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，提现金额不能低于' + $scope.bank_configure.min_money + '元',
            duration: 2000
          })
          return false
        }

        //发起后端提现请求
        UserDepositeFactory.set_person_cash(t_money, a_status, $scope.paypal, userid, random)
      }

      //接收申请提现结果通知
      $scope.$on('UserDepositeFactory.set_person_cash', function() {
        var userRel = UserDepositeFactory.get_person_cash()

        //console.log(userRel);

        if (userRel.status == 0) {
          //修改失败
          //    返回失败原因
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })
          return false
        } else if (userRel.status == 1) {
          //申请成功
          $ionicLoading.show({
            noBackdrop: true,
            template: userRel.msg,
            duration: 2000
          })

          //提现申请成功 重新请求获得用户的详细信息
          UserPersonalFactory.set_userinfo(userid, random)
          //跳转提现记录页面
          $state.go('tab.user_deposite_record')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，程序猿哥哥正在抢修！',
            duration: 1500
          })
          return false
        }
      })

      $scope.changeTab = function(index, paypal) {
        $scope.paypal = paypal
        $scope.Present_method = index
        //设置高亮
      }

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })

      // 选择提现方式
      $scope.Present_method1 = function() {
        //  隐藏支付宝  //显示银行卡
        $scope.a_status = 1

        // $scope.modal.show();
      }

      $scope.Present_method2 = function() {
        //  隐藏银行卡  //显示支付宝
        $scope.a_status = 2

        // $scope.modal.show();
      }

      $scope.deposite_record = function() {
        // 跳转提现记录页面
        $state.go('tab.user_deposite_record')

        //获取用户提现记录
        UserDepositeFactory.set_getusercashlog(userid, random)
      }

      //提现记录下拉更新

      $scope.txjl_doRefresh = function() {
        UserDepositeFactory.set_getusercashlog(userid, random)
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //提现记录 上拉加载更多
      // 尝试页面加载完成 之后在执行 看能否有效

      $scope.loadMore = function() {
        $scope.hasNextPage = false

        //发送后端请求
        UserDepositeFactory.txjl_loadMore(userid, random)
        $scope.$broadcast('scroll.infiniteScrollComplete')
      }

      $scope.tixian_log_showloading = true
      // UserDepositeFactory.set_getusercashlog(userid, random);

      //收到提现记录通知请求
      $scope.$on('UserDepositeFactory.set_getusercashlog', function() {
        $scope.getusercashlog_data = UserDepositeFactory.get_getusercashlog()

        $scope.hasNextPage = UserDepositeFactory.hasNextPage()

        $scope.tixian_log_showloading = false
        //console.log($scope.getusercashlog_data);
      })
    }
  ])

  /*
   name 账户明细
   */

  .controller('UserLog', [
    '$rootScope',
    '$scope',
    '$state',
    '$ionicLoading',
    'StorageFactory',
    'UserLogFactory',
    function($rootScope, $scope, $state, $ionicLoading, StorageFactory, UserLogFactory) {
      //如果不存在会员信息 则跳转登陆页面
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        $state.go('tab.user_login') //路由跳转登录
        return false
      }

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      //后端请求用户的账户明细  1.用户金钱 2.积分

      UserLogFactory.set_user_log(1, userid, random)

      // 获取账户余额
      $scope.money = StorageFactory.get('profile').money

      $scope.zhmx_hasNextPage = false

      $scope.zhmx_showloading = true

      //接收用户账户明细通知
      $scope.$on('UserLogFactory.set_user_log', function() {
        $scope.log_data = UserLogFactory.get_user_log()

        $scope.zhmx_hasNextPage = UserLogFactory.hasNextPage()

        $scope.zhmx_showloading = false

        $scope.$broadcast('scroll.infiniteScrollComplete')
      })

      //下拉更新
      $scope.zhmx_doRefresh = function() {
        UserLogFactory.set_user_log(userid, random)
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载更多

      $scope.loadMore = function() {
        UserLogFactory.zhmx_loadMore(userid, random)
      }

      //点击高亮 更换请求
      $scope.zhmx_changeTab = function(index, type) {
        //发起后端请求
        $scope.zhmx_showloading = true

        UserLogFactory.set_user_log(type, userid, random)
      }

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })
    }
  ])

  /*
    name  我的试用订单
   */

  .controller('trial_order', [
    '$timeout',
    '$scope',
    '$state',
    '$ionicPopup',
    '$ionicModal',
    '$ionicLoading',
    'ENV',
    'StorageFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'configFactory',
    function(
      $timeout,
      $scope,
      $state,
      $ionicPopup,
      $ionicModal,
      $ionicLoading,
      ENV,
      StorageFactory,
      UserProfileFactory,
      trialOrderFactory,
      configFactory
    ) {
      //页面加载之前事件

      $scope.$on('$ionicView.beforeEnter', function() {
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.userInfo = ''
          $scope.logo_status = 0
          $state.go('tab.user_login')
          // console.log(window.location.href.split("#")[0])
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid

          //获取用户签名
          random = StorageFactory.get('user').data.random

          // $scope.logo_status = 1

          // 第一步 后台获取我的免费试用订单
          trialOrderFactory.set_getorderlists(userid, 'trial', type, random)
        }
      })

      $scope.ENV = ENV

      //默认显示已审核

      var type = 1

      //默认关闭上拉加载更多
      $scope.wdsy_hasNextPage = false

      //后台请求获取后台的活动配置
      configFactory.set_trial_config()

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })

      //接收配置请求通知
      $scope.$on('configFactory.set_trial_config', function() {
        //alert("配置通知来了");

        var trial_config = configFactory.get_trial_config() //网站试用配置

        //试用资格审核时间限制

        $scope.seller_check_time = trial_config.data.seller_check_time * 86400

        //已通过试用，待填写订单号时间限制
        $scope.buyer_write_order_time = trial_config.data.buyer_write_order_time * 3600

        //填写试用报告时间限制
        $scope.buyer_write_talk_time = trial_config.data.buyer_write_talk_time * 86400

        //试用报告审核时间限制
        $scope.seller_trialtalk_check = trial_config.data.seller_trialtalk_check.value * 86400

        //待审核订单号时间限制
        $scope.seller_order_check_time = trial_config.data.seller_order_check_time * 3600

        //修改订单号时间限制

        $scope.buyer_check_update_order_sn = trial_config.data.buyer_check_update_order_sn * 3600
      })

      //接收订单返回通知
      $scope.$on('trialOrderFactory.set_getorderlists', function() {
        $scope.wdsy_showloading = false
        $scope.trial_getorderlists = trialOrderFactory.get_getorderlists()
        $scope.wdsy_hasNextPage = trialOrderFactory.get_wdsy_hasNextPage()
        $scope.$broadcast('scroll.infiniteScrollComplete') //广播通知！
      })

      //点击订单跳转填写订单号页面

      $scope.add_order_number = function(order_id, goods_id, order_status, trial_report) {
        //如果订单状态是待审核资格，则跳转商品页面
        if (order_status == 1) {
          $state.go('tab.show_trial', {
            id: goods_id
          })

          return false

          //如果订单状态是已通过待填写订单号 则跳转订单号填写页面
        } else if (order_status == 2 || (order_status == 4 && !trial_report)) {
          $state.go('tab.trial_order_id', {
            id: order_id,
            goodid: goods_id
          })

          //如果订单状态是填写试用报告 则跳转试用报告填写页面
        } else if (order_status == 8 || (order_status == 4 && trial_report)) {
          //填写试用报告

          $state.go('tab.trial_order_report', {
            id: order_id,
            goodid: goods_id
          })
        } else {
          $state.go('tab.show_trial', {
            id: goods_id
          })
        }

        //订单状态是已通过(不含试用报告) 跳转订单号填写页面
      }

      // 第二步 针对不同的订单状态 写各种方法

      //设置高亮，获取不同状态的订单
      $scope.trial_order_changeTab = function(index, type_id) {
        type = type_id

        $scope.wdsy_showloading = true

        trialOrderFactory.set_getorderlists(userid, 'trial', type, random)
      }

      $scope.wdsy_doRefresh = function() {
        // 下拉更新 我的试用订单
        trialOrderFactory.set_getorderlists(userid, 'trial', type, random)

        //通知更新完成
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载更多
      $scope.wdsy_loadMore = function() {
        //console.log("上拉加载更多来了");

        //发起后端请求;
        trialOrderFactory.wdsy_loadMore(userid, 'trial', type, random)
      }

      //放弃申请

      $scope.abandon_application = function(order_id) {
        $scope.add_order_number = function() {
          //超级笨方法 屏蔽页面顶部的点击事件
          return false
        }

        $ionicPopup
          .confirm({
            title: '放弃申请', // String. 弹窗的标题。
            //   subTitle: '', // String (可选)。弹窗的副标题。
            template: '一旦放弃申请,则不可再申请该试用商品,您确定要放弃申请？', // String (可选)。放在弹窗body内的html模板。
            cancelText: '取消', // String (默认: 'Cancel')。取消按钮的文字。
            cancelType: '', // String (默认: 'button-default')。取消按钮的类型。
            okText: '确定', // String (默认: 'OK')。OK按钮的文字。
            okType: '' // String (默认: 'button-positive')。OK按钮的类型。
          })
          .then(function(res) {
            if (res == false) {
              //超级笨方法 恢复页面顶部的点击事件
              $scope.add_order_number = function(order_id, goods_id, order_status) {
                //如果订单状态是待审核资格，则跳转商品页面

                if (order_status == 1) {
                  $state.go('tab.show_trial', {
                    id: goods_id
                  })

                  return false
                } else if (order_status == 2 || order_status == 4) {
                  $state.go('tab.trial_order_id', {
                    id: order_id,
                    goodid: goods_id
                  })
                } else if (order_status == 8) {
                  //填写试用报告

                  $state.go('tab.trial_order_report', {
                    id: order_id,
                    goodid: goods_id
                  })
                } else {
                  $state.go('tab.trial_order_id', {
                    id: order_id,
                    goodid: goods_id
                  })
                }

                //订单状态是已通过(不含试用报告) 跳转订单号填写页面
              }
            } else {
              //发起后端请求 放弃试用资格

              trialOrderFactory.set_close_order_sn(order_id, userid, random)
            }
          })
      }

      // 接收关闭申请成功通知
      $scope.$on('trialOrderFactory.set_close_order_sn', function() {
        // 超级笨方法 恢复页面顶部点击事件。
        $scope.add_order_number = function(order_id, goods_id, order_status) {
          //如果订单状态是待审核资格，则跳转商品页面

          if (order_status == 1) {
            $state.go('tab.show_trial', {
              id: goods_id
            })

            return false
          } else if (order_status == 2 || order_status == 4) {
            $state.go('tab.trial_order_id', {
              id: order_id,
              goodid: goods_id
            })
          } else if (order_status == 8) {
            //填写试用报告

            $state.go('tab.trial_order_report', {
              id: order_id,
              goodid: goods_id
            })
          } else {
            $state.go('tab.trial_order_id', {
              id: order_id,
              goodid: goods_id
            })
          }

          //订单状态是已通过(不含试用报告) 跳转订单号填写页面
        }

        var data_close_order = trialOrderFactory.get_close_order_sn()

        //console.log(data_close_order);

        $ionicLoading.show({
          noBackdrop: true,
          template: data_close_order.msg,
          duration: 1000
        })

        //重新获取当前的待审核列表
        trialOrderFactory.set_getorderlists(userid, 'trial', -1, random)

        var a = document.getElementById('sub_header_list').getElementsByTagName('a')

        a[0].className = 'button button-clear sub_button_select'
      })
    }
  ])

  /*
    name  试用填写订单号 填写试用报告
   */

  .controller('trial_order_add', [
    '$rootScope',
    '$interval',
    '$filter',
    '$ionicActionSheet',
    '$location',
    '$scope',
    '$state',
    '$stateParams',
    '$ionicPopup',
    '$ionicModal',
    '$ionicLoading',
    'Upload',
    'ENV',
    'StorageFactory',
    'rebate_showFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'configFactory',
    'uploadFactory',
    'fileReader',
    function(
      $rootScope,
      $interval,
      $filter,
      $ionicActionSheet,
      $location,
      $scope,
      $state,
      $stateParams,
      $ionicPopup,
      $ionicModal,
      $ionicLoading,
      Upload,
      ENV,
      StorageFactory,
      rebate_showFactory,
      UserProfileFactory,
      trialOrderFactory,
      configFactory,
      uploadFactory,
      fileReader
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }
        $rootScope.hideTabs = 'tabs-item-hide'
        //获得传过来的商品id
        var aid = $stateParams.goodid
        //获得传过来的订单id
        var order_id = $stateParams.id

        $scope.ENV = ENV

        // 获取用户id
        userid = StorageFactory.get('user').data.userid

        //获取用户签名
        random = StorageFactory.get('user').data.random

        //第二步 获取这个订单详细信息
        trialOrderFactory.set_order_info(order_id, userid, random)
      })

      $scope.images_list = []
      //接收文件上传通知
      $scope.$on('uploadFactory.set_upload', function() {
        $ionicLoading.show({
          noBackdrop: true,
          template: '图片已成功上传',
          duration: 2000
        })

        //服务器远程图片地址
        $scope.response = uploadFactory.get_upload()

        var imgUrl = ENV.imgUrl
        for (x in $scope.response) {
          if (x == 1) $scope.sybg_vm.img = imgUrl + $scope.response[x]
        }
      })

      //获得传过来的商品id
      var aid = $stateParams.goodid
      //获得传过来的订单id
      var order_id = $stateParams.id

      $scope.ENV = ENV

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      //获取后台试用活动设置
      //后台请求获取后台的活动配置
      configFactory.set_trial_config()

      //接收配置请求通知
      $scope.$on('configFactory.set_trial_config', function() {
        //alert("配置通知来了");

        var trial_config = configFactory.get_trial_config() //网站试用配置

        //试用资格审核时间限制

        $scope.seller_check_time = trial_config.data.seller_check_time * 86400

        //已通过试用，待填写订单号时间限制
        $scope.buyer_write_order_time = trial_config.data.buyer_write_order_time * 3600

        //填写试用报告时间限制
        $scope.buyer_write_talk_time = trial_config.data.buyer_write_talk_time * 86400

        //试用报告审核时间限制
        $scope.seller_trialtalk_check = trial_config.data.seller_trialtalk_check.value * 86400

        //待审核订单号时间限制
        $scope.seller_order_check_time = trial_config.data.seller_order_check_time * 3600

        //修改订单号时间限制

        $scope.buyer_check_update_order_sn = trial_config.data.buyer_check_update_order_sn * 3600
      })

      // 第一步 获取这个商品详情
      rebate_showFactory.get_rebate_show(aid)

      // 定义用户填写订单号数组
      $scope.order_vm = {
        mum: ''
      }

      $scope.sybgimg = function() {
        return $('#File1').click()
      }

      //定义用户填写试用报告数组
      $scope.sybg_vm = {
        pinfen: '',
        img: 'img/shai_img.jpg',
        xinde: ''
      }

      $scope.onFileSelect = function($files) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在上传 请稍后...',
          duration: 1000
        })

        uploadFactory.set_upload($files)
      }

      //填写试用报告
      $scope.sybg_add = function() {
        if ($scope.sybg_vm.img != 'img/shai_img.jpg') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请分享一张宝贝图片...',
            duration: 1000
          })

          return false
        }

        if ($scope.sybg_vm.xinde == '') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请分享一些试用过程和经验...',
            duration: 1000
          })

          return false
        }

        $ionicLoading.show({
          noBackdrop: true,
          template: '亲,正在提交试用报告,请稍等...',
          duration: 1000
        })

        var pinfen_id = !$scope.sybg_vm.pinfen ? 3 : $scope.sybg_vm.pinfen.id //设置默认评分
        //后端提交试用报告
        trialOrderFactory.set_trial_report(
          order_id,
          pinfen_id,
          $scope.sybg_vm.img,
          $scope.sybg_vm.xinde,
          userid,
          random
        )
      }

      //接收试用报告  返回结果
      $scope.$on('trialOrderFactory.set_trial_report', function() {
        var sybg_data = trialOrderFactory.get_trial_report()

        if (sybg_data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: sybg_data.msg,
            duration: 1000
          })

          //跳转返回我的订单页面
          $state.go('tab.trial_order')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: sybg_data.msg,
            duration: 1000
          })
        }
      })

      //接收试用报告  返回结果
      $scope.$on('uploadFactory.set_upload', function() {
        var data = uploadFactory.get_upload()

        if (data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传完成',
            duration: 1000
          })

          $scope.sybg_vm.img = ENV.imgUrl + data.url
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传失败',
            duration: 1000
          })
        }
      })

      // 试用小课堂
      // 试用是不用拿钱买的,最终下单的钱是需要返还回来的。
      // 商家拿产品来试用，是为了提高曝光率,提示销量。
      // 试用品价值从几元-几百不等,每天都有好货额。
      // 商家提供了喜欢的试用品,咋可得给人家一个好评。
      // 快速提现,最快2小时到账额。
      // 加入vip,什么审核都是浮云,vip要的就是快。
      // 一般是每天10点上新品,记得第一时间来呃。
      // 有平台做担保,放100个心吧。
      // 积分可以兑换试用品，平时多签到，参与平台活动。
      // 遇到难题不用怕，在线客服来帮忙。
      // 平台出现试用大神,完成上百笔试用了。
      // 遇到喜欢的商品,一定要第一时间申请，晚了就木有了。
      // 红包是商家为了奖励用户参与发给大家的。

      // 如果一天成功申请一个试用品,一个月下来东西都用不完啊。

      $scope.pingfen = [
        {
          name: '★完全不满意,比较失望',
          id: 1
        },
        {
          name: '★★非常不满意,只是感谢提供',
          id: 3
        },
        {
          name: '★★★一般满意,还有待提高',
          id: 3
        },
        {
          name: '★★★★比较满意,离完美差点',
          id: 4
        },
        {
          name: '★★★★★完全满意,太喜欢这款',
          id: 5
        }
      ]

      $scope.$on('NewsContent.rebateshow', function() {
        $scope.txddh_showdata = rebate_showFactory.getshow()

        console.log($scope.txddh_showdata)
        $scope.txddh_showloading = false

        //console.log($scope.txddh_showdata);
        //如果是搜索下单，随机取一个搜索关系词
        if ($scope.txddh_showdata.type == 'search') {
          var keyword_string = $scope.txddh_showdata.goods_rule.keyword
          var keyword_string = keyword_string.replace(/，/g, ',') //增强用户体验，替换，
          var keyword_obj2 = keyword_string.split(',') //字符串转化为数组
          //console.log(keyword_obj2);
          $scope.goods_keyword = keyword_obj2[Math.floor(Math.random() * keyword_obj2.length)] //随机取得一个字符
        }
      })

      //接收订单详细信息通知
      $scope.$on('trialOrderFactory.set_order_info', function() {
        $scope.order_info = trialOrderFactory.get_order_info()
        //console.log($scope.order_info);
      })

      $scope.add_order_number = function() {
        var alertPopup = $ionicPopup.show({
          title: '填写订单号',
          templateUrl: 'a.html',
          scope: $scope,
          buttons: [
            {
              text: '取消'
            },
            {
              text: '确定',
              type: 'button-assertive',
              onTap: function(e) {
                var patten = /^\d{9,20}$/
                if (!patten.test($scope.order_vm.mum)) {
                  e.preventDefault()
                  $ionicLoading.show({
                    noBackdrop: true,
                    template: '亲,订单号是9位以上数字！',
                    duration: 1000
                  })
                  return false
                }

                //发起后端请求 填写订单号
                trialOrderFactory.set_fill_order_sn(order_id, $scope.order_vm.mum, userid, random)
              }
            }
          ]
        })
      }

      //接收填写订单号返回结果通知
      $scope.$on('trialOrderFactory.set_fill_order_sn', function() {
        var data_fill_order_sn = trialOrderFactory.get_fill_order_sn()

        if (data_fill_order_sn.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_fill_order_sn.msg,
            duration: 1000
          })

          //跳转返回我的订单页面
          $state.go('tab.trial_order')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_fill_order_sn.msg,
            duration: 1000
          })
        }
      })

      //点击去下单 普通下单直接跳转至淘宝,搜索下单,直接跳转淘宝引导页面
      //点击去下单 普通下单直接跳转至淘宝,搜索下单,直接跳转淘宝引导页面
      $scope.To_order = function(type, source, url) {
        source = $filter('f_source')(source)

        var go_tiele = source + '下单'

        if (type == 'search') {
          go_tiele = source + '搜索下单'
          url = getDomain(url)
          //提取搜索下单地址
          function getDomain(weburl) {
            var urlReg = /.*\:\/\/([^\/]*).*/
            domain = weburl.match(urlReg)
            domain = domain[1].split('.')
            domain = 'http://m.' + domain[1] + '.' + domain[2]
            return domain != null && domain.length > 0 ? domain : url
          }
          window.open(url)
        }

        if (type == 'qrcode') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请扫描页面二维码下单',
            duration: 2000
          })

          return false
        }

        if (type == 'general' || type == 'ask') {
          //普通下单
          window.open(url)
        }
      }

      //我要申诉
      // 如果状态不等于4 拒绝申诉
      $scope.Appeal = function(status, order_id, goods_id) {
        //console.log(status, order_id, goods_id);
        if (status != 4) return false
        //跳转申诉页面 弹窗还是新页面纠结啊

        //跳转订单申诉页面
        $state.go('tab.order_appeal', {
          id: order_id,
          aid: goods_id
        })
      }

      //获取订单日志记录！
      $scope.Order_log = function() {
        //console.log(order_id);

        $state.go('tab.order_log', {
          id: order_id
        })
      }

      //第三步 判断下单类型 是普通下单 还是答案下单 二维码下单

      //接收订单返回通知
      $scope.$on('trialOrderFactory.set_getorderlists', function() {
        $scope.wdsy_showloading = false

        $scope.trial_getorderlists = trialOrderFactory.get_getorderlists()

        //console.log($scope.trial_getorderlists);
      })
    }
  ])

  /*
    name  试用详情页
   */

  .controller('show_trial', [
    '$rootScope',
    '$scope',
    '$ionicPopup',
    '$state',
    '$ionicLoading',
    '$ionicModal',
    '$stateParams',
    '$filter',
    'StorageFactory',
    'UserProfileFactory',
    'rebate_showFactory',
    'configFactory',
    'trialOrderFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $ionicPopup,
      $state,
      $ionicLoading,
      $ionicModal,
      $stateParams,
      $filter,
      StorageFactory,
      UserProfileFactory,
      rebate_showFactory,
      configFactory,
      trialOrderFactory,
      ENV
    ) {
      //获得传过来的商品id
      var aid = $stateParams.id
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'

        // 获取会员参与该活动情况
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          //console.log(StorageFactory.get('user'));
          $scope.lnvolved_status = 0 //会员参与活动状态
          $scope.login_status = 0 //会员登录状态
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid
          //获取用户签名
          random = StorageFactory.get('user').data.random

          $scope.Member_group = StorageFactory.get('profile').groupid

          //获取用户支付宝绑定状态
          $scope.allpay_status = StorageFactory.get('profile').alipay_status

          //获取用户邮箱认证状态
          $scope.emall_status = StorageFactory.get('profile').email_status

          //获取用户实名认证状态
          $scope.name_status = StorageFactory.get('profile').name_status

          //console.log(StorageFactory.get('profile').name_status);

          // 获取用户手机认证状态
          $scope.phone_status = StorageFactory.get('profile').phone_status

          //获取用户绑定的淘宝帐号数量
          UserProfileFactory.set_username_taobao(userid, random)

          //获取会员是否参与过本次活动
          trialOrderFactory.set_order_is_join(aid, userid, random)

          $scope.lnvolved_status = 1

          //获取会员积分
          $scope.user_point = StorageFactory.get('profile').point
          $scope.login_status = 1 //会员登录状态

          // alert($scope.login_status);
        }
      })

      //开始测试分享

      $scope.fenxiang = function() {
        var title = $scope.showdata.title
        var content =
          '[免费试用]' + $scope.showdata.title + '限量份数' + $scope.showdata.goods_number + '快来免费申请吧！'
        var url = ENV.siteUrl + $scope.showdata.url
        var imageurl = $filter('imgUrl')($scope.showdata.thumb)
        window.plugins.Baidushare.bdshare(
          title,
          content,
          url,
          imageurl,
          function(success) {
            if (success == 1) {
              $ionicLoading.show({
                noBackdrop: true,
                template: '分享成功！',
                duration: 1000
              })
            } else if (success == 2) {
            } else {
            }
          },
          function(fail) {
            alert('encoding failed: ' + fail)
          }
        )
      }

      $scope.ENV = ENV

      $scope.Application_reasons = ''

      $scope.sy_showloading = true

      $scope.Switch = 1

      $scope.data_time = Math.round(new Date().getTime() / 1000)

      //接收通知 当前用户已绑定淘宝帐号
      $scope.$on('UserProfileFactory.set_username_taobao', function() {
        $scope.data_bind_taobao = UserProfileFactory.get_username_taobao()

        //获得用户默认淘宝帐号 如果为空 则指定第一个
        var lists = $scope.data_bind_taobao.lists

        function data_bind_taobao_default() {
          for (var x in lists) {
            if (lists[x].is_default == 1) {
              $scope.data_bind_taobao_default = lists[x]
            } else {
              $scope.data_bind_taobao_default = lists[0]
            }
          }
        }

        data_bind_taobao_default()

        taobao_default_id = $scope.data_bind_taobao_default == undefined ? '' : $scope.data_bind_taobao_default.id

        $scope.vm.data_bind_taobao_default.id =
          $scope.data_bind_taobao_default == undefined ? '' : $scope.data_bind_taobao_default.id
      })

      // 定义选择淘宝号 和申请理由数组   答案下单的时候 的问题和答案
      var vm = ($scope.vm = {
        data_bind_taobao_default: {
          id: 0
        }
      })

      $('.i_tryout .s_part6 .part').bind('click', function() {
        $('.i_tryout .s_part6 .part').attr('class', 'part')
        $(this).attr('class', 'part sel')
      })

      //后台请求获取后台的活动配置
      configFactory.set_trial_config()

      // 获取商品详情
      rebate_showFactory.get_rebate_show(aid)

      //商品介绍

      $scope.goods_body = function() {
        $scope.Switch = 1
      }

      //获取商品的申请人数

      $scope.good_user_list = function() {
        $scope.user_list_showloading = true
        $scope.Switch = 2

        rebate_showFactory.set_good_user_list(aid, 1)
      }

      //获取试用报告列表
      $scope.report_lists = function() {
        $scope.report_list_showloading = true
        $scope.Switch = 3
        rebate_showFactory.set_trial_report_lists(aid)
      }

      //试用报告上拉加载更多
      $scope.sybg_loadMore = function() {
        rebate_showFactory.sybg_loadMore(aid)
      }

      //接收配置请求通知
      $scope.$on('configFactory.set_trial_config', function() {
        //alert("配置通知来了");

        var trial_config = configFactory.get_trial_config() //网站试用配置

        $scope.bind_alipay = trial_config.data.buyer_join_condition.bind_alipay //绑定支付宝

        $scope.bind_taobao = trial_config.data.buyer_join_condition.bind_taobao //绑定淘宝

        $scope.bind_email = trial_config.data.buyer_join_condition.email //绑定邮箱

        $scope.information = trial_config.data.buyer_join_condition.information //完善个人资料 客户端判断废弃

        $scope.reason = trial_config.data.buyer_join_condition.reason // 需申请理由

        $scope.bind_phone = trial_config.data.buyer_join_condition.phone //绑定手机

        //console.log($scope.bind_phone);

        $scope.realname = trial_config.data.buyer_join_condition.realname // 实名认证
      })

      //接收商品详情通知

      $scope.$on('NewsContent.rebateshow', function() {
        $scope.showdata = rebate_showFactory.getshow()
        $scope.sy_showloading = false
      })

      //接收参与用户返回列表通知
      $scope.$on('rebate_showFactory.set_good_user_list', function() {
        $scope.user_list = rebate_showFactory.get_good_user_list()
        $scope.user_list_showloading = false

        $('.hang1').each(function() {
          if (
            $(this)
              .find('img')
              .height() <
            $(this)
              .find('img')
              .width()
          ) {
            $(this)
              .find('img')
              .css(
                'height',
                $(this)
                  .find('img')
                  .width()
              )
          }
        })
      })

      //接收用户试用报告列表通知

      $scope.$on('rebate_showFactory.set_trial_report_lists', function() {
        $scope.report_list = rebate_showFactory.get_trial_report_lists()
        $scope.report_list_showloading = false

        $scope.sybg_hasNextPage = rebate_showFactory.sybg_hasNextPage()
        //console.log($scope.report_list);
        $scope.$broadcast('scroll.infiniteScrollComplete') //广播通知！
      })

      //接收用户是否参与过抢购通知
      $scope.$on('trialOrderFactory.set_order_is_join', function() {
        $scope.order_is_join = trialOrderFactory.get_order_is_join()

        // $scope.report_list_showloading = false;
      })

      //普通用户申请试用  如果用户已经参与过本次活动，则不能再次申请
      //活动商品状态   -3 待审核待付款 -2 待审核已付款 -1 待上线 1活动进行中 2.活动结算 3. 已完成  4.撤销 5.屏蔽

      /*--------------普通申请------------------------
        -1 如果用户未登录，先跳转至会员登录页面

        0.如果当前活动状态不为进行中，拒绝申请。
        1.检测用户是否被商家拉入黑名单                      ---------------
        2.再次判断用户参与活动状态
        2.1 判断后台设置 需完善基本资料   需完成手机认证   需完成邮箱认证   需完成实名认证   需绑定淘宝账号   需绑定支付宝账号  需申请理由
        2.2 判断下单类型 如果是答案下单 则跳转输入答案页面

        3.判断后台设置，是否允许同一用户申请一个商家下的多次试用 ----------
        4.如果后台开启了让用户选择淘宝账号，则弹出让用户选择淘宝帐号，如果未绑定，则先跳转绑定页面
        5.如果后台开启了多商家说点什么，则同时跟淘宝帐号一起显示
        6.提交后台申请，等待返回结果。提交参数 userid 商品id 签名 

    */
      $scope.Apply_trial = function() {
        //console.log($scope.login_status);

        //未登录去登录
        if ($scope.login_status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，请先登录后再申请！',
            duration: 1500
          })

          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        //活动状态不对 拒绝申请
        if ($scope.showdata.status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，当前活动未开始或已结束！',
            duration: 1500
          })

          return false
        }

        //判断是否参与过本活动
        if ($scope.lnvolved_status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您已参与过本次活动,不能再次参与！',
            duration: 1500
          })

          return false
        }

        //判断后台配置开启的参与活动条件 根据配置提示用户完善
        if (
          ($scope.bind_alipay == 5 && $scope.allpay_status != 1) ||
          ($scope.realname == 3 && $scope.name_status != 1) ||
          ($scope.bind_email == 2 && $scope.emall_status != 1) ||
          ($scope.bind_phone == 1 && $scope.phone_status != 1) ||
          ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count < 1)
        ) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.alert({
            title: '参与条件',
            templateUrl: 'a.html',
            scope: $scope
          })
          alertPopup.then(function(res) {})
          return false
        }

        //判断是普通申请还是答案申请
        if ($scope.showdata.type != undefined && $scope.showdata.type == 'ask') {
          var alertPopup = $ionicPopup.show({
            title: '请先回答商家问题',
            templateUrl: 'c.html',
            scope: $scope,
            buttons: [
              {
                text: '取消'
              },
              {
                text: 'OK',
                type: 'button-assertive button-outline',
                onTap: function(e) {
                  if (!$scope.vm.Answer) {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲，答案不能为空',
                      duration: 1500
                    })

                    return false
                  } else if ($scope.vm.Answer == $scope.showdata.goods_rule.ask.answer) {
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,回答正确！',
                      duration: 1000
                    })

                    $scope.taobao_reason()
                  } else {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,答案错误,请仔细找找！',
                      duration: 1500
                    })

                    return false
                  }
                }
              }
            ]
          })
          alertPopup.then(function(res) {})

          return false
        }

        //都未开启 提交后台申请
        $scope.taobao_reason()
      }

      //--------vip申请-------
      /*   判断商品是否支持vip
         第一 判断用户组是否是vip，
         第二 判断用户的vip剩余次数
     //    第三 判断用户是否参与过这个活动
         第四 如果满足以上条件，则扣除用户次数，返回状态 。
    */
      $scope.vip_Apply_trial = function() {
        if ($scope.showdata.goods_vipfree != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '当前商品不支持使用vip特权!',
            duration: 2000
          })

          return false
        }

        if ($scope.Member_group != 2) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您还不是vip,无法享受vip特权!,加入vip,立享多项特权！',
            duration: 2000
          })
          // 跳转vip介绍页面
          return false
        }

        //判断是普通申请还是答案申请
        if ($scope.showdata.type != undefined && $scope.showdata.type == 'ask') {
          var alertPopup = $ionicPopup.show({
            title: '请先回答商家问题',
            templateUrl: 'c.html',
            scope: $scope,
            buttons: [
              {
                text: '取消'
              },
              {
                text: 'OK',
                type: 'button-assertive button-outline',
                onTap: function(e) {
                  if (!$scope.vm.Answer) {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲，答案不能为空',
                      duration: 1500
                    })

                    return false
                  } else if ($scope.vm.Answer == $scope.showdata.goods_rule.ask.answer) {
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,回答正确！',
                      duration: 1000
                    })
                    $scope.vip_Apply_trial2()
                  } else {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,答案错误,请仔细找找！',
                      duration: 1500
                    })

                    return false
                  }
                }
              }
            ]
          })
          alertPopup.then(function(res) {})
          return false
        }

        if ($scope.Member_group != 2) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您还不是vip,无法享受vip特权!,加入vip,立享多项特权！',
            duration: 2000
          })
          // 跳转vip介绍页面
          return false
        }

        // 如果后台开启了 需要绑定淘宝帐号 则vip弹出选择淘宝帐号
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count < 1) {
          //弹出提示窗口页面 提示用户完成活动条件
          $ionicLoading.show({
            noBackdrop: true,
            template: '请先绑定淘宝帐号，才能使用vip特权！',
            duration: 2000
          })

          $state.go('tab.user_profile_taobao') //路由跳转绑定淘宝帐号

          return false
        }

        //选择淘宝帐号 好下单
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count > 0) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.confirm({
            title: '请选择要用的淘宝帐号',
            templateUrl: 'e.html',
            cancelText: '放弃试用', // String (默认: 'Cancel')。一个取消按钮的文字。
            okText: '确定试用', // String (默认: 'OK')。OK按钮的文字。
            okType: 'button-assertive button-outline', // String (默认: 'button-positive')。OK按钮的类型。
            scope: $scope
          })
          alertPopup.then(function(res) {
            if (res) {
              var taobao_id =
                $scope.vm.data_bind_taobao_default == undefined
                  ? taobao_default_id
                  : $scope.vm.data_bind_taobao_default.id
              trialOrderFactory.vip_apply_order(aid, taobao_id, '', userid, random)
              return false
            } else {
            }
          })
          return false
        }

        // 发起后端请求 走vip试用渠道
        trialOrderFactory.vip_apply_order(aid, '', '', userid, random)
      }

      $scope.vip_Apply_trial2 = function() {
        if ($scope.showdata.goods_vipfree != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '当前商品不支持使用vip特权!',
            duration: 2000
          })

          return false
        }

        if ($scope.Member_group != 2) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您还不是vip,无法享受vip特权!,加入vip,立享多项特权！',
            duration: 2000
          })
          // 跳转vip介绍页面
          return false
        }

        // 如果后台开启了 需要绑定淘宝帐号 则vip弹出选择淘宝帐号
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count < 1) {
          //弹出提示窗口页面 提示用户完成活动条件
          $ionicLoading.show({
            noBackdrop: true,
            template: '请先绑定淘宝帐号，才能使用vip特权！',
            duration: 2000
          })

          $state.go('tab.user_profile_taobao') //路由跳转绑定淘宝帐号

          return false
        }

        //选择淘宝帐号 好下单
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count > 0) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.confirm({
            title: '请选择要用的淘宝帐号',
            templateUrl: 'e.html',
            cancelText: '放弃试用', // String (默认: 'Cancel')。一个取消按钮的文字。
            okText: '确定试用', // String (默认: 'OK')。OK按钮的文字。
            okType: 'button-assertive button-outline', // String (默认: 'button-positive')。OK按钮的类型。
            scope: $scope
          })
          alertPopup.then(function(res) {
            if (res) {
              var taobao_id =
                $scope.vm.data_bind_taobao_default == undefined
                  ? taobao_default_id
                  : $scope.vm.data_bind_taobao_default.id
              trialOrderFactory.vip_apply_order(aid, taobao_id, '', userid, random)
              return false
            } else {
            }
          })
          return false
        }
        // 发起后端请求 走vip试用渠道
        trialOrderFactory.vip_apply_order(aid, '', '', userid, random)
      }

      /*   1.首先获得该商品兑换所需积分
     2.如果会员积分小于本次兑换所需积分，则提示积分不足
     3.如果积分充足 那么则提示绑定淘宝帐号 或者选择试用淘宝帐号
     4.最后再确定是否兑换
*/
      $scope.jinbi_Apply_trial = function() {
        if ($scope.showdata.goods_point == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '非常抱歉,本商品暂不支持积分兑换',
            duration: 1500
          })

          return false
        }

        if (Number($scope.user_point) < Number($scope.showdata.point_num)) {
          $ionicLoading.show({
            noBackdrop: true,
            template:
              '非常抱歉,兑换本商品需要' +
              $scope.showdata.point_num +
              '积分,<br/>您目前拥有' +
              $scope.user_point +
              '积分<br/>多参加任务,每日签到,大转盘可获得更多积分！',
            duration: 3000
          })
          return false
        }

        //判断是普通申请还是答案申请
        if ($scope.showdata.type != undefined && $scope.showdata.type == 'ask') {
          var alertPopup = $ionicPopup.show({
            title: '请先回答商家问题',
            templateUrl: 'c.html',
            scope: $scope,
            buttons: [
              {
                text: '取消'
              },
              {
                text: 'OK',
                type: 'button-assertive button-outline',
                onTap: function(e) {
                  if (!$scope.vm.Answer) {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲，答案不能为空',
                      duration: 1500
                    })

                    return false
                  } else if ($scope.vm.Answer == $scope.showdata.goods_rule.ask.answer) {
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,回答正确！',
                      duration: 1000
                    })
                    $scope.jinbi_Apply_trial2()
                  } else {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,答案错误,请仔细找找！',
                      duration: 1500
                    })

                    return false
                  }
                }
              }
            ]
          })
          alertPopup.then(function(res) {})
          return false
        }

        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count < 1) {
          //弹出提示窗口页面 提示用户完成活动条件
          $ionicLoading.show({
            noBackdrop: true,
            template: '请先绑定淘宝帐号，才能使用积分兑换！',
            duration: 2000
          })

          $state.go('tab.user_profile_taobao') //路由跳转绑定淘宝帐号

          return false
        }

        //选择淘宝帐号 好下单
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count > 0) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.confirm({
            title: '请选择要用的淘宝帐号',
            subTitle: '积分余额 ' + $scope.user_point + ' 本次兑换花费 ' + $scope.showdata.point_num + ' 积分',
            templateUrl: 'e.html',
            cancelText: '放弃兑换',
            okText: '确定兑换',
            okType: 'button-assertive button-outline',
            scope: $scope
          })
          alertPopup.then(function(res) {
            if (res) {
              var taobao_id =
                $scope.vm.data_bind_taobao_default == undefined
                  ? taobao_default_id
                  : $scope.vm.data_bind_taobao_default.id
              trialOrderFactory.point_apply_order(aid, taobao_id, '', userid, random)
              return false
            } else {
            }
          })
          return false
        }
        trialOrderFactory.point_apply_order(aid, '', '', userid, random)
      }

      $scope.jinbi_Apply_trial2 = function() {
        if ($scope.showdata.goods_point == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '非常抱歉,本商品暂不支持积分兑换',
            duration: 1500
          })

          return false
        }

        if ($scope.user_point < $scope.showdata.point_num) {
          $ionicLoading.show({
            noBackdrop: true,
            template:
              '非常抱歉,兑换本商品需要' +
              $scope.showdata.point_num +
              '积分,<br/>您目前拥有' +
              $scope.user_point +
              '积分<br/>多参加任务,每日签到,大转盘可获得更多积分！',
            duration: 3000
          })
          return false
        }

        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count < 1) {
          //弹出提示窗口页面 提示用户完成活动条件
          $ionicLoading.show({
            noBackdrop: true,
            template: '请先绑定淘宝帐号，才能使用积分兑换！',
            duration: 2000
          })

          $state.go('tab.user_profile_taobao') //路由跳转绑定淘宝帐号

          return false
        }

        //选择淘宝帐号 好下单
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count > 0) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.confirm({
            title: '请选择要用的淘宝帐号',
            subTitle: '积分余额 ' + $scope.user_point + ' 本次兑换花费 ' + $scope.showdata.point_num + ' 积分',
            templateUrl: 'e.html',
            cancelText: '放弃兑换',
            okText: '确定兑换',
            okType: 'button-assertive button-outline',
            scope: $scope
          })
          alertPopup.then(function(res) {
            if (res) {
              var taobao_id =
                $scope.vm.data_bind_taobao_default == undefined
                  ? taobao_default_id
                  : $scope.vm.data_bind_taobao_default.id
              trialOrderFactory.point_apply_order(aid, taobao_id, '', userid, random)
              return false
            } else {
            }
          })
          return false
        }

        trialOrderFactory.point_apply_order(aid, '', '', userid, random)
      }

      //弹出选择淘宝号 和对商家说点什么  根据后台配置来的 如果后台关闭了则不需要

      $scope.taobao_reason = function() {
        if (($scope.bind_taobao == 4 && $scope.data_bind_taobao.count > 0) || $scope.reason == 7) {
          var alertPopup = $ionicPopup.alert({
            title: '选择帐号',
            templateUrl: 'b.html',
            scope: $scope,
            okText: '提交申请',
            buttons: [
              {
                text: '放弃'
              },
              {
                text: '提交申请',
                type: 'button-assertive',
                onTap: function(e) {
                  $scope.Submit_trial()
                }
              }
            ]
          })
          alertPopup.then(function(res) {})
          return false
        }
        $scope.Submit_trial()
      }

      //提交后台申请试用   传入
      $scope.Submit_trial = function(reason) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '亲，正在为您提交申请！',
          duration: 1500
        })

        var taobao_id =
          $scope.vm.data_bind_taobao_default == undefined ? taobao_default_id : $scope.vm.data_bind_taobao_default.id
        taobao_id = $scope.bind_taobao == 4 ? taobao_id : ''

        console.log(taobao_id)

        var reason = $scope.vm.Application_reasons
        trialOrderFactory.apply_order(aid, taobao_id, reason, userid, random)
      }

      //接收申请结果返回通知

      $scope.$on('trialOrderFactory.apply_order', function() {
        var data_get_apply_order = trialOrderFactory.get_apply_order()

        if (data_get_apply_order.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_get_apply_order.msg,
            duration: 1500
          })

          return false
        } else if (data_get_apply_order.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '试用申请成功,请等待商家审核，请随时留意app通知。',
            duration: 1500
          })
          //按钮变成已申请状态
          $scope.order_is_join.data = 0
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '程序员哥哥，正在抢修',
            duration: 1500
          })
        }
      })

      //接收vip申请结果返回通知

      $scope.$on('trialOrderFactory.vip_apply_order', function() {
        var data_get_apply_order = trialOrderFactory.get_vip_apply_order()

        if (data_get_apply_order.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_get_apply_order.msg,
            duration: 1500
          })

          return false
        } else if (data_get_apply_order.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '恭喜您！成功使用vip特权试用本商品，赶快去下单吧！',
            duration: 1500
          })

          //按钮变成已申请状态
          $scope.order_is_join.data = 0

          //跳转到填写订单号页面。
          $state.go('tab.trial_order_id', {
            id: data_get_apply_order.data,
            goodid: aid
          })

          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '程序员哥哥，正在抢修',
            duration: 1500
          })
        }
      })

      //接收金币兑换结果通知

      $scope.$on('trialOrderFactory.point_apply_order', function() {
        var data_get_apply_order = trialOrderFactory.get_point_apply_order()

        if (data_get_apply_order.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_get_apply_order.msg,
            duration: 1500
          })

          return false
        } else if (data_get_apply_order.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '恭喜您！成功使用' + $scope.showdata.point_num + '积分兑换本商品试用特权，赶快去下单吧！',
            duration: 1500
          })

          //按钮变成已申请状态
          $scope.order_is_join.data = 0

          //跳转到填写订单号页面。
          $state.go('tab.trial_order_id', {
            id: data_get_apply_order.data,
            goodid: aid
          })

          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '程序员哥哥，正在抢修',
            duration: 1500
          })

          return false
        }
      })

      function hang1SetHeight() {
        $('.s_part2 .hang1').height($('.s_part2 .hang1 img').height())
      }

      $scope.$on('$ionicView.loaded', function() {
        // app.initialize();
      })
    }
  ])

  //订单申诉
  .controller('order_Appeal', [
    '$scope',
    '$state',
    '$ionicLoading',
    '$stateParams',
    'StorageFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'uploadFactory',
    'ENV',
    'Upload',
    function(
      $scope,
      $state,
      $ionicLoading,
      $stateParams,
      StorageFactory,
      UserProfileFactory,
      trialOrderFactory,
      uploadFactory,
      ENV,
      Upload
    ) {
      // 获取会员参与该活动情况
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
      } else {
      }

      //console.log($stateParams);
      //获得传过来的商品id
      var aid = $stateParams.aid
      //获得传过来的订单id
      $scope.order_id = $stateParams.id

      //console.log($scope.order_id);

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      //console.log(StorageFactory.get('profile'));

      //传入的订单id

      //获得用户已存在的手机 QQ

      $scope.appeal = {
        type: '',
        buyer_cause: '',
        buyer_cause_img1: 'img/pz1.jpg',
        buyer_cause_img2: 'img/pz2.jpg',
        buyer_cause_img3: 'img/pz3.jpg',
        buyer_phone: StorageFactory.get('profile').phone,
        buyer_qq: StorageFactory.get('profile').qq == 0 ? '' : StorageFactory.get('profile').qq
      }

      $scope.set_appeal = function() {
        //console.log($scope.appeal);

        if (!$scope.appeal.type) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请选择申诉类型 ',
            duration: 1500
          })

          return false
        }

        if ($scope.appeal.buyer_cause_img1 == 'img/pz1.jpg') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请上传申诉凭证',
            duration: 1500
          })

          return false
        }

        if ($scope.appeal.buyer_cause == '') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请填写申诉理由！',
            duration: 1500
          })

          return false
        }

        if ($scope.appeal.buyer_phone == '') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请填写联系手机！',
            duration: 1500
          })

          return false
        }

        if ($scope.appeal.buyer_qq == '') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请填写联系QQ！',
            duration: 1500
          })

          return false
        }
        //发起后端申诉请求
        trialOrderFactory.set_appeal_order(
          $scope.order_id,
          aid,
          $scope.appeal.type,
          $scope.appeal.buyer_cause,
          $scope.appeal.buyer_cause_img1,
          $scope.appeal.buyer_cause_img2,
          $scope.appeal.buyer_cause_img3,
          $scope.appeal.buyer_phone,
          $scope.appeal.buyer_qq,
          userid,
          random
        )
      }

      //接收申诉结果通知

      $scope.$on('trialOrderFactory.set_appeal_order', function() {
        var data_appeal_order = trialOrderFactory.get_appeal_order()

        if (data_appeal_order.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '申诉提交成功,我们会尽快联系商家处理...',
            duration: 1500
          })

          //跳转返回个人中心
          $state.go('tab.user')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_appeal_order.msg,
            duration: 1500
          })
        }
      })

      $scope.imgurl = function(id) {
        $scope.IMGid = id
        return $('#File' + id).click()
      }

      $scope.onFileSelect = function($files) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在上传 请稍后...',
          duration: 1000
        })

        uploadFactory.set_upload($files)
      }

      $scope.$on('uploadFactory.set_upload', function() {
        var data = uploadFactory.get_upload()

        if (data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传完成',
            duration: 1000
          })

          var arr = new Array(2)
          arr[0] = '$scope.appeal.buyer_cause_img'

          if ($scope.IMGid == '1') {
            $scope.appeal.buyer_cause_img1 = ENV.imgUrl + data.url
          }

          if ($scope.IMGid == '2') {
            $scope.appeal.buyer_cause_img2 = ENV.imgUrl + data.url
          }

          if ($scope.IMGid == '3') {
            $scope.appeal.buyer_cause_img3 = ENV.imgUrl + data.url
          }
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传失败',
            duration: 1000
          })
        }
      })

      $scope.$on('uploadFactory.set_upload', function() {
        var data = uploadFactory.get_upload()

        if (data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传完成',
            duration: 1000
          })

          $scope.sybg_vm.img = ENV.imgUrl + data.url
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传失败',
            duration: 1000
          })
        }
      })

      //接收文件上传通知
      $scope.$on('uploadFactory.set_upload', function() {
        $ionicLoading.show({
          noBackdrop: true,
          template: '图片已成功上传',
          duration: 2000
        })

        //服务器远程图片地址
        $scope.response = uploadFactory.get_upload()

        var imgUrl = ENV.imgUrl
        for (x in $scope.response) {
          if (x == 1) $scope.appeal.buyer_cause_img1 = imgUrl + $scope.response[x]
          if (x == 2) $scope.appeal.buyer_cause_img2 = imgUrl + $scope.response[x]
          if (x == 3) $scope.appeal.buyer_cause_img3 = imgUrl + $scope.response[x]
        }
      })
    }
  ])

  //订单日志 通用免费试用 购物返利 等。
  .controller('Userorder_log', [
    '$scope',
    '$stateParams',
    '$state',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    function($scope, $stateParams, $state, $ionicLoading, StorageFactory, UserProfileFactory, trialOrderFactory) {
      //如果不存在会员信息 则跳转登陆页面
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        $state.go('tab.user_login') //路由跳转登录
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      //接收传过来的订单id
      var order_id = $stateParams.id

      //console.log($stateParams);

      //发起后端请求
      trialOrderFactory.set_order_log(order_id, userid, random)

      //接收订单通知
      $scope.$on('trialOrderFactory.set_order_log', function() {
        $scope.data_order_log = trialOrderFactory.get_order_log()
      })
    }
  ])

  /*
    name  我的返利订单
   */

  .controller('rebate_order', [
    '$rootScope',
    '$timeout',
    '$scope',
    '$state',
    '$ionicPopup',
    '$ionicModal',
    '$ionicLoading',
    'ENV',
    'StorageFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'configFactory',
    function(
      $rootScope,
      $timeout,
      $scope,
      $state,
      $ionicPopup,
      $ionicModal,
      $ionicLoading,
      ENV,
      StorageFactory,
      UserProfileFactory,
      trialOrderFactory,
      configFactory
    ) {
      //页面加载之前事件

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.userInfo = ''
          $scope.logo_status = 0
        } else {
          // 获取用户id
          var userid = StorageFactory.get('user').data.userid

          //获取用户签名
          var random = StorageFactory.get('user').data.random

          // 第一步 后台获取我的返利订单
        }
      })

      $scope.ENV = ENV

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      trialOrderFactory.set_getorderlists(userid, 'rebate', 1, random)

      //设置加载中图标
      $scope.wdfl_showloading = true

      //默认显示已审核

      var type = 1

      //默认关闭上拉加载更多
      $scope.wdsy_hasNextPage = false

      //后台请求获取后台的返利活动配置
      configFactory.set_rebate_config()

      //接收配置请求通知
      $scope.$on('configFactory.set_rebate_config', function() {
        //alert("配置通知来了");

        var rebate_config = configFactory.get_rebate_config() //网站试用配置

        //填写订单号时间限制

        $scope.buyer_write_order_time = rebate_config.data.buyer_write_order_time * 60

        //console.log($scope.rebate_config);

        //商家审核总时间天数
        $scope.seller_check_time = rebate_config.data.seller_check_time * 86400

        //console.log($scope.seller_check_time);

        //修改订单号时间限制

        $scope.buyer_check_update_order_sn = rebate_config.data.buyer_check_update_order_sn * 3600
      })

      //接收订单返回通知
      $scope.$on('trialOrderFactory.set_getorderlists', function() {
        $scope.wdfl_showloading = false

        $scope.trial_getorderlists = trialOrderFactory.get_getorderlists()

        //console.log(trialOrderFactory.get_wdsy_hasNextPage());

        $scope.wdsy_hasNextPage = trialOrderFactory.get_wdsy_hasNextPage()

        $scope.$broadcast('scroll.infiniteScrollComplete') //广播通知！

        //console.log($scope.trial_getorderlists);
      })

      //点击订单跳转填写订单号页面
      $scope.add_order_number = function(order_id, goods_id, order_status, trial_report) {
        //如果订单状态是待审核资格，则跳转商品页面
        if (order_status == 1) {
          $state.go('tab.rebate_show', {
            id: goods_id
          })

          return false

          //如果订单状态是已通过待填写订单号 则跳转订单号填写页面
        } else if (order_status == 2 || order_status == 4 || order_status == 3) {
          $state.go('tab.rebate_order_id', {
            id: order_id,
            goodid: goods_id
          })

          //如果订单状态是填写试用报告 则跳转试用报告填写页面
        } else if (order_status == 7 && !trial_report) {
          //晒单分享

          $state.go('tab.rebate_The_sun', {
            id: order_id,
            goodid: goods_id
          })
        } else if (order_status == 5) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请等待商家最终确认返款！',
            duration: 1500
          })

          return false
        } else {
          $state.go('tab.rebate_show', {
            id: goods_id
          })
        }

        //订单状态是已通过(不含试用报告) 跳转订单号填写页面
      }

      // 第二步 针对不同的订单状态 写各种方法

      //设置高亮，获取不同状态的订单
      $scope.trial_order_changeTab = function(index, type_id) {
        $scope.wdfl_showloading = true
        type = type_id

        $scope.wdsy_showloading = true

        trialOrderFactory.set_getorderlists(userid, 'rebate', type, random)
      }

      $scope.wdfl_doRefresh = function() {
        // 下拉更新 我的返利订单
        trialOrderFactory.set_getorderlists(userid, 'rebate', type, random)

        //通知更新完成
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载更多
      $scope.wdfl_loadMore = function() {
        //console.log("上拉加载更多来了");

        //发起后端请求;
        trialOrderFactory.wdsy_loadMore(userid, 'rebate', type, random)
      }

      //放弃申请

      $scope.abandon_application = function(order_id) {
        $scope.add_order_number = function() {
          //超级笨方法 屏蔽页面顶部的点击事件
          return false
        }

        $ionicPopup
          .confirm({
            title: '放弃申请', // String. 弹窗的标题。
            //   subTitle: '', // String (可选)。弹窗的副标题。
            template: '一旦放弃申请,则不可再申请该试用商品,您确定要放弃申请？', // String (可选)。放在弹窗body内的html模板。
            cancelText: '取消', // String (默认: 'Cancel')。取消按钮的文字。
            cancelType: '', // String (默认: 'button-default')。取消按钮的类型。
            okText: '确定', // String (默认: 'OK')。OK按钮的文字。
            okType: 'button-assertive button-outline' // String (默认: 'button-positive')。OK按钮的类型。
          })
          .then(function(res) {
            if (res == false) {
              //超级笨方法 恢复页面顶部的点击事件
              $scope.add_order_number = function(order_id, goods_id, order_status) {
                //如果订单状态是待审核资格，则跳转商品页面

                if (order_status == 1) {
                  $state.go('tab.show_trial', {
                    id: goods_id
                  })

                  return false
                } else if (order_status == 2 || order_status == 4) {
                  $state.go('tab.trial_order_id', {
                    id: order_id,
                    goodid: goods_id
                  })
                } else if (order_status == 8) {
                  //填写试用报告

                  $state.go('tab.trial_order_report', {
                    id: order_id,
                    goodid: goods_id
                  })
                } else {
                  $state.go('tab.trial_order_id', {
                    id: order_id,
                    goodid: goods_id
                  })
                }

                //订单状态是已通过(不含试用报告) 跳转订单号填写页面
              }
            } else {
              //发起后端请求 放弃试用资格

              trialOrderFactory.set_close_order_sn(order_id, userid, random)
            }
          })
      }

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })
    }
  ])

  /*
    name  购物返利详情页 .....
   */

  .controller('rebate_show', [
    '$rootScope',
    '$scope',
    '$ionicPopup',
    '$state',
    '$ionicLoading',
    '$ionicModal',
    '$stateParams',
    '$filter',
    'StorageFactory',
    'UserProfileFactory',
    'rebate_showFactory',
    'configFactory',
    'trialOrderFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $ionicPopup,
      $state,
      $ionicLoading,
      $ionicModal,
      $stateParams,
      $filter,
      StorageFactory,
      UserProfileFactory,
      rebate_showFactory,
      configFactory,
      trialOrderFactory,
      ENV
    ) {
      //获得传过来的商品id
      var aid = $stateParams.id

      // 获取商品详情
      rebate_showFactory.get_rebate_show(aid)

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'

        // 获取会员参与该活动情况
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.lnvolved_status = 0 //会员参与活动状态
          $scope.login_status = 0 //会员登录状态
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid

          //获取用户签名
          random = StorageFactory.get('user').data.random

          //获取用户支付宝绑定状态
          $scope.allpay_status = StorageFactory.get('profile').alipay_status

          //获取用户邮箱认证状态
          $scope.emall_status = StorageFactory.get('profile').email_status

          //获取用户实名认证状态
          $scope.name_status = StorageFactory.get('profile').name_status

          // 获取用户手机认证状态
          $scope.phone_status = StorageFactory.get('profile').phone_status

          //获取用户绑定的淘宝帐号数量
          UserProfileFactory.set_username_taobao(userid, random)

          //获取会员是否参与过本次活动
          //获取会员是否参与过本次活动
          trialOrderFactory.set_order_is_join(aid, userid, random)

          $scope.lnvolved_status = 1
          $scope.login_status = 1 //会员登录状态
        }
      })

      $scope.fenxiang = function() {
        var title = $scope.showdata.title
        var content =
          '[超级返利]' + $scope.showdata.title + '限量份数' + $scope.showdata.goods_number + '快来免费申请吧！'
        var url = ENV.siteUrl + $scope.showdata.url
        var imageurl = $filter('imgUrl')($scope.showdata.thumb)
        window.plugins.Baidushare.bdshare(
          title,
          content,
          url,
          imageurl,
          function(success) {
            if (success == 1) {
              $ionicLoading.show({
                noBackdrop: true,
                template: '分享成功！',
                duration: 1000
              })
            } else if (success == 2) {
            } else {
            }
          },
          function(fail) {
            alert('encoding failed: ' + fail)
          }
        )
      }

      $scope.Application_reasons = ''
      $scope.Switch = 1
      $scope.fl_showloading = true
      $scope.data_time = Math.round(new Date().getTime() / 1000)

      //获取返利商品的抢购人数
      $scope.good_user_list = function() {
        $scope.user_list_showloading = true
        $scope.Switch = 2

        rebate_showFactory.set_good_user_list(aid, 1)
      }

      //获取返利晒单用户列表
      $scope.rebate_shai_lists = function() {
        $scope.Switch = 3
        $scope.mjsd_showloading = true
        rebate_showFactory.set_shai_report_lists(aid)
      }

      //活动详情
      $scope.goods_body = function() {
        $scope.Switch = 1
      }

      // 定义选择淘宝号 和申请理由数组   答案下单的时候 的问题和答案
      var vm = ($scope.vm = {})

      //后台请求获取后台的活动配置
      configFactory.set_rebate_config()

      //接收配置请求通知
      $scope.$on('configFactory.set_rebate_config', function() {
        //alert("配置通知来了");

        var rebate_config = configFactory.get_rebate_config() //网站试用配置

        //console.log(rebate_config);

        $scope.bind_alipay = rebate_config.data.buyer_join_condition.bind_alipay //绑定支付宝

        $scope.bind_taobao = rebate_config.data.buyer_join_condition.bind_taobao //绑定淘宝

        $scope.bind_email = rebate_config.data.buyer_join_condition.email //绑定邮箱

        $scope.information = rebate_config.data.buyer_join_condition.information //完善个人资料 客户端判断废弃

        $scope.bind_phone = rebate_config.data.buyer_join_condition.phone //绑定手机

        $scope.realname = rebate_config.data.buyer_join_condition.realname // 实名认证
      })

      //接收用户是否参与过抢购通知
      $scope.$on('trialOrderFactory.set_order_is_join', function() {
        $scope.order_is_join = trialOrderFactory.get_order_is_join()

        // $scope.report_list_showloading = false;
      })

      $(document).on('click', '.i_rebateDetail .s_part6 .part', function() {
        $('.i_rebateDetail .s_part6 .part').attr('class', 'part')
        $(this).attr('class', 'part sel')
      })

      //接收商品详情通知

      $scope.$on('NewsContent.rebateshow', function() {
        $scope.showdata = rebate_showFactory.getshow()
        $scope.fl_showloading = false
        //console.log($scope.showdata);
      })

      //接收买家晒单列表通知

      $scope.$on('rebate_showFactory.set_shai_report_lists', function() {
        $scope.shai_lists = rebate_showFactory.get_shai_report_lists()
        $scope.mjsd_showloading = false
      })

      //接收参与用户返回列表通知

      $scope.$on('rebate_showFactory.set_good_user_list', function() {
        $scope.user_list = rebate_showFactory.get_good_user_list()
        $scope.user_list_showloading = false

        //console.log($scope.user_list);
      })

      //普通用户申请试用  如果用户已经参与过本次活动，则不能再次申请
      //活动商品状态   -3 待审核待付款 -2 待审核已付款 -1 待上线 1活动进行中 2.活动结算 3. 已完成  4.撤销 5.屏蔽

      /*--------------普通申请------------------------
        -1 如果用户未登录，先跳转至会员登录页面

        0.如果当前活动状态不为进行中，拒绝申请。
        1.检测用户是否被商家拉入黑名单                      ---------------
        2.再次判断用户参与活动状态
        2.1 判断后台设置 需完善基本资料   需完成手机认证   需完成邮箱认证   需完成实名认证   需绑定淘宝账号   需绑定支付宝账号  需申请理由
        2.2 判断下单类型 如果是答案下单 则跳转输入答案页面

        3.判断后台设置，是否允许同一用户申请一个商家下的多次试用 ----------
        4.如果后台开启了让用户选择淘宝账号，则弹出让用户选择淘宝帐号，如果未绑定，则先跳转绑定页面
        5.如果后台开启了多商家说点什么，则同时跟淘宝帐号一起显示
        6.提交后台申请，等待返回结果。提交参数 userid 商品id 签名 

    */
      $scope.Panic_buying = function() {
        //未登录去登录
        if ($scope.login_status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，请先登录后再申请！',
            duration: 1500
          })

          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        //活动状态不对 拒绝申请
        if ($scope.showdata.status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，当前活动未开始或已结束！',
            duration: 1500
          })

          return false
        }

        //判断是否参与过本活动
        if ($scope.lnvolved_status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您已参与过本次活动,不能再次参与！',
            duration: 1500
          })

          return false
        }

        //判断后台配置开启的参与活动条件 根据配置提示用户完善

        if (
          ($scope.bind_alipay == 5 && $scope.allpay_status != 1) ||
          ($scope.realname == 3 && $scope.name_status != 1) ||
          ($scope.bind_email == 2 && $scope.emall_status != 1) ||
          ($scope.bind_phone == 1 && $scope.phone_status != 1)
        ) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.alert({
            title: '参与条件',
            templateUrl: 'a.html',
            scope: $scope
          })
          alertPopup.then(function(res) {
            //console.log('Thank you for not eating my delicious ice cream cone');
          })
          return false
        }

        //  //console.log($scope.showdata);

        //判断是普通抢购还是答案抢购
        if ($scope.showdata.type != undefined && $scope.showdata.type == 'ask') {
          var alertPopup = $ionicPopup.show({
            title: '请先回答商家问题',
            templateUrl: 'c.html',
            scope: $scope,
            buttons: [
              {
                text: '取消'
              },
              {
                text: 'OK',
                type: 'button-assertive',
                onTap: function(e) {
                  //console.log($scope.vm);

                  if (!$scope.vm.Answer) {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲，答案不能为空',
                      duration: 1500
                    })

                    return false
                  } else if ($scope.vm.Answer == $scope.showdata.goods_rule.ask.answer) {
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,回答正确！',
                      duration: 1000
                    })

                    $scope.Submit_rebate()
                  } else {
                    e.preventDefault()
                    $ionicLoading.show({
                      noBackdrop: true,
                      template: '亲,答案错误,请仔细找找！',
                      duration: 1500
                    })

                    return false
                  }
                }
              }
            ]
          })
          alertPopup.then(function(res) {})

          return false
        }

        //都未开启 提交后台抢购
        $scope.Submit_rebate()
      }

      //提交后台抢购返利
      $scope.Submit_rebate = function(reason) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '亲，正在为您提交申请！',
          duration: 1500
        })

        trialOrderFactory.set_apply_rebate_order(aid, userid, random)
      }

      //接收抢购结果返回通知

      $scope.$on('trialOrderFactory.set_apply_rebate_order', function() {
        var data_get_apply_order = trialOrderFactory.get_apply_rebate()

        //console.log(data_get_apply_order);
        if (data_get_apply_order.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_get_apply_order.msg,
            duration: 1500
          })

          return false
        } else if (data_get_apply_order.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '恭喜您!抢购成功, 请在指定时间内按照活动要求下单！',
            duration: 3000
          })

          //跳转订单号填写页面
          $state.go('tab.rebate_order_id', {
            id: data_get_apply_order.data,
            goodid: aid
          })
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '程序员哥哥，正在抢修',
            duration: 1500
          })
        }
      })
    }
  ])

  /*
    name  购物返利填写订单号 晒单
   */

  .controller('rebate_order_add', [
    '$rootScope',
    '$interval',
    '$filter',
    '$location',
    '$scope',
    '$state',
    '$stateParams',
    '$ionicPopup',
    '$ionicModal',
    '$ionicLoading',
    'ENV',
    'StorageFactory',
    'rebate_showFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'configFactory',
    'uploadFactory',
    'ENV',
    'Upload',
    function(
      $rootScope,
      $interval,
      $filter,
      $location,
      $scope,
      $state,
      $stateParams,
      $ionicPopup,
      $ionicModal,
      $ionicLoading,
      ENV,
      StorageFactory,
      rebate_showFactory,
      UserProfileFactory,
      trialOrderFactory,
      configFactory,
      uploadFactory,
      ENV,
      Upload
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $rootScope.hideTabs = 'tabs-item-hide'

        //获得传过来的商品id
        aid = $stateParams.goodid
        //获得传过来的订单id
        order_id = $stateParams.id

        // 获取用户id
        userid = StorageFactory.get('user').data.userid

        //获取用户签名
        random = StorageFactory.get('user').data.random

        trialOrderFactory.set_order_info(order_id, userid, random)
      })

      //获得传过来的商品id
      var aid = $stateParams.goodid
      //获得传过来的订单id
      var order_id = $stateParams.id

      // 第一步 获取这个商品详情
      rebate_showFactory.get_rebate_show(aid)

      $scope.ENV = ENV

      $scope.txddh_showloading = true

      //获取后台试用活动设置
      configFactory.set_rebate_config()

      //接收配置请求通知
      $scope.$on('configFactory.set_rebate_config', function() {
        var rebate_config = configFactory.get_rebate_config() //网站返利活动配置

        //填写订单号时间限制
        //console.log(rebate_config);

        $scope.buyer_write_order_time = rebate_config.data.buyer_write_order_time * 60

        //商家审核总时间天数
        $scope.seller_check_time = rebate_config.data.seller_check_time * 86400

        //修改订单号时间限制
        $scope.buyer_check_update_order_sn = rebate_config.data.buyer_check_update_order_sn * 3600
      })

      // 定义用户填写订单号数组
      $scope.order_vm = {
        mum: ''
      }

      //定义用户填写试用报告数组
      $scope.sybg_vm = {
        pinfen: '',
        img: 'img/shai_img.jpg',
        xinde: ''
      }

      //晒单分享
      $scope.The_sun = function() {
        if ($scope.sybg_vm.img == 'img/shai_img.jpg') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请分享一下宝贝图片...',
            duration: 1000
          })

          return false
        }

        if ($scope.sybg_vm.xinde == '') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请分享一下购物心得...',
            duration: 1000
          })

          return false
        }

        $ionicLoading.show({
          noBackdrop: true,
          template: '亲,正在提交...',
          duration: 1000
        })

        //后端提交晒单内容
        trialOrderFactory.set_shai_report(order_id, $scope.sybg_vm.img, $scope.sybg_vm.xinde, userid, random)
      }

      //接收晒单返回结果
      $scope.$on('trialOrderFactory.set_shai_report', function() {
        var shai_data = trialOrderFactory.get_shai_report()

        if (shai_data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: shai_data.msg,
            duration: 1000
          })

          //跳转返回我的订单页面
          $state.go('tab.rebate_order')

          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: shai_data.msg,
            duration: 1000
          })
        }
      })

      $scope.pingfen = [
        {
          name: '★完全不满意,比较失望',
          id: 1
        },
        {
          name: '★★非常不满意,只是感谢提供',
          id: 3
        },
        {
          name: '★★★一般满意,还有待提高',
          id: 3
        },
        {
          name: '★★★★比较满意,离完美差点',
          id: 4
        },
        {
          name: '★★★★★完全满意,太喜欢这款',
          id: 5
        }
      ]

      $scope.$on('NewsContent.rebateshow', function() {
        $scope.txddh_showdata = rebate_showFactory.getshow()
        $scope.txddh_showloading = false

        //console.log($scope.txddh_showdata);
        //如果是搜索下单，随机取一个搜索关系词
        if ($scope.txddh_showdata.type == 'search') {
          var keyword_string = $scope.txddh_showdata.goods_rule.keyword
          var keyword_string = keyword_string.replace(/，/g, ',') //增强用户体验，替换，
          var keyword_obj2 = keyword_string.split(',') //字符串转化为数组
          //console.log(keyword_obj2);
          $scope.goods_keyword = keyword_obj2[Math.floor(Math.random() * keyword_obj2.length)] //随机取得一个字符
        }
      })

      //第二步 获取这个订单详细信息

      //接收订单详细信息通知
      $scope.$on('trialOrderFactory.set_order_info', function() {
        $scope.order_info = trialOrderFactory.get_order_info()
      })

      $scope.add_order_number = function() {
        var alertPopup = $ionicPopup.show({
          title: '填写订单号',
          templateUrl: 'a.html',
          scope: $scope,
          buttons: [
            {
              text: '取消'
            },
            {
              text: '确定',
              type: 'button-assertive',
              onTap: function(e) {
                var patten = /^\d{9,20}$/
                if (!patten.test($scope.order_vm.mum)) {
                  e.preventDefault()
                  $ionicLoading.show({
                    noBackdrop: true,
                    template: '亲,订单号是9位以上数字！',
                    duration: 1000
                  })
                  return false
                }

                //发起后端请求 填写订单号
                trialOrderFactory.set_fill_order_sn(order_id, $scope.order_vm.mum, userid, random)
              }
            }
          ]
        })
      }

      //接收填写订单号返回结果通知
      $scope.$on('trialOrderFactory.set_fill_order_sn', function() {
        var data_fill_order_sn = trialOrderFactory.get_fill_order_sn()

        if (data_fill_order_sn.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_fill_order_sn.msg,
            duration: 1000
          })

          //跳转返回我的订单页面
          $state.go('tab.rebate_order')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_fill_order_sn.msg,
            duration: 1000
          })
        }
      })

      //点击去下单 普通下单直接跳转至淘宝,搜索下单,直接跳转淘宝引导页面
      $scope.To_order = function(type, source, url) {
        // //console.log($filter('f_xiaoshu' )($scope.txddh_showdata.goods_price - ($scope.txddh_showdata.goods_price * $scope.txddh_showdata.goods_discount /10 ) ));

        source = $filter('f_source')(source)
        var go_tiele = source + '下单'

        if (type == 'search') {
          go_tiele = source + '搜索下单'
          url = getDomain(url)
          //提取搜索下单地址
          function getDomain(weburl) {
            var urlReg = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/i
            domain = weburl.match(urlReg)
            domain = domain[1] + 'm.' + domain[4] + domain[5]
            return domain != null && domain.length > 0 ? domain : ''
          }
          window.open(url)
        }

        if (type == 'qrcode') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请扫描页面二维码下单',
            duration: 2000
          })

          return false
        }

        if (type == 'general' || type == 'ask') {
          //普通下单
          window.open(url)
        }
      }

      //我要申诉
      // 如果状态不等于4 拒绝申诉
      $scope.Appeal = function(status, order_id, goods_id) {
        //console.log(status, order_id, goods_id);
        if (status != 4) return false
        //跳转申诉页面 弹窗还是新页面纠结啊

        //跳转订单申诉页面
        $state.go('tab.order_appeal', {
          id: order_id,
          aid: goods_id
        })
      }

      //获取订单日志记录！
      $scope.Order_log = function() {
        //console.log(order_id);

        $state.go('tab.order_log', {
          id: order_id
        })
      }

      $scope.imgurl1 = function(index) {
        return $('#File1').click()
      }

      $scope.onFileSelect = function($files) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '正在上传 请稍后...'
        })

        uploadFactory.set_upload($files)
      }

      $scope.$on('uploadFactory.set_upload', function() {
        var data = uploadFactory.get_upload()
        if (data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传完成',
            duration: 1000
          })
          $scope.sybg_vm.img = ENV.imgUrl + data.url
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '图片上传失败',
            duration: 1000
          })
        }
      })

      //第三步 判断下单类型 是普通下单 还是答案下单 二维码下单

      //接收订单返回通知
      $scope.$on('trialOrderFactory.set_getorderlists', function() {
        $scope.wdsy_showloading = false

        $scope.trial_getorderlists = trialOrderFactory.get_getorderlists()

        //console.log($scope.trial_getorderlists);
      })
    }
  ])

  //我的日赚任务会员中心
  .controller('task_order', [
    '$scope',
    '$state',
    '$ionicModal',
    '$ionicLoading',
    'StorageFactory',
    'TaskFactory',
    function($scope, $state, $ionicModal, $ionicLoading, StorageFactory, TaskFactory) {
      //如果不存在会员信息 则跳转登陆页面
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        $state.go('tab.user_login') //路由跳转登录
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      $scope.task_showloading = true

      //获取我的日赚任务记录
      TaskFactory.set_user_borke(userid, random)

      //接收通知
      $scope.$on('TaskFactory.set_user_borke', function() {
        $scope.data_user_borke = TaskFactory.get_user_borke()
        $scope.task_showloading = false
        //console.log($scope.data_user_borke);
      })
    }
  ])

  //日赚任务列表
  .controller('task_list', [
    '$rootScope',
    '$scope',
    '$state',
    '$ionicModal',
    '$ionicLoading',
    'StorageFactory',
    'UserProfileFactory',
    'configFactory',
    'TaskFactory',
    function(
      $rootScope,
      $scope,
      $state,
      $ionicModal,
      $ionicLoading,
      StorageFactory,
      UserProfileFactory,
      configFactory,
      TaskFactory
    ) {
      //如果不存在会员信息 则跳转登陆页面
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
      } else {
        // 获取用户id
        var userid = StorageFactory.get('user').data.userid

        //获取用户签名
        var random = StorageFactory.get('user').data.random
      }

      $scope.$on('UserDepositeFactory.set_getusercashlog', function() {
        $scope.answer_task_data = configFactory.get_answer_task()
      })

      //接收通知
      $scope.$on('TaskFactory.set_user_borke', function() {
        $scope.data_user_borke = TaskFactory.get_user_borke()

        //console.log($scope.data_user_borke);
      })

      $scope.task_hasNextPage = false

      //获取我的日赚任务记录

      var ordrby = 'id'
      TaskFactory.set_broke_list(ordrby)

      $scope.$on('TaskFactory.set_broke_list', function() {
        $scope.broke_list = TaskFactory.get_broke_list()

        //console.log($scope.broke_list);

        $scope.task_showloading = false
        $scope.$broadcast('scroll.infiniteScrollComplete')
        $scope.task_hasNextPage = TaskFactory.task_hasNextPage()
      })

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      //获取是否有下一页
      $scope.task_hasNextPage = function() {
        return TaskFactory.task_hasNextPage()
      }

      //下拉更新
      $scope.task_doRefresh = function() {
        TaskFactory.set_broke_list(ordrby)
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载
      $scope.task_loadMore = function() {
        TaskFactory.task_loadMore(ordrby)
      }

      //切换tab显示排序 时间 佣金
      $scope.task_changeTab = function(orby) {
        ordrby = orby
        $scope.task_showloading = true
        //数据请求
        TaskFactory.set_broke_list(ordrby)
      }

      $('.s_ti a').bind('click', function() {
        $('.s_ti a').attr('class', '')
        $(this).attr('class', 'sel')
      })
    }
  ])

  //我的日赚任务详情页
  .controller('task_show', [
    '$rootScope',
    '$scope',
    '$stateParams',
    '$state',
    '$ionicModal',
    '$filter',
    '$ionicLoading',
    'StorageFactory',
    'TaskFactory',
    'UserProfileFactory',
    'configFactory',
    'trialOrderFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $stateParams,
      $state,
      $ionicModal,
      $filter,
      $ionicLoading,
      StorageFactory,
      TaskFactory,
      UserProfileFactory,
      configFactory,
      trialOrderFactory,
      ENV
    ) {
      //如果不存在会员信息 则跳转登陆页面
      // 获得传过来的日赚任务id
      var id = $stateParams.id
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      $scope.task_show_hasNextPage = true
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        //$state.go('tab.user_login'); //路由跳转登录
        $scope.login_status = 0
      } else {
        // 获取用户id
        var userid = StorageFactory.get('user').data.userid
        //获取用户签名
        var random = StorageFactory.get('user').data.random
        //获取当前会员是否参与过当前日赚任务
        trialOrderFactory.set_is_join_borke(id, userid, random)
        $scope.login_status = 1

        // 获取用户手机认证状态
        $scope.phone_status = StorageFactory.get('profile').phone_status
      }

      // 获取日赚任务详情
      TaskFactory.set_broke_show(id)

      //获得后台日赚任务配置
      configFactory.set_answer_task()

      //接收日赚任务通知
      $scope.$on('UserDepositeFactory.set_getusercashlog', function() {
        $scope.answer_task_data = configFactory.get_answer_task()
      })

      //接收日赚详情通知
      $scope.$on('TaskFactory.set_broke_show', function() {
        $scope.task_show = TaskFactory.get_broke_show()
        $scope.task_show_hasNextPage = false
      })

      //接收是否参与过后台回馈通知
      $scope.$on('trialOrderFactory.set_is_join_borke', function() {
        $scope.is_join_borke = trialOrderFactory.get_is_join_borke()
        //console.log( $scope.is_join_borke);
      })

      //接收填写答案反馈结果
      $scope.$on('trialOrderFactory.set_answer_task', function() {
        $scope.data_answer_task = trialOrderFactory.get_answer_task()
        //console.log($scope.data_answer_task);

        if ($scope.data_answer_task.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: $scope.data_answer_task.msg,
            duration: 2000
          })

          return false
        } else if ($scope.data_answer_task.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template:
              '恭喜您,回答正确,获得本次任务现金奖励' +
              $scope.task_show.goods_price +
              '元，奖励已实时发送至您账户当中！',
            duration: 2000
          })

          //设置已状态
          $scope.is_join_borke.data = 1
        }
      })

      //日赚任务分享

      $scope.fenxiang = function() {
        var title = '[回答就有钱]' + $scope.task_show.title
        var content = '[做任务赚钱]' + $scope.task_show.title + '快来参与吧！'
        var url = ENV.siteUrl + '/broke/'
        var imageurl = $filter('imgUrl')($scope.task_show.thumb)

        //   console.log(title,content,url,imageurl);
        window.plugins.Baidushare.bdshare(
          title,
          content,
          url,
          imageurl,
          function(success) {
            if (success == 1) {
              $ionicLoading.show({
                noBackdrop: true,
                template: '分享成功！',
                duration: 1000
              })
            } else if (success == 2) {
            } else {
            }
          },
          function(fail) {
            alert('encoding failed: ' + fail)
          }
        )
      }

      $scope.add_daan = function(daan) {
        if ($scope.login_status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请先登录后,在申请',
            duration: 2000
          })

          return false
        }

        if ($scope.is_join_borke.data == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您已参与过一次了,请把机会留给其它人！',
            duration: 2000
          })

          //设置参与状态为1
          $scope.is_join_borke.data = 1
          return false
        }

        if (!daan) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请填写答案,答案不能为空！',
            duration: 2000
          })
          return false
        }

        /*       if((/[@#\$%\^&\*]+/g.test(daan)) ){

        $ionicLoading.show({
          noBackdrop: true,
          template: "亲,答案不能包含特殊字符！",
          duration: 2000
        });
         return false;

       }*/

        trialOrderFactory.set_answer_task(id, daan, userid, random)
      }

      $scope.zhao_daan = function(source) {
        if ($scope.task_show.goods_url == null || $scope.task_show.goods_url == '') {
          if (source == 1) {
            window.location.href = 'http://m.taobao.com'
          } else if (source == 2) {
            window.location.href = 'http://m.tmall.com'
          } else if (source == 3) {
            window.location.href = 'http://m.jd.com'
          } else {
            $ionicLoading.show({
              noBackdrop: true,
              template: '请按照搜索要求下单',
              duration: 2000
            })

            return false
          }
        } else {
          window.location.href = $scope.task_show.goods_url
          return false
        }
      }
    }
  ])

  //积分商城
  .controller('integral_list', [
    '$rootScope',
    '$scope',
    '$ionicLoading',
    'StorageFactory',
    'integralFactory',
    function($rootScope, $scope, $ionicLoading, StorageFactory, integralFactory) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      $scope.shop_list_showloading = true

      //获取积分兑换商品
      integralFactory.set_shop_lists()

      //下拉更新
      $scope.shop_doRefresh = function() {
        integralFactory.set_shop_lists()
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      $scope.$on('integralFactory.set_shop_lists', function() {
        $scope.shop_list = integralFactory.get_shop_list()
        $scope.$broadcast('scroll.infiniteScrollComplete')
        $scope.shop_hasNextPage = integralFactory.shop_hasNextPage()
        $scope.shop_list_showloading = false
        //console.log($scope.shop_list);
      })

      //上拉加载更多

      $scope.shop_loadMore = function() {
        integralFactory.shop_loadMore()
      }
    }
  ])

  //积分兑换详情页面
  .controller('integral_show', [
    '$rootScope',
    '$scope',
    '$ionicPopup',
    '$state',
    '$stateParams',
    '$ionicLoading',
    'trialOrderFactory',
    'StorageFactory',
    'integralFactory',
    'UserProfileFactory',
    'UserPersonalFactory',
    function(
      $rootScope,
      $scope,
      $ionicPopup,
      $state,
      $stateParams,
      $ionicLoading,
      trialOrderFactory,
      StorageFactory,
      integralFactory,
      UserProfileFactory,
      UserPersonalFactory
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })
      var id = $stateParams.id
      $scope.shop_show_showloading = true

      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        //$state.go('tab.user_login'); //路由跳转登录
        $scope.login_status = 0
      } else {
        // 获取用户id
        var userid = StorageFactory.get('user').data.userid
        //获取用户签名
        var random = StorageFactory.get('user').data.random
        //获取当前会员是否已经兑换过
        trialOrderFactory.set_shop_exchange(id, userid, random)

        //获得当前会员的用户积分
        $scope.user_point = StorageFactory.get('profile').point

        // 获取用户当前收货地址
        UserProfileFactory.set_username_Profile_address(userid, random)

        //获取用户手机号
        $scope.user_phone = StorageFactory.get('profile').phone

        $scope.login_status = 1
      }

      $('.i_PointsDetail .s_part6 .R span').live('click', function() {
        $('.i_PointsDetail .s_part6 .R span').attr('class', '')
        $(this).attr('class', 'sel')
      })

      $scope.spec = function(spec) {
        shop_spec = spec
      }

      //获得当前系统的时间
      $scope.getTime = Math.round(new Date().getTime() / 1000)

      //获取商品详情
      integralFactory.set_shop_show(id)

      $scope.$on('integralFactory.set_shop_show', function() {
        $scope.shop_show = integralFactory.get_shop_show()

        var keyword_string = $scope.shop_show.spec
        var keyword_string = keyword_string.replace(/，/g, ',') //增强用户体验，替换，
        var keyword_string = keyword_string.replace(/ /g, ',') //增强用户体验，替换空格
        $scope.keyword_obj2 = keyword_string.split(',') //字符串转化为数组

        //对商品尺寸进行格式化分组
        //console.log($scope.keyword_obj2);
        //console.log($scope.shop_show);

        $scope.shop_show_showloading = false

        shop_spec = $scope.keyword_obj2[0]
      })

      //接收是否参与过后台回馈通知
      $scope.$on('trialOrderFactory.set_shop_exchange', function() {
        $scope.is_join_shop = trialOrderFactory.get_is_join_borke()
      })

      $scope.$on('UserProfileFactory.set_username_Profile_address', function() {
        var data_address = UserProfileFactory.get_username_Profile_address()
        $scope.data_address = data_address.data
        var address = data_address.data
        console.log(data_address)

        $scope.provice_id = address.provice //已选省id
        $scope.city_id = address.area //已选市id

        console.log($scope.provice_id)
      })

      //我的积分兑换
      $scope.user_order_jifen = function() {
        if ($scope.logo_status != 1) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.user_jifen_order')
      }

      $scope.showConfirm = function() {}

      $scope.Exchange = function() {
        //1.判断会员是否登录
        // 2.判断会员是否参与
        // 2.判断会员积分是否充足
        // 4.提交后台兑换

        if ($scope.login_status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,您还未登录,请先登录后再兑换',
            duration: 2000
          })

          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        if ($scope.is_join_shop.data == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,您已经兑换过本商品,同一商品只能兑换一次！',
            duration: 2000
          })

          return false
        }

        if ($scope.user_point < $scope.shop_show.point - 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template:
              '亲,您的积分不足，目前账户剩余积分 ' +
              $scope.user_point +
              '兑换所需积分 ' +
              $scope.shop_show.point +
              ' 多参与积分任务,每日签到可获得更多积分！',
            duration: 2000
          })

          return false
        }

        if ($scope.provice_id == '' || $scope.provice_id == null) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲, 请先完善收货地址，方便收货',
            duration: 2000
          })

          $state.go('tab.user_profile_address')
          return false
        }

        if ($scope.user_phone == '') {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲, 请先进行手机认证之后才能兑换',
            duration: 2000
          })

          $state.go('tab.user_profile_phone')
          return false
        }

        var confirmPopup = $ionicPopup.confirm({
          title: '积分兑换确认',
          template:
            '您确定花费 <b class="cc" >' +
            $scope.shop_show.point +
            " 积分</b>?<br/> ,兑换本商品,<br/>账户目前积分余额 <b class='cc' >" +
            $scope.user_point +
            ' </b>积分',
          cancelText: '放弃兑换',
          cancelType: '',
          okText: '确定兑换',
          okType: 'button-assertive button-outline'
        })
        confirmPopup.then(function(res) {
          if (res) {
            //发起后台兑换
            trialOrderFactory.set_exchange(id, shop_spec, userid, random)
          } else {
          }
        })
      }

      //接收兑换成功结果通知
      $scope.$on('trialOrderFactory.set_exchange', function() {
        $scope.exchange_data = trialOrderFactory.get_exchange()

        //console.log($scope.exchange_data);
        if ($scope.exchange_data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '恭喜您,花费积分 ' + $scope.shop_show.point + '成功兑换了喜欢的商品',
            duration: 2000
          })

          //设置兑换状态为1
          $scope.is_join_shop.data = 1

          //重新获取用户积分
          UserPersonalFactory.set_userinfo(userid, random)
          $scope.user_point = $scope.user_point - $scope.shop_show.point
        } else if ($scope.exchange_data.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: $scope.exchange_data.msg,
            duration: 2000
          })

          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '程序员哥哥正在维护',
            duration: 2000
          })

          return false
        }
      })

      $scope.$on('UserPersonal.set_userinfo', function() {
        $scope.userInfo = UserPersonalFactory.get_userinfo()
        if ($scope.userInfo.status == 1) {
          //将用户信息写入本地缓存
          StorageFactory.set('profile', $scope.userInfo)
        }
      })
    }
  ])

  //我的积分兑换
  .controller('jifen_order', [
    '$scope',
    '$state',
    '$ionicModal',
    '$ionicLoading',
    'StorageFactory',
    'TaskFactory',
    'trialOrderFactory',
    function($scope, $state, $ionicModal, $ionicLoading, StorageFactory, TaskFactory, trialOrderFactory) {
      //如果不存在会员信息 则跳转登陆页面
      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        $state.go('tab.user_login') //路由跳转登录
        return false
      }

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      $scope.shop_log_showloading = true

      //获取我的积分兑换记录
      trialOrderFactory.set_shop_log(userid, random, 0)

      //接收积分兑换记录通知
      $scope.$on('trialOrderFactory.set_shop_log', function() {
        $scope.shop_log = trialOrderFactory.get_shop_log()
        $scope.shop_log_showloading = false

        //console.log($scope.shop_log);
      })

      $scope.jifen_order_changeTab = function(status) {
        $scope.shop_log_showloading = true

        trialOrderFactory.set_shop_log(userid, random, status)
      }

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })
    }
  ])

  //推荐好友
  .controller('invitation', [
    '$rootScope',
    '$scope',
    '$state',
    '$ionicLoading',
    'StorageFactory',
    'configFactory',
    'UserProfileFactory',
    'invitationFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $state,
      $ionicLoading,
      StorageFactory,
      configFactory,
      UserProfileFactory,
      invitationFactory,
      ENV
    ) {
      //如果不存在会员信息 则跳转登陆页面

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.login_status = 0

          url = ''
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid

          url = ENV.siteUrl + '/index.php?m=friends&c=index&a=index&userid=' + userid

          //获取用户签名
          random = StorageFactory.get('user').data.random

          //获得会员的邀请好友记录

          $scope.login_status = 1

          //获得会员已获得邀请好友的总金额
          invitationFactory.set_recommend_total_moeny(userid, random)
        }
      })

      //分享推荐的链接给好友

      //获得会员的邀请码
      //获得会员的邀请好友记录

      //获得邀请好友的排行榜
      invitationFactory.set_order_by_friend()

      //获得后台设置的邀请好友奖励规则和活动说明
      configFactory.set_friends_config()

      //获得后台配置通知
      $scope.$on('configFactory.set_friends_config', function() {
        $scope.config_data = configFactory.get_friends_config()

        //console.log($scope.config_data);
      })

      //获得奖励累计通知
      $scope.$on('invitationFactory.set_recommend_total_moeny', function() {
        var total_moeny = invitationFactory.get_recommend_total_moeny()

        console.log(total_moeny)

        $scope.total_moeny = total_moeny.data[0].num ? total_moeny.data[0].num : 0

        $scope.user_num = total_moeny.data.us_num

        //console.log($scope.total_moeny );
      })

      $scope.set_invitation_log = function() {
        if ($scope.login_status == 0) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        $state.go('tab.invitation_log')
        $scope.invitation_log_showloading = true
        //请求后台的推荐好友记录
        invitationFactory.set_recommend_friend(userid, random)
      }

      //获得推荐好友记录通知
      $scope.$on('invitationFactory.set_recommend_friend', function() {
        $scope.recommend_friend = invitationFactory.get_recommend_friend()

        $scope.invitation_log_showloading = false
        //console.log($scope.recommend_friend);
      })

      //获得邀请好友排行榜
      $scope.$on('invitationFactory.set_order_by_friend', function() {
        $scope.user_paihang = invitationFactory.get_order_by_friend()

        ////console.log($scope.user_paihang);
      })

      $scope.$on('$ionicView.afterEnter', function() {
        var title = '好友强力推荐 快来看'
        var content = '哈，发现一个免费试用商品的网站，还能赚红包，我已经申请了好多东西，真的很不错哦。快去看看吧！'
        var imageurl = ENV.siteUrl + '/uploadfile/apk/logo.png'

        // var url = url != '' ? url :'';

        console.log(url)

        window._bd_share_config = {
          common: {
            bdText: content,
            bdDesc: title,
            bdUrl: url,
            bdPic: imageurl
          },
          share: [
            {
              bdSize: 20
            }
          ],
          slide: [
            {
              bdImg: 0,
              bdPos: 'right',
              bdTop: 100
            }
          ],
          image: [
            {
              viewType: 'list',
              viewPos: 'top',
              viewColor: 'black',
              viewSize: '32',
              viewList: ['qzone', 'tsina', 'huaban', 'tqq', 'renren']
            }
          ],
          selectShare: [
            {
              bdselectMiniList: ['qzone', 'tqq', 'kaixin001', 'bdxc', 'copy']
            }
          ]
        }
        with (document)
          (0)[
            ((getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src =
              'http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion=' + ~(-new Date() / 36e5))
          ]
      })

      //弹出分享网址

      $scope.arturl = function() {
        var name = prompt('我的分享链接 将此链接复制发给好友', url) //将输入的内容赋给变量 name
      }
    }
  ])

  //更多

  .controller('duo', [
    '$rootScope',
    '$scope',
    'ENV',
    function($rootScope, $scope, ENV) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = ''
      })
    }
  ])

  //用于网站帮助指引！

  .controller('help', [
    '$rootScope',
    '$scope',
    function($rootScope, $scope) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      //获取后台闪电试用活动设置.
    }
  ])

  //幸运大转盘
  .controller('jiang', [
    '$rootScope',
    '$state',
    '$timeout',
    '$ionicPopup',
    '$interval',
    '$scope',
    'configFactory',
    'jiangFactory',
    'StorageFactory',
    function(
      $rootScope,
      $state,
      $timeout,
      $ionicPopup,
      $interval,
      $scope,
      configFactory,
      jiangFactory,
      StorageFactory
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'

        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.login_status = 0
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid

          //获取用户签名
          random = StorageFactory.get('user').data.random

          //获得会员的抽奖总数
          jiangFactory.set_user_reward_cishu(userid, random)

          $scope.login_status = 1
        }
      })

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      $scope.$on('jiangFactory.set_reward_list', function() {
        $scope.reward_list = jiangFactory.get_reward_list()
        //console.log($scope.reward_list);
      })

      //获取后台抽奖配置
      configFactory.set_reward_config()

      $scope.$on('configFactory.set_reward_config', function() {
        $scope.reward_config = configFactory.get_reward_config()
        ////console.log($scope.reward_cishu);
      })

      //接收配置到达通知过来
      $scope.$on('jiangFactory.set_user_reward_cishu', function() {
        $scope.reward_cishu = jiangFactory.get_user_reward_cishu()
        //console.log($scope.reward_cishu);
      })

      //获取最新的中奖记录
      jiangFactory.set_reward_list()

      $scope.$on('jiangFactory.set_reward_list', function() {
        $scope.reward_list = jiangFactory.get_reward_list()
        //console.log($scope.reward_list);
      })

      //获取当前会员的中奖记录

      $scope.$on('jiangFactory.set_user_reward_list', function() {
        $scope.user_reward_list = jiangFactory.get_user_reward_list()
        $scope.jiang_log_showloading = false
        //console.log($scope.reward_list);
      })

      $scope.User_user_reward = function() {
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }
        $state.go('tab.jiang_log')
        $scope.jiang_log_showloading = true

        jiangFactory.set_user_reward_list(userid, random)
      }

      $scope.cishu = 5
      //获取后台奖项设置
      //每日抽奖次数
      //分享之后的抽奖次数
      //获取最新中奖人数

      $scope.rank1 = '现金100元'

      //发起抽奖请求

      //接收微信回调通知 再让用户抽一次

      var ajax_data //ajax查询结果
      var rank = 0
      var r = 0
      $scope.rotate = function(rank) {
        rank = !rank ? 0 : rank
        r = r + 5

        if (rank == 1 && r % 360 > 0 && r % 360 < 30) {
          r = 5
          $('#rotate').attr('style', 'transform: rotate(1deg)')
        }
        if (rank == 2 && r % 360 > 30 && r % 360 < 90) {
          r = 60
        }

        if (rank == 3 && r % 360 > 90 && r % 360 < 150) {
          r = 120
        }

        if (rank == 4 && r % 360 > 150 && r % 360 < 210) {
          r = 180
        }

        if (rank == 5 && r % 360 > 210 && r % 360 < 270) {
          r = 240
        }

        if (rank == 6 && r % 360 > 270 && r % 360 < 330) {
          r = 300
        }

        element = document.getElementById('rotate')
        element.style.MozTransform = 'rotate(' + r + 'deg)'
        element.style.webkitTransform = 'rotate(' + r + 'deg)'
        element.style.msTransform = 'rotate(' + r + 'deg)'
        element.style.OTransform = 'rotate(' + r + 'deg)'
        element.style.transform = 'rotate(' + r + 'deg)'
      }

      $scope.rotate_stop = function() {
        rank = 1
        setTimeout($scope.alert_sring_fun(), 500)
      }

      $scope.alert_sring_fun = function(lottery) {
        var alert_sring
        if (!lottery) {
          alert_sring = '今日抽奖机会已请明日再来！'

          var confirmPopup = $ionicPopup.confirm({
            title: '中奖结果',
            template: alert_sring
          })
          confirmPopup.then(function(res) {
            if (res) {
              //console.log('You are sure');
            } else {
              //console.log('You are not sure');
            }
          })

          return false
        }

        //console.log(lottery.rank);
        if (lottery.rank == 1) {
          alert_sring = '恭喜您获得一等奖'
        }
        if (lottery.rank == 2) {
          alert_sring = '恭喜您获得二等奖'
        }
        if (lottery.rank == 3) {
          alert_sring = '恭喜您获得三等奖'
        }
        if (lottery.rank == 4) {
          alert_sring = '恭喜您获得四等奖'
        }
        if (lottery.rank == 5) {
          alert_sring = '恭喜您获得五等奖'
        }
        if (lottery.rank == 6) {
          alert_sring = '恭喜您获得六等奖'
        }
        var confirmPopup = $ionicPopup.confirm({
          title: '中奖结果',
          okText: '确定', // String (默认: 'OK')。OK按钮的文字。
          okType: 'button-assertive button-outlin button-clear',
          cancelText: '取消',
          cancelType: 'button-assertive button-outlin button-clear',
          template: alert_sring + '奖励 ' + lottery.result
        })
        confirmPopup.then(function(res) {
          if (res) {
            //console.log('You are sure');
          } else {
            //console.log('You are not sure');
          }
        })

        chou_state = 0
      }

      var chou_state = 0 //用户是否正在抽奖

      $scope.ajax = function() {
        if ($scope.login_status == 0) {
          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        if (chou_state != 0) {
          return false
        }

        if ($scope.reward_cishu < 1) {
          return false
        }
        chou_state = 1

        $scope.interval1 = $interval(function() {
          $scope.rotate(0)
        }, 10)
        //发起后台抽奖请求;
        jiangFactory.set_lottery(userid, random)
      }

      $scope.$on('jiangFactory.set_lottery', function() {
        $scope.data_lottery = jiangFactory.get_lottery()

        jiangFactory.set_user_reward_cishu(userid, random)
        //console.log($scope.data_lottery);

        $timeout(function() {
          $interval.cancel($scope.interval1)
          if ($scope.data_lottery.rank == 1) $('#rotate').attr('style', 'transform: rotate(1deg)')
          if ($scope.data_lottery.rank == 2) $('#rotate').attr('style', 'transform: rotate(780deg)')
          if ($scope.data_lottery.rank == 3) $('#rotate').attr('style', 'transform: rotate(840deg)')
          if ($scope.data_lottery.rank == 4) $('#rotate').attr('style', 'transform: rotate(900deg)')
          if ($scope.data_lottery.rank == 5) $('#rotate').attr('style', 'transform: rotate(960deg)')
          if ($scope.data_lottery.rank == 6) $('#rotate').attr('style', 'transform: rotate(1020deg)')
          $scope.alert_sring_fun($scope.data_lottery)
        }, 2000)
      })
    }
  ])

  .controller('commission_list', [
    '$rootScope',
    '$scope',
    'ENV',
    '$timeout',
    '$ionicPopover',
    'categorylistsFactory',
    'commissionFactory',
    '$stateParams',
    '$ionicSideMenuDelegate',
    function(
      $rootScope,
      $scope,
      ENV,
      $timeout,
      $ionicPopover,
      categorylistsFactory,
      commissionFactory,
      $stateParams,
      $ionicSideMenuDelegate
    ) {
      //console.log($stateParams);
      $scope.ENV = ENV

      //隐藏tabs

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      // 显示加载中图标
      $scope.showloading = true

      $scope.commission_hasNextPage = false

      //全部分类展示

      $scope.hide = function() {
        $ionicLoading.hide()
      }

      //请求返利商品列表
      commissionFactory.getTopTopics()

      //接收到刚才传过来的通知
      $scope.$on('commissionFactory.getTopTopics', function() {
        $scope.goods = commissionFactory.getArticles()
        //console.log($scope.goods);
        $scope.showloading = false
        $scope.commission_hasNextPage = commissionFactory.hasNextPage()
        $scope.$broadcast('scroll.infiniteScrollComplete')
      })

      //下拉刷新
      $scope.doRefresh = function() {
        commissionFactory.getTopTopics()

        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载
      $scope.loadMore = function() {
        commissionFactory.getMoreTopics()
      }
    }
  ])

  /*
      name  闪电试用详情页
     */

  .controller('commission_show', [
    '$rootScope',
    '$scope',
    '$ionicPopup',
    '$state',
    '$ionicLoading',
    '$ionicModal',
    '$filter',
    '$stateParams',
    'StorageFactory',
    'UserProfileFactory',
    'rebate_showFactory',
    'configFactory',
    'trialOrderFactory',
    'ENV',
    function(
      $rootScope,
      $scope,
      $ionicPopup,
      $state,
      $ionicLoading,
      $ionicModal,
      $filter,
      $stateParams,
      StorageFactory,
      UserProfileFactory,
      rebate_showFactory,
      configFactory,
      trialOrderFactory,
      ENV
    ) {
      //获得传过来的商品id
      var aid = $stateParams.id
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'

        // 获取会员参与该活动情况
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          //console.log(StorageFactory.get('user'));
          $scope.lnvolved_status = 0 //会员参与活动状态
          $scope.login_status = 0 //会员登录状态
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid

          //获取用户签名
          random = StorageFactory.get('user').data.random

          //console.log(StorageFactory.get('profile'));

          //获取当前用户会员组

          $scope.Member_group = StorageFactory.get('profile').groupid

          //获取用户支付宝绑定状态
          $scope.allpay_status = StorageFactory.get('profile').alipay_status

          //获取用户邮箱认证状态
          $scope.emall_status = StorageFactory.get('profile').email_status

          //获取用户实名认证状态
          $scope.name_status = StorageFactory.get('profile').name_status

          //console.log(StorageFactory.get('profile').name_status);

          // 获取用户手机认证状态
          $scope.phone_status = StorageFactory.get('profile').phone_status

          //获取用户绑定的淘宝帐号数量
          UserProfileFactory.set_username_taobao(userid, random)

          //获取会员是否参与过本次活动
          trialOrderFactory.set_order_is_join(aid, userid, random)

          //获取会员历史完成多少订单
          trialOrderFactory.set_complete_order(userid, random)

          $scope.lnvolved_status = 1

          $scope.login_status = 1 //会员登录状态
        }
      })

      //闪电试用分享

      $scope.fenxiang = function() {
        var title = $scope.showdata.title
        var content =
          '[闪电试用 无需审核]' + $scope.showdata.title + '限量份数' + $scope.showdata.goods_number + '快来免费申请吧！'
        var url = ENV.siteUrl + $scope.showdata.url
        var imageurl = $filter('imgUrl')($scope.showdata.thumb)

        //  console.log(title,content,url,imageurl);
        window.plugins.Baidushare.bdshare(
          title,
          content,
          url,
          imageurl,
          function(success) {
            if (success == 1) {
              $ionicLoading.show({
                noBackdrop: true,
                template: '分享成功！',
                duration: 1000
              })
            } else if (success == 2) {
            } else {
            }
          },
          function(fail) {
            alert('encoding failed: ' + fail)
          }
        )
      }

      $scope.ENV = ENV

      $scope.Application_reasons = ''

      $scope.commission_showloading = true

      $scope.Switch = 1

      $scope.data_time = Math.round(new Date().getTime() / 1000)

      //接收通知 当前用户已绑定淘宝帐号
      $scope.$on('UserProfileFactory.set_username_taobao', function() {
        $scope.data_bind_taobao = UserProfileFactory.get_username_taobao()

        //console.log($scope.data_bind_taobao);
        //获得用户默认淘宝帐号 如果为空 则指定第一个
        var lists = $scope.data_bind_taobao.lists

        function data_bind_taobao_default() {
          for (var x in lists) {
            if (lists[x].is_default == 1) {
              $scope.data_bind_taobao_default = lists[x]
            } else {
              $scope.data_bind_taobao_default = lists[0]
            }
          }
        }
        data_bind_taobao_default()
        taobao_default_id = $scope.data_bind_taobao_default == undefined ? '' : $scope.data_bind_taobao_default.id
        $scope.vm.data_bind_taobao_default.id = $scope.data_bind_taobao_default.id
      })

      // 定义选择淘宝号 和申请理由数组   答案下单的时候 的问题和答案
      var vm = ($scope.vm = {
        data_bind_taobao_default: {
          id: 0
        }
      })

      $('.s_part6 .part').live('click', function() {
        $('.s_part6 .part').attr('class', 'part')
        $(this).attr('class', 'part sel')
      })

      //后台请求获取后台的活动配置
      configFactory.set_commission_config()

      // 获取商品详情
      rebate_showFactory.get_rebate_show(aid)

      //商品介绍
      $scope.goods_body = function() {
        $scope.Switch = 1
      }

      //接收订单数量返回通知
      $scope.$on('trialOrderFactory.set_complete_order', function() {
        $scope.complete_order = trialOrderFactory.get_complete_order()

        //console.log($scope.complete_order );
      })

      //接收配置请求通知
      $scope.$on('configFactory.set_commission_config', function() {
        var trial_config = configFactory.get_commission_config() //网站试用配置
        $scope.bind_alipay = trial_config.data.buyer_join_condition.bind_alipay //绑定支付宝
        $scope.bind_taobao = trial_config.data.buyer_join_condition.bind_taobao //绑定淘宝
        $scope.bind_email = trial_config.data.buyer_join_condition.email //绑定邮箱
        $scope.bind_phone = trial_config.data.buyer_join_condition.phone //绑定手机
        $scope.realname = trial_config.data.buyer_join_condition.realname // 实名认证
        $scope.num_trial_art = trial_config.data.buyer_join_condition.num_trial_art
      })

      //接收商品详情通知

      $scope.$on('NewsContent.rebateshow', function() {
        $scope.showdata = rebate_showFactory.getshow()
        $scope.commission_showloading = false

        //console.log($scope.showdata);
      })

      //接收参与用户返回列表通知

      $scope.$on('rebate_showFactory.set_good_user_list', function() {
        $scope.user_list = rebate_showFactory.get_good_user_list()
        $scope.user_list_showloading = false

        //console.log($scope.user_list);
      })

      $scope.good_user_list = function() {
        $scope.user_list_showloading = true
        $scope.Switch = 2
        rebate_showFactory.set_good_user_list(aid, 1)
      }

      //接收用户是否参与过抢购通知
      $scope.$on('trialOrderFactory.set_order_is_join', function() {
        $scope.order_is_join = trialOrderFactory.get_order_is_join()
      })

      //普通用户申请试用  如果用户已经参与过本次活动，则不能再次申请
      //活动商品状态   -3 待审核待付款 -2 待审核已付款 -1 待上线 1活动进行中 2.活动结算 3. 已完成  4.撤销 5.屏蔽

      /*--------------普通申请------------------------
          -1 如果用户未登录，先跳转至会员登录页面

          0.如果当前活动状态不为进行中，拒绝申请。
          1.检测用户是否被商家拉入黑名单                      ---------------
          2.再次判断用户参与活动状态
          2.1 判断后台设置 需完善基本资料   需完成手机认证   需完成邮箱认证   需完成实名认证   需绑定淘宝账号   需绑定支付宝账号  需申请理由
          2.2 判断下单类型 如果是答案下单 则跳转输入答案页面

          3.判断后台设置，是否允许同一用户申请一个商家下的多次试用 ----------
          4.如果后台开启了让用户选择淘宝账号，则弹出让用户选择淘宝帐号，如果未绑定，则先跳转绑定页面
          5.如果后台开启了多商家说点什么，则同时跟淘宝帐号一起显示
          6.提交后台申请，等待返回结果。提交参数 userid 商品id 签名 

      */
      $scope.Apply_commission = function() {
        //console.log($scope.login_status);

        //未登录去登录
        if ($scope.login_status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，请先登录后再申请！',
            duration: 1500
          })

          $state.go('tab.user_login') //路由跳转登录
          return false
        }

        //活动状态不对 拒绝申请
        if ($scope.showdata.status != 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，当前活动未开始或已结束！',
            duration: 1500
          })

          return false
        }

        //判断是否参与过本活动
        if ($scope.lnvolved_status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲，您已参与过本次活动,不能再次参与！',
            duration: 1500
          })

          return false
        }

        //判断后台配置开启的参与活动条件 根据配置提示用户完善

        if (
          ($scope.bind_alipay == 5 && $scope.allpay_status != 1) ||
          ($scope.realname == 3 && $scope.name_status != 1) ||
          ($scope.bind_email == 2 && $scope.emall_status != 1) ||
          ($scope.bind_phone == 1 && $scope.phone_status != 1) ||
          ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count < 1) ||
          $scope.num_trial_art > $scope.complete_order
        ) {
          //弹出提示窗口页面 提示用户完成活动条件
          var alertPopup = $ionicPopup.alert({
            title: '参与条件',
            okText: '我已知晓', // String (默认: 'OK')。OK按钮的文字。
            okType: 'button-assertive button-outlin button-outline',
            templateUrl: 'a.html',
            scope: $scope
          })
          alertPopup.then(function(res) {})
          return false
        }

        //  //console.log($scope.showdata);

        //都未开启 提交后台申请
        $scope.taobao_reason()
      }

      /*   1.首先获得该商品兑换所需积分
       2.如果会员积分小于本次兑换所需积分，则提示积分不足
       3.如果积分充足 那么则提示绑定淘宝帐号 或者选择试用淘宝帐号
       4.最后再确定是否兑换
  */

      //弹出选择淘宝号 和对商家说点什么  根据后台配置来的 如果后台关闭了则不需要

      $scope.taobao_reason = function() {
        if ($scope.bind_taobao == 4 && $scope.data_bind_taobao.count > 0) {
          var alertPopup = $ionicPopup.alert({
            title: '选择帐号',
            templateUrl: 'b.html',
            scope: $scope,
            okText: '提交申请',
            buttons: [
              {
                text: '放弃'
              },
              {
                text: '提交申请',
                type: 'button-assertive',
                onTap: function(e) {
                  $scope.Submit_trial()
                }
              }
            ]
          })
          alertPopup.then(function(res) {})
          return false
        }
        $scope.Submit_trial()
      }

      //提交后台申请试用   传入
      $scope.Submit_trial = function(reason) {
        $ionicLoading.show({
          noBackdrop: true,
          template: '亲，正在为您提交申请！',
          duration: 1500
        })

        var taobao_id =
          $scope.vm.data_bind_taobao_default == undefined ? taobao_default_id : $scope.vm.data_bind_taobao_default.id

        taobao_id = $scope.bind_taobao == 4 ? taobao_id : ''
        trialOrderFactory.set_commission_pay_submit(aid, taobao_id, userid, random)
      }

      //接收申请结果返回通知

      $scope.$on('trialOrderFactory.set_commission_pay_submit', function() {
        var data_get_apply_order = trialOrderFactory.get_commission_pay_submit()

        //console.log(data_get_apply_order);

        if (data_get_apply_order.status == 0) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_get_apply_order.msg,
            duration: 1500
          })

          return false
        } else if (data_get_apply_order.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '恭喜您！闪电试用抢购成功',
            duration: 1500
          })
          //按钮变成已申请状态
          $scope.order_is_join.data = 0

          //跳转填写订单号页面
          $state.go('tab.commission_order_id', {
            id: data_get_apply_order.data,
            goodid: aid
          })

          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: '程序员哥哥，正在抢修',
            duration: 1500
          })
        }
      })

      function hang1SetHeight() {
        $('.s_part2 .hang1').height($('.s_part2 .hang1 img').height())
      }
    }
  ])

  /*
      name  我的闪电试用订单
     */

  .controller('commission_order', [
    '$rootScope',
    '$timeout',
    '$scope',
    '$state',
    '$ionicPopup',
    '$ionicModal',
    '$ionicLoading',
    'ENV',
    'StorageFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'configFactory',
    function(
      $rootScope,
      $timeout,
      $scope,
      $state,
      $ionicPopup,
      $ionicModal,
      $ionicLoading,
      ENV,
      StorageFactory,
      UserProfileFactory,
      trialOrderFactory,
      configFactory
    ) {
      //页面加载之前事件

      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
        if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
          $scope.userInfo = ''
          $scope.logo_status = 0
          $state.go('tab.user_login') //路由跳转登录
          return false
        } else {
          // 获取用户id
          userid = StorageFactory.get('user').data.userid

          //获取用户签名
          random = StorageFactory.get('user').data.random

          $scope.logo_status = 1

          //console.log(userid);
          trialOrderFactory.set_getorderlists(userid, 'commission', 1, random)
        }
      })

      //console.log(userid, 'commission', 1, random);
      $scope.ENV = ENV

      //设置加载中图标
      $scope.wdfl_showloading = true

      //默认显示已审核
      var type = 1
      //默认关闭上拉加载更多
      $scope.wdsy_hasNextPage = false
      //后台请求获取后台的返利活动配置
      configFactory.set_commission_config()
      //接收配置请求通知
      $scope.$on('configFactory.set_commission_config', function() {
        var rebate_config = configFactory.get_commission_config() //闪电试用配置

        //填写订单号时间限制
        $scope.buyer_write_order_time = rebate_config.data.buyer_write_order_time * 60

        //console.log(rebate_config);

        //商家审核总时间天数
        $scope.seller_check_time = rebate_config.data.seller_check_time * 3600
        //console.log($scope.seller_check_time);

        //修改订单号时间限制

        $scope.buyer_check_update_order_sn = rebate_config.data.buyer_check_update_order_sn * 3600

        //商家最终确认付款时间限制
        $scope.seller_pay_time = rebate_config.data.seller_pay_time * 3600
      })

      //接收订单返回通知
      $scope.$on('trialOrderFactory.set_getorderlists', function() {
        $scope.wdfl_showloading = false

        $scope.trial_getorderlists = trialOrderFactory.get_getorderlists()

        //console.log(trialOrderFactory.get_wdsy_hasNextPage());

        $scope.wdsy_hasNextPage = trialOrderFactory.get_wdsy_hasNextPage()

        $scope.$broadcast('scroll.infiniteScrollComplete') //广播通知！

        //console.log($scope.trial_getorderlists);
      })

      //点击订单跳转填写订单号页面
      $scope.add_order_number = function(order_id, goods_id, order_status, trial_report) {
        //如果订单状态是待审核资格，则跳转商品页面
        if (order_status == 1) {
          $state.go('tab.commission_show', {
            id: goods_id
          })

          return false

          //如果订单状态是已通过待填写订单号 则跳转订单号填写页面
        } else if (order_status == 2 || order_status == 4 || order_status == 3) {
          $state.go('tab.commission_order_id', {
            id: order_id,
            goodid: goods_id
          })

          //如果订单状态是填写试用报告 则跳转试用报告填写页面
        } else if (order_status == 7 && !trial_report) {
          //晒单分享 已完成

          $state.go('tab.commission_show', {
            id: goods_id
          })
        } else if (order_status == 5) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '亲,请等待商家最终确认返款！',
            duration: 1500
          })

          return false
        } else {
          $state.go('tab.commission_show', {
            id: goods_id
          })
        }

        //订单状态是已通过(不含试用报告) 跳转订单号填写页面
      }

      // 第二步 针对不同的订单状态 写各种方法

      //设置高亮，获取不同状态的订单
      $scope.trial_order_changeTab = function(index, type_id) {
        $scope.wdfl_showloading = true
        type = type_id

        $scope.wdsy_showloading = true

        trialOrderFactory.set_getorderlists(userid, 'commission', type, random)

        var a = document.getElementById('sub_header_list').getElementsByTagName('a')
        for (var i = 0; i < a.length; i++) {
          a[i].className = 'button button-clear '
        }
        a[index].className = 'button button-clear sub_button_select'
      }

      $scope.wdfl_doRefresh = function() {
        // 下拉更新 我的返利订单
        trialOrderFactory.set_getorderlists(userid, 'commission', type, random)

        //通知更新完成
        $scope.$broadcast('scroll.refreshComplete') //广播通知
      }

      //上拉加载更多
      $scope.wdfl_loadMore = function() {
        //console.log("上拉加载更多来了");

        //发起后端请求;
        trialOrderFactory.wdsy_loadMore(userid, 'commission', type, random)
      }

      $('.bar-subheader .button').bind('click', function() {
        $('.bar-subheader .button').attr('class', 'button button-clear')
        $(this).attr('class', 'button button-clear sub_button_select')
      })
    }
  ])

  //  闪电试用填写订单号

  .controller('commission_order_add', [
    '$rootScope',
    '$interval',
    '$filter',
    '$location',
    '$scope',
    '$state',
    '$stateParams',
    '$ionicPopup',
    '$ionicModal',
    '$ionicLoading',
    'ENV',
    'StorageFactory',
    'rebate_showFactory',
    'UserProfileFactory',
    'trialOrderFactory',
    'configFactory',
    function(
      $rootScope,
      $interval,
      $filter,
      $location,
      $scope,
      $state,
      $stateParams,
      $ionicPopup,
      $ionicModal,
      $ionicLoading,
      ENV,
      StorageFactory,
      rebate_showFactory,
      UserProfileFactory,
      trialOrderFactory,
      configFactory
    ) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      if (!StorageFactory.get('user') || (StorageFactory.get('user') && StorageFactory.get('user').status != 1)) {
        $state.go('tab.user_login') //路由跳转登录
        return false
      }

      //console.log($stateParams);

      //获得传过来的商品id
      var aid = $stateParams.goodid
      //获得传过来的订单id
      var order_id = $stateParams.id

      $scope.ENV = ENV

      $scope.txddh_showloading = true

      // 获取用户id
      var userid = StorageFactory.get('user').data.userid

      //获取用户签名
      var random = StorageFactory.get('user').data.random

      //获取后台试用活动设置
      configFactory.set_commission_config()

      //接收配置请求通知
      $scope.$on('configFactory.set_commission_config', function() {
        var rebate_config = configFactory.get_commission_config() //网站返利活动配置

        $scope.buyer_write_order_time = rebate_config.data.buyer_write_order_time * 60

        //商家审核总时间天数
        $scope.seller_check_time = rebate_config.data.seller_check_time * 3600

        //修改订单号时间限制
        $scope.buyer_check_update_order_sn = rebate_config.data.buyer_check_update_order_sn * 3600
      })

      // 第一步 获取这个商品详情
      rebate_showFactory.get_rebate_show(aid)

      // 定义用户填写订单号数组
      $scope.order_vm = {
        mum: ''
      }

      //晒单分享
      $scope.The_sun = function() {
        $ionicLoading.show({
          noBackdrop: true,
          template: '亲,正在提交...',
          duration: 1000
        })

        //后端提交晒单内容
        trialOrderFactory.set_shai_report(order_id, $scope.sybg_vm.img, $scope.sybg_vm.xinde, userid, random)
      }

      //接收晒单返回结果
      $scope.$on('trialOrderFactory.set_shai_report', function() {
        var shai_data = trialOrderFactory.get_shai_report()

        if (shai_data.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: shai_data.msg,
            duration: 1000
          })

          //跳转返回我的订单页面
          $state.go('tab.rebate_order')

          return false
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: shai_data.msg,
            duration: 1000
          })
        }
      })

      $scope.$on('NewsContent.rebateshow', function() {
        $scope.txddh_showdata = rebate_showFactory.getshow()
        $scope.txddh_showloading = false

        //console.log($scope.txddh_showdata);
        //如果是搜索下单，随机取一个搜索关系词
        if ($scope.txddh_showdata.type == 'search') {
          var keyword_string = $scope.txddh_showdata.goods_rule.keyword
          var keyword_string = keyword_string.replace(/，/g, ',') //增强用户体验，替换，
          var keyword_obj2 = keyword_string.split(',') //字符串转化为数组
          //console.log(keyword_obj2);
          $scope.goods_keyword = keyword_obj2[Math.floor(Math.random() * keyword_obj2.length)] //随机取得一个字符
        }
      })

      //第二步 获取这个订单详细信息
      trialOrderFactory.set_order_info(order_id, userid, random)

      //接收订单详细信息通知
      $scope.$on('trialOrderFactory.set_order_info', function() {
        $scope.order_info = trialOrderFactory.get_order_info()
        //console.log($scope.order_info);
      })

      $scope.add_order_number = function() {
        var alertPopup = $ionicPopup.show({
          title: '填写订单号',
          templateUrl: 'a.html',
          scope: $scope,
          buttons: [
            {
              text: '取消',
              type: 'button-assertive button-outlin button-clear'
            },
            {
              text: '确定',
              type: 'button-assertive button-outlin button-clear',
              onTap: function(e) {
                var patten = /^\d{9,20}$/
                if (!patten.test($scope.order_vm.mum)) {
                  e.preventDefault()
                  $ionicLoading.show({
                    noBackdrop: true,
                    template: '亲,订单号是9位以上数字！',
                    duration: 1000
                  })
                  return false
                }

                //发起后端请求 填写订单号
                trialOrderFactory.set_fill_order_sn(order_id, $scope.order_vm.mum, userid, random)
              }
            }
          ]
        })
      }

      //接收填写订单号返回结果通知
      $scope.$on('trialOrderFactory.set_fill_order_sn', function() {
        var data_fill_order_sn = trialOrderFactory.get_fill_order_sn()

        if (data_fill_order_sn.status == 1) {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_fill_order_sn.msg,
            duration: 1000
          })

          //跳转返回我的订单页面
          $state.go('tab.commission_order')
        } else {
          $ionicLoading.show({
            noBackdrop: true,
            template: data_fill_order_sn.msg,
            duration: 1000
          })
        }
      })

      //点击去下单 普通下单直接跳转至淘宝,搜索下单,直接跳转淘宝引导页面
      //点击去下单 普通下单直接跳转至淘宝,搜索下单,直接跳转淘宝引导页面
      $scope.To_order = function(type, source, url) {
        source = $filter('f_source')(source)

        var go_tiele = source + '搜索下单'
        url = getDomain(url)
        //提取搜索下单地址
        function getDomain(weburl) {
          var urlReg = /^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/i
          domain = weburl.match(urlReg)
          domain = domain[1] + 'm.' + domain[4] + domain[5]
          return domain != null && domain.length > 0 ? domain : ''
        }

        cordova.ThemeableBrowser.open(url, '_blank', {
          statusbar: {
            color: '#ffffffff'
          },
          toolbar: {
            height: 44,
            color: '#f0f0f0ff'
          },
          title: {
            color: '#003264ff',
            staticText: go_tiele,
            showPageTitle: true
          },
          backButton: {
            wwwImage: 'img/back.png',
            wwwImagePressed: 'img/back_pressed.png',
            align: 'left',
            event: 'backPressed'
          },
          closeButton: {
            wwwImage: 'img/close.png',
            wwwImagePressed: 'img/close_pressed.png',
            align: 'right',
            event: 'closePressed'
          },
          customButtons: [
            {
              wwwImage: 'img/menu.png',
              wwwImagePressed: 'img/menu.png',
              align: 'right',
              event: 'sharePressed'
            }
          ],
          menu: {
            wwwImage: 'img/menu2.png',
            wwwImagePressed: 'img/menu2.png',
            title: 'help',
            cancel: 'Cancel',
            align: 'right',
            event: 'helloPressed'
            /*             items: [
                 {
                     event: 'helloPressed',
                     label: '填写订单号'
                 },
                 {
                     event: 'testPressed',
                     label: '查看下单提示!'
                 }
             ]*/
          },
          backButtonCanClose: true
        })
          .addEventListener('backPressed', function(e) {
            //返回事件
          })
          .addEventListener('sharePressed', function(e) {
            alert(
              '活动下单方式为：搜索下单' +
                '\n' +
                '搜索关键词：' +
                $scope.txddh_showdata.keyword +
                '\n' +
                '2.按照 ' +
                $filter('f_sort')($scope.txddh_showdata.sort) +
                ' 排序' +
                '\n' +
                '3.宝贝位置大约在 ' +
                $scope.txddh_showdata.goods_address +
                '\n' +
                '4.查找掌柜旺旺' +
                $scope.txddh_showdata.goods_wangwang +
                '\n' +
                '5.活动下单价：' +
                $scope.txddh_showdata.goods_price +
                ' 元\n' +
                '6.活动完成：返还' +
                $filter('f_xiaoshu')($scope.txddh_showdata.fan_price) +
                ' 元\n' +
                '7.下单完成之后，记得填写订单号。'
            )
          })
          .addEventListener('helloPressed', function(e) {
            var name = prompt(
              '亲 请填写已付款下单的订单编号' +
                '\n' +
                ' 您可在订单详情查看订单号' +
                '\n' +
                '对照订单号仔细输入' +
                '\n' +
                '如果输入错误,可在此修改' +
                '\n' +
                '提示：订单号一般为10位以上纯数字',
              ''
            )

            if (name != null && name != '') {
              trialOrderFactory.set_fill_order_sn(order_id, name, userid, random)
            }
          })
          .addEventListener('sharePressed2', function(e) {})
          .addEventListener(cordova.ThemeableBrowser.EVT_ERR, function(e) {
            console.error(e.message)
          })
          .addEventListener(cordova.ThemeableBrowser.EVT_WRN, function(e) {
            //console.log(e.message);
          })
      }

      //我要申诉
      // 如果状态不等于4 拒绝申诉
      $scope.Appeal = function(status, order_id, goods_id) {
        //console.log(status, order_id, goods_id);
        if (status != 4) return false
        //跳转申诉页面 弹窗还是新页面纠结啊

        //跳转订单申诉页面
        $state.go('tab.order_appeal', {
          id: order_id,
          aid: goods_id
        })
      }

      //获取订单日志记录！
      $scope.Order_log = function() {
        //console.log(order_id);

        $state.go('tab.order_log', {
          id: order_id
        })
      }

      //第三步 判断下单类型 是普通下单 还是答案下单 二维码下单

      //接收订单返回通知
      $scope.$on('trialOrderFactory.set_getorderlists', function() {
        $scope.wdsy_showloading = false

        $scope.trial_getorderlists = trialOrderFactory.get_getorderlists()

        //console.log($scope.trial_getorderlists);
      })
    }
  ])

  //用户搜索

  .controller('so_list', [
    '$rootScope',
    '$scope',
    '$ionicLoading',
    'StorageFactory',
    'soFactory',
    function($rootScope, $scope, $ionicLoading, StorageFactory, soFactory) {
      $scope.$on('$ionicView.beforeEnter', function() {
        $rootScope.hideTabs = 'tabs-item-hide'
      })

      //获取服务器热门搜索
      $scope.hot_showloading = true

      $scope.quyu = 1

      soFactory.set_keyword_hot()

      // 热门搜索关键词
      $scope.$on('soFactory.set_keyword_hot', function() {
        $scope.keyword_hot = soFactory.get_keyword_hot()

        $scope.hot_showloading = false
      })

      //搜索结果通知

      $scope.$on('soFactory.set_search_goods', function() {
        var search_goods = soFactory.get_search_goods()

        $scope.search_goods = uniqueArray(search_goods)

        function uniqueArray(data) {
          data = data || []
          var a = {}
          for (var i = 0; i < data.length; i++) {
            var v = data[i]
            if (typeof a[v] == 'undefined') {
              a[v] = 1
            }
          }

          data.length = 0
          for (var i in a) {
            data[data.length] = i
          }

          return data
        }

        $scope.so_showloading = false
      })

      //首先获取用户的搜索记录
      $scope.data_keyword = StorageFactory.get('keyword')

      //console.log($scope.data_keyword);

      var Key = new Array()
      $scope.search = function(Keyword) {
        if (Keyword == '' || Keyword == null) {
          $ionicLoading.show({
            noBackdrop: true,
            template: '请输入搜索关键词',
            duration: 1000
          })

          return false
        }

        $scope.quyu = 2
        $scope.so_showloading = true

        //提交服务器搜索数据

        soFactory.set_search_goods(Keyword)

        if ($scope.data_keyword) {
          for (var i in $scope.data_keyword) {
            //该元素在tmp内部不存在才允许追加
            if (Keyword.indexOf($scope.data_keyword[i]) == -1) {
              key = $scope.data_keyword.concat(Keyword)
              StorageFactory.set('keyword', key)
              //console.log(key);
            }
          }
        } else {
          Key[0] = Keyword
          StorageFactory.set('keyword', Key)
        }
      }

      //清除历史记录
      $scope.dedele = function() {
        StorageFactory.remove('keyword')
      }
    }
  ])
