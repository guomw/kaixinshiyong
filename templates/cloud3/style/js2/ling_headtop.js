

$(function () {
    initLoginHover();
    initNaviateHover();
    //initPop();
});

function initLoginHover() {
    var $nickname = $(".top-nickname"), $toggle = $(".top-toggle"), $up_down = $(".top-nickname i");
    $(".top-toggle, .top-nickname").hover(function () {
        $toggle.css("display", "block");
        $nickname.addClass("hover");
        $up_down.addClass("top-up");
    }, function () {
        $toggle.css("display", "none");
        $nickname.removeClass("hover");
        $up_down.removeClass("top-up");
    });
    //用户登录
    $('#dluyhu').hover(function () {
        $('#qqsjdel').addClass('mrxhjthover');
        $('#Cellph').show();
    }, function () {
        $('#qqsjdel').removeClass('mrxhjthover');
        $('#Cellph').hide();
    });
}

function initNaviateHover() {
    //网站导航
    $('#naviatetext,#naviate').hover(function () {
        $('#naviatetext').addClass('naviatehover');
        $('#naviate').show();
    }, function () {
        $('#naviatetext').removeClass('naviatehover');
        $('#naviate').hide();
    });
}

//function initPop() {
//    if ("@(model.Pop.sk_id > 0)" == "True") {
//        var pop = new Pop("领啦网：",
//        "@(model.Pop.sk_link)",
//        "@(model.Pop.sk_content)");
//    }
//}

//function initMessageHover() {
//    $(".top_ml dl dd").hover(function () {
//        $(this).find(".top_ld").show();
//    }, function () {
//        $(this).find(".top_ld").hide();
//    });
//}