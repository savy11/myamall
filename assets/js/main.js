// JavaScript Document

$(window).on('DOMContentLoaded', function () {
    "use strict";

    //Keep track of last scroll
    var lastScroll = 0;
    var header = $("#header");
    var headerfixed = $("#header-main-fixed");
    var headerfixedbg = $(".header-bg");
    var headerfixedtopbg = $(".top-header-bg");
    $(window).scroll(function () {
        //Sets the current scroll position
        var st = $(this).scrollTop();
        //Determines up-or-down scrolling
        if (st > lastScroll) {

            //Replace this with your function call for downward-scrolling
            if (st > 50) {
                header.addClass("header-top-fixed");
                header.find(".header-top-row").addClass("dis-n");
                headerfixedbg.addClass("header-bg-fixed");
                headerfixed.addClass("header-main-fixed");
                headerfixedtopbg.addClass("top-header-bg-fix");
            }
        } else {
            //Replace this with your function call for upward-scrolling
            if (st < 50) {
                header.removeClass("header-top-fixed");
                header.find(".header-top-row").removeClass("dis-n");
                headerfixed.removeClass("header-main-fixed");
                headerfixedbg.removeClass("header-bg-fixed");
                headerfixedtopbg.removeClass("top-header-bg-fix");
                headerfixed.removeClass("header-main-fixed")
            }
        }
        //Updates scroll position
        lastScroll = st;
    });


    $('.dropdown').hover(function () {
        $(this).addClass('open');
    }, function () {
        $(this).removeClass('open');
    });

    $('[data-toogle="tooltip"]').tooltip();

    // Color Filter
    $(".colors li a").each(function () {
        $(this).css("background-color", "#" + $(this).attr("rel")).attr("href", "#" + $(this).attr("rel"));
    });

    // Categories Menu Manipulations
    $(".ul-side-category li a").click(function () {
        var sm = $(this).next();
        if (sm.hasClass("sub-category")) {
            if (sm.css("display") === "none") {
                $(this).next().slideDown();
            } else {
                $(this).next().slideUp();
                $(this).next().find(".sub-category").slideUp();
            }
            return false;
        } else {
            return true;
        }
    });
    // new WOW().init();
});



