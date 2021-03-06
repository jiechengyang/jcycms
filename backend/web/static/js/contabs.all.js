window.openConTab = function (that) {
    $("#openNewContab", window.parent.document).remove();
    var title = that.attr("title") == "undifiend" ? "详情" : that.attr("title");
    var href = that.attr("href") == "undifined" ? "/" : that.attr("href");
    $("body", window.parent.document).append('<a class="J_menuItem" href="' + that.attr("href") + '" id="openNewContab" data-index="0">' + title + "</a>");
    $("#openNewContab", window.parent.document).bind("click", openContabsInternal);
    $("#openNewContab", window.parent.document).click()
};
window.closeContab = function (that) {
    that.click()
};

function createTabMenu(obj, index) {
    deleteTabMenu(obj);
    window.setTimeout(function () {
        if (index == undefined) {
            index = 0
        }
        var html = '<div class="jcymenu" id="jcymenu-context-menu"><div class="jcymenuli open "><ul role="menu" class="dropdown-menu dropdown-menu-right">';
        var listr = "";
        var linestr = '<li class="divider"></li>';
        var lioption = [{
            liclass: "J_tabRerfer",
            iconclass: "fa fa-refresh",
            text: "刷新当前选项卡"
        }, {
            liclass: "J_tabCloseNow",
            iconclass: "fa fa-remove",
            text: "关闭当前选项卡"
        }, {
            liclass: "J_tabCloseOther",
            iconclass: "fa fa-remove",
            text: "关闭其他选项卡"
        }, {
            liclass: "J_tabCloseAll",
            iconclass: "fa fa-remove",
            text: "关闭全部选项卡"
        }, ];
        if (index == 0) {
            lioption = [{
                liclass: "J_tabRerfer",
                iconclass: "fa fa-refresh",
                text: "刷新当前选项卡"
            }, {
                liclass: "J_tabCloseOther",
                iconclass: "fa fa-remove",
                text: "关闭其他选项卡"
            }, {
                liclass: "J_tabCloseAll",
                iconclass: "fa fa-remove",
                text: "关闭全部选项卡"
            }, ]
        } else {
            if (!$(obj).hasClass("active")) {
                lioption = [{
                    liclass: "J_tabCloseNow",
                    iconclass: "fa fa-remove",
                    text: "关闭选项卡"
                }, {
                    liclass: "J_tabCloseAll",
                    iconclass: "fa fa-remove",
                    text: "关闭全部选项卡"
                }, ]
            }
        }
        for (var p in lioption) {
            listr += "<li class='" + lioption[p].liclass + "'><a><i class='" + lioption[p].iconclass + "'></i>&nbsp;&nbsp;&nbsp;" + lioption[p].text + "</a></li>" + linestr
        }
        listr = listr.substring(0, listr.length - linestr.length);
        var witdths = 0;
        html += listr + "</ul></div></div>";
        for (var i = 0; i < index; i++) {
            var _obj = $(obj).parent().find("a.J_menuTab").eq(i);
            var width = parseInt(_obj.width()) * 1.5;
            witdths += width
        }
        witdths += parseInt($(obj).width());
        $("#wrapper").prepend(html);
        var left = parseInt($("#jcymenu-context-menu").css("left"));
        left += witdths;
        if (index >= 16) {
            var parentObj = $(obj).parent('div.page-tabs-content').eq(0);
            left += parseInt(parentObj.css('marginLeft')) - parseInt($(obj).parent().find("a.J_menuTab").eq(index-1)/2);
        }

        $("#jcymenu-context-menu").css("left", left + "px")
    }, 250)
}

function deleteTabMenu(obj) {
    TabMenuClick(obj);
    window.setTimeout(function () {
        if ($("#jcymenu-context-menu").length > 0) {
            $("#jcymenu-context-menu").remove()
        }
    }, 200)
}

function TabMenuClick(obj) {
    $(".J_tabRerfer").bind("click", function (e) {
        reloadIframe()
    });
    $(".J_tabCloseNow").bind("click", function (e) {
        close_tab(obj)
    });
    $(".J_tabCloseOther").on("click", function (e) {
        $(".page-tabs-content").children("[data-id]").not(":first").not(".active").each(function () {
            $('.J_iframe[data-id="' + $(this).data("id") + '"]').remove();
            $(this).remove()
        });
        $(".page-tabs-content").css("margin-left", "0")
    });
    $(".J_tabCloseAll").on("click", function () {
        $(".page-tabs-content").children("[data-id]").not(":first").each(function () {
            $('.J_iframe[data-id="' + $(this).data("id") + '"]').remove();
            $(this).remove()
        });
        $(".page-tabs-content").children("[data-id]:first").each(function () {
            $('.J_iframe[data-id="' + $(this).data("id") + '"]').show();
            $(this).addClass("active")
        });
        $(".page-tabs-content").css("margin-left", "0")
    })
}

function close_tab(obj) {
    if ($(obj).hasClass("active")) {
        $(obj).prev("a.J_menuTab").addClass("active");
        var dataId = $(obj).data("id") ? $(obj).data("id") : $(obj).attr("data-id");
        if ($('.J_iframe[data-id="' + dataId + '"]').prev("iframe.J_iframe").length > 0) {
            $('.J_iframe[data-id="' + dataId + '"]').prev("iframe.J_iframe").show()
        }
        $('.J_iframe[data-id="' + dataId + '"]').remove()
    }
    $(obj).remove()
}
var openContabsInternal;
$(function () {
    $(".J_menuTabs a.J_menuTab").bind("contextmenu", function (e) {
        var index = $(".J_menuTabs a.J_menuTab").index(this);
        createTabMenu(this, index);
        return false
    });
    $(".J_menuTabs a.J_menuTab").bind("blur", function (e) {
        deleteTabMenu(this)
    });

    function f(l) {
        var k = 0;
        $(l).each(function () {
            k += $(this).outerWidth(true)
        });
        return k
    }

    function g(n) {
        var o = f($(n).prevAll()),
            q = f($(n).nextAll());
        var l = f($(".content-tabs").children().not(".J_menuTabs"));
        var k = $(".content-tabs").outerWidth(true) - l;
        var p = 0;
        if ($(".page-tabs-content").outerWidth() < k) {
            p = 0
        } else {
            if (q <= (k - $(n).outerWidth(true) - $(n).next().outerWidth(true))) {
                if ((k - $(n).next().outerWidth(true)) > q) {
                    p = o;
                    var m = n;
                    while ((p - $(m).outerWidth()) > ($(".page-tabs-content").outerWidth() - k)) {
                        p -= $(m).prev().outerWidth();
                        m = $(m).prev()
                    }
                }
            } else {
                if (o > (k - $(n).outerWidth(true) - $(n).prev().outerWidth(true))) {
                    p = o - $(n).prev().outerWidth(true)
                }
            }
        }
        $(".page-tabs-content").animate({
            marginLeft: 0 - p + "px"
        }, "fast")
    }

    function a() {
        var o = Math.abs(parseInt($(".page-tabs-content").css("margin-left")));
        var l = f($(".content-tabs").children().not(".J_menuTabs"));
        var k = $(".content-tabs").outerWidth(true) - l;
        var p = 0;
        if ($(".page-tabs-content").width() < k) {
            return false
        } else {
            var m = $(".J_menuTab:first");
            var n = 0;
            while ((n + $(m).outerWidth(true)) <= o) {
                n += $(m).outerWidth(true);
                m = $(m).next()
            }
            n = 0;
            if (f($(m).prevAll()) > k) {
                while ((n + $(m).outerWidth(true)) < (k) && m.length > 0) {
                    n += $(m).outerWidth(true);
                    m = $(m).prev()
                }
                p = f($(m).prevAll())
            }
        }
        $(".page-tabs-content").animate({
            marginLeft: 0 - p + "px"
        }, "fast")
    }

    function b() {
        var o = Math.abs(parseInt($(".page-tabs-content").css("margin-left")));
        var l = f($(".content-tabs").children().not(".J_menuTabs"));
        var k = $(".content-tabs").outerWidth(true) - l;
        var p = 0;
        if ($(".page-tabs-content").width() < k) {
            return false
        } else {
            var m = $(".J_menuTab:first");
            var n = 0;
            while ((n + $(m).outerWidth(true)) <= o) {
                n += $(m).outerWidth(true);
                m = $(m).next()
            }
            n = 0;
            while ((n + $(m).outerWidth(true)) < (k) && m.length > 0) {
                n += $(m).outerWidth(true);
                m = $(m).next()
            }
            p = f($(m).prevAll());
            if (p > 0) {
                $(".page-tabs-content").animate({
                    marginLeft: 0 - p + "px"
                }, "fast")
            }
        }
    }
    $(".J_menuItem").each(function (k) {
        if (!$(this).attr("data-index")) {
            $(this).attr("data-index", k)
        }
    });

    function c() {
        var o = $(this).attr("href"),
            m = $(this).data("index"),
            l = $.trim($(this).text()),
            k = true;
        if (o == undefined || $.trim(o).length == 0) {
            return false
        }
        $(".J_menuTab").each(function () {
            if ($(this).data("id") == o) {
                if (!$(this).hasClass("active")) {
                    $(this).addClass("active").siblings(".J_menuTab").removeClass("active");
                    g(this);
                    $(".J_mainContent .J_iframe").each(function () {
                        if ($(this).data("id") == o) {
                            $(this).show().siblings(".J_iframe").hide();
                            return false
                        }
                    })
                }
                k = false;
                return false
            }
        });
        if (k) {
            var p = '<a href="javascript:;" class="active J_menuTab" data-id="' + o + '">' + l + ' <i class="fa fa-times-circle"></i></a>';
            $(".J_menuTab").removeClass("active");
            var n = '<iframe class="J_iframe" name="iframe' + m + '" width="100%" height="100%" src="' + o + '" frameborder="0" data-id="' + o + '" seamless></iframe>';
            $(".J_mainContent").find("iframe.J_iframe").hide().parents(".J_mainContent").append(n);
            $(".J_menuTabs .page-tabs-content").append(p);
            g($(".J_menuTab.active"))
        }
        $(".J_menuTabs a.J_menuTab").bind("contextmenu", function (e) {
            var index = $(".J_menuTabs a.J_menuTab").index(this);
            createTabMenu(this, index);
            return false
        });
        $(".J_menuTabs a.J_menuTab").bind("blur", function (e) {
            deleteTabMenu(this)
        });
        return false
    }
    $(".J_menuItem").on("click", c);

    function h() {
        var m = $(this).parents(".J_menuTab").data("id");
        var l = $(this).parents(".J_menuTab").width();
        if ($(this).parents(".J_menuTab").hasClass("active")) {
            if ($(this).parents(".J_menuTab").next(".J_menuTab").size()) {
                var k = $(this).parents(".J_menuTab").next(".J_menuTab:eq(0)").data("id");
                $(this).parents(".J_menuTab").next(".J_menuTab:eq(0)").addClass("active");
                $(".J_mainContent .J_iframe").each(function () {
                    if ($(this).data("id") == k) {
                        $(this).show().siblings(".J_iframe").hide();
                        return false
                    }
                });
                var n = parseInt($(".page-tabs-content").css("margin-left"));
                if (n < 0) {
                    $(".page-tabs-content").animate({
                        marginLeft: (n + l) + "px"
                    }, "fast")
                }
                $(this).parents(".J_menuTab").remove();
                $(".J_mainContent .J_iframe").each(function () {
                    if ($(this).data("id") == m) {
                        $(this).remove();
                        return false
                    }
                })
            }
            if ($(this).parents(".J_menuTab").prev(".J_menuTab").size()) {
                var k = $(this).parents(".J_menuTab").prev(".J_menuTab:last").data("id");
                $(this).parents(".J_menuTab").prev(".J_menuTab:last").addClass("active");
                $(".J_mainContent .J_iframe").each(function () {
                    if ($(this).data("id") == k) {
                        $(this).show().siblings(".J_iframe").hide();
                        return false
                    }
                });
                $(this).parents(".J_menuTab").remove();
                $(".J_mainContent .J_iframe").each(function () {
                    if ($(this).data("id") == m) {
                        $(this).remove();
                        return false
                    }
                })
            }
        } else {
            $(this).parents(".J_menuTab").remove();
            $(".J_mainContent .J_iframe").each(function () {
                if ($(this).data("id") == m) {
                    $(this).remove();
                    return false
                }
            });
            g($(".J_menuTab.active"))
        }
        return false
    }
    $(".J_menuTabs").on("click", ".J_menuTab i", h);

    function i() {
        $(".page-tabs-content").children("[data-id]").not(":first").not(".active").each(function () {
            $('.J_iframe[data-id="' + $(this).data("id") + '"]').remove();
            $(this).remove()
        });
        $(".page-tabs-content").css("margin-left", "0")
    }
    $(".J_tabCloseOther").on("click", i);

    function j() {
        g($(".J_menuTab.active"))
    }
    $(".J_tabShowActive").on("click", j);

    function e() {
        if (!$(this).hasClass("active")) {
            var k = $(this).data("id");
            $(".J_mainContent .J_iframe").each(function () {
                if ($(this).data("id") == k) {
                    $(this).show().siblings(".J_iframe").hide();
                    return false
                }
            });
            $(this).addClass("active").siblings(".J_menuTab").removeClass("active");
            g(this)
        }
    }
    $(".J_menuTabs").on("click", ".J_menuTab", e);

    function d() {
        var l = $('.J_iframe[data-id="' + $(this).data("id") + '"]');
        var k = l.attr("src")
    }
    $(".J_menuTabs").on("dblclick", ".J_menuTab", d);
    $(".J_tabLeft").on("click", a);
    $(".J_tabRight").on("click", b);
    $(".J_tabCloseAll").on("click", function () {
        $(".page-tabs-content").children("[data-id]").not(":first").each(function () {
            $('.J_iframe[data-id="' + $(this).data("id") + '"]').remove();
            $(this).remove()
        });
        $(".page-tabs-content").children("[data-id]:first").each(function () {
            $('.J_iframe[data-id="' + $(this).data("id") + '"]').show();
            $(this).addClass("active")
        });
        $(".page-tabs-content").css("margin-left", "0")
    });
    openContabsInternal = c
});