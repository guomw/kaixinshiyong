//兼容ie6的fixed代码 
//jQuery(function($j){
//	$j('#pop').positionFixed()
//})
(function ($j) {
    $j.positionFixed = function (el) {
        $j(el).each(function () {
            new fixed(this)
        })
        return el;
    }
    $j.fn.positionFixed = function () {
        return $j.positionFixed(this)
    }
    var fixed = $j.positionFixed.impl = function (el) {
        var o = this;
        o.sts = {
            target: $j(el).css('position', 'fixed'),
            container: $j(window)
        }
        o.sts.currentCss = {
            top: o.sts.target.css('top'),
            right: o.sts.target.css('right'),
            bottom: o.sts.target.css('bottom'),
            left: o.sts.target.css('left')
        }
        if (!o.ie6) return;
        o.bindEvent();
    }
    $j.extend(fixed.prototype, {
        ie6: $.browser.msie && $.browser.version < 7.0,
        bindEvent: function () {
            var o = this;
            o.sts.target.css('position', 'absolute')
            o.overRelative().initBasePos();
            o.sts.target.css(o.sts.basePos)
            o.sts.container.scroll(o.scrollEvent()).resize(o.resizeEvent());
            o.setPos();
        },
        overRelative: function () {
            var o = this;
            var relative = o.sts.target.parents().filter(function () {
                if ($j(this).css('position') == 'relative') return this;
            })
            if (relative.size() > 0) relative.after(o.sts.target)
            return o;
        },
        initBasePos: function () {
            var o = this;
            o.sts.basePos = {
                top: o.sts.target.offset().top - (o.sts.currentCss.top == 'auto' ? o.sts.container.scrollTop() : 0),
                left: o.sts.target.offset().left - (o.sts.currentCss.left == 'auto' ? o.sts.container.scrollLeft() : 0)
            }
            return o;
        },
        setPos: function () {
            var o = this;
            o.sts.target.css({
                top: o.sts.container.scrollTop() + o.sts.basePos.top,
                left: o.sts.container.scrollLeft() + o.sts.basePos.left
            })
        },
        scrollEvent: function () {
            var o = this;
            return function () {
                o.setPos();
            }
        },
        resizeEvent: function () {
            var o = this;
            return function () {
                setTimeout(function () {
                    o.sts.target.css(o.sts.currentCss)
                    o.initBasePos();
                    o.setPos()
                }, 1)
            }
        }
    })
})(jQuery)

jQuery(function ($j) {
    $j('#footer').positionFixed()
})

//pop右下角弹窗函数
//作者：yanue
function Pop(title, url, intro) {
    this.initPop();
    this.title = title;
    this.url = url;
    this.intro = intro;
    this.apearTime = 1000;
    this.hideTime = 500;
    this.delay = 10000;
    //添加信息
    this.addInfo();
    //显示
    this.showDiv();
    //关闭
    this.closeDiv();

}

Pop.prototype = {
    initPop: function() {
        if ($('#pop').length == 0) {
            var pop = '<style type="text/css">' +
                '#pop{background:#fff;width:260px;border:1px solid #e0e0e0;font-size:12px;position: fixed;right:10px;bottom:10px;z-index:99999;}' +
                '#popHead{line-height:32px;background:#f6f0f3;border-bottom:1px solid #e0e0e0;position:relative;font-size:12px;padding:0 0 0 10px;}' +
                '#popHead h2{font-size:14px;color:#666;line-height:32px;height:32px;}' +
                '#popHead #popClose{position:absolute;right:10px;top:1px;}' +
                '#popHead a#popClose:hover{color:#f00;cursor:pointer;}' +
                '#popContent{padding:5px 10px;}' +
                '#popTitle a{line-height:24px;font-size:14px;font-family:"微软雅黑";color:#333;font-weight:bold;text-decoration:none;}' +
                '#popTitle a:hover{color:#f60;}' +
                '#popIntro{text-indent:24px;line-height:160%;margin:5px 0;color:#666;}' +
                '#popMore{text-align:right;border-top:1px dotted #ccc;line-height:24px;margin:8px 0 0 0;}' +
                '#popMore a{color:#f60;}' +
                '#popMore a:hover{color:#f00;}' +
                '</style>' +
                '<div id="pop" style="display:none;">' +
                '<div id="popHead">' +
                '<a id="popClose" title="关闭">关闭</a>' +
                '<h2>温馨提示</h2>' +
                '</div>' +
                '<div id="popContent">' +
                '<dl>' +
                '<dt id="popTitle"><a href="http://yanue.info/" target="_blank">这里是参数</a></dt>' +
                '<dd id="popIntro">这里是内容简介</dd>' +
                '</dl>' +
                '<p id="popMore"><a href="http://yanue.info/" target="_blank">查看 »</a></p>' +
                '</div>' +
                '</div>';
            $('body').append(pop);
        }
    },
    addInfo: function() {
        $("#popTitle a").attr('href', this.url).html(this.title);
        $("#popIntro").html(this.intro);
        $("#popMore a").attr('href', this.url);
    },
    showDiv: function(time) {
        if (!($.browser.msie && ($.browser.version == "6.0") && !$.support.style)) {
            $('#pop').slideDown(this.apearTime).delay(this.delay).fadeOut(400);
            ;
        } else { //调用jquery.fixed.js,解决ie6不能用fixed
            $('#pop').show();
            jQuery(function($j) {
                $j('#pop').positionFixed()
            })
        }
    },
    closeDiv: function() {
        $("#popClose").click(function() {
            $('#pop').hide();
        }
        );
    }
};
