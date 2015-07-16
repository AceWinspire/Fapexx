$( document ).ready(function() {
    $('.right-part').css('height',$('.left-part').height());
});

$( window ).resize(function() {
    $('.right-part').css('height',$('.left-part').height());
});

var ua = navigator.userAgent;
var video = document.createElement("video"),
        idevice = /ip(hone|ad|od)/i.test(ua),
        noflash = flashembed.getVersion()[0] === 0;
//simulate = !idevice && noflash && !!(video.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"').replace(/no/, ''));
var BASE_URL = 'http://' + window.location.hostname + '/';
var time;
var redirect = false;
var player = null;
var clip = null;
var timerHelper = 0;
var checkHelper = null;
var current_channel_id = null;
var reloadInterval;
function checkAndroidVersion() {
    if (ua.indexOf("Android") >= 0)
    {
        var androidversion = parseFloat(ua.slice(ua.indexOf("Android") + 8));
        return androidversion;
    }
}
function resizeVideoContainer() {
    var webTv = $("#webtv").width();
    var h = webTv * 9 / 16;
    return h;
}
function alertFn() {
    alert("Već ste ulogovani sa drugog uređaja.")
    window.location.href = BASE_URL + "user/login";
    return;
}

function onJavaScriptBridgeCreated(playerId)
{
    if (player == null) {
        player = document.getElementById('strobe');
        player.addEventListener("complete", "completeFunc");
        if (checkHelper == null) {
            checkHelper = true;
            time = true;
        }
    }
}

function playerStrobe(obj) {
    if (noflash == true || flashembed.getVersion()[0] < 10) {
        noFlashFn();
        return;
    }

    $("#webtv").html("<div id='strobe'></div>");
    clip = obj;
    var pqs = new ParsedQueryString();
    var parameterNames = pqs.params(false);
    var width = $("#webtv").width();
    var h = resizeVideoContainer();
    var parameters = {
        src: obj.url,
        autoPlay: "true",
        verbose: true,
        controlBarAutoHide: "true",
        controlBarPosition: "bottom",
        scaleMode: "stretch",
        streamType: "live",
        javascriptCallbackFunction: "onJavaScriptBridgeCreated",
        poster: obj.img,
        plugin_hls: BASE_URL + "js/strobe/HLSDynamicPlugin.swf"
    };

    for (var i = 0; i < parameterNames.length; i++) {
        var parameterName = parameterNames[i];
        parameters[parameterName] = pqs.param(parameterName) ||
                parameters[parameterName];
    }

    var wmodeValue = "transparent";
    var wmodeOptions = ["direct", "opaque", "transparent", "window"];
    if (parameters.hasOwnProperty("wmode"))
    {
        if (wmodeOptions.indexOf(parameters.wmode) >= 0)
        {
            wmodeValue = parameters.wmode;
        }
        delete parameters.wmode;
    }
    var name = "StrobeMediaPlayback_" ;
        name += obj['is_premium'] && charged_user == false ? "premium_video" : '';

    // Embed the player SWF:	            
    swfobject.embedSWF(
            BASE_URL + "js/strobe/StrobeMediaPlayback.swf"
            , "strobe"
            , width
            , h
            , "10.1.0"
            , "expressInstall.swf"
            , parameters
            , {
        allowFullScreen: "true",
        wmode: wmodeValue
    }
    , {
        name: name
    }
    );
}

function html5Video(obj) {
    var name = obj['is_premium'] && charged_user == false ? "premium_video" : '';
    var videoTag = "";
    var h = resizeVideoContainer();
    videoTag += '<div style="position:relative;height:' + h + ';width:' + $("#webtv").width() + '">';
    videoTag += '<video id="webtv_html5Video" style="position:relative;z-index:5" width="100%" height="' + h + '" preload="none" autoplay controls x-webkit-airplay="allow" src="' + obj.url + '" poster="' + obj.img + '" name="' + name + '" type="application/x-mpegURL">';
    videoTag += '</video>';
    videoTag += '<div style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:999" id="click-simulate"></div>';
    videoTag += '</div>';
    $("#webtv").html(videoTag);
    var video = $('#webtv_html5Video')[0];
    video.load();
    $("#webtv_html5Video,#click-simulate").on("click touch",function() {
        video.load();
        video.webkitEnterFullscreen();
        setTimeout(function() {
            video.play();
        }, 500);
        videoPlayedListener(obj.is_premium);
    });
    video.addEventListener('ended', endVideo, false);
}

function endVideo() {
    var video = $('#webtv_html5Video')[0];
    if(video.getAttribute('name').indexOf("premium_video") > -1){
        window.location.href = BASE_URL + "index/packages";
    }
};

function directStream(obj) {
    var player = document.getElementById("webtv");
    var h = resizeVideoContainer();
    $("#webtv").removeClass('fp3');
    player.innerHTML = '<a href="' + obj.url + '" style="height:' + h + 'px;display:block;width:100%;background:url(' + obj.img + ') no-repeat center center"></a>';
}

function noFlashFn() {
    $("#webtv").html("<div id='strobe' style='color:#fff;margin-top:15px;'><h2></h2><p></p></div>");
    $("#strobe h2").html("Flash verzija 10.1 ili veća je obavezna")
    $("#strobe p").html("Nemate instaliran flash plugin<br>Možete ga skinuti klikom na sledeći <a href='http://get.adobe.com/flashplayer/' target='_blank'>Link</a>");
}

function completeFunc() {
    if(player['name'].indexOf("premium_video") > -1){
        window.location.href = BASE_URL + "index/packages";
    }
}

function videoPlayedListener(video_is_premium){
    if(charged_user == false && video_is_premium){
        var counter_video_played = 0;
        var increase = window.setInterval(function(){
            counter_video_played++;
            if(counter_video_played == 20){
                clearInterval(increase);
                window.location.href = BASE_URL + "index/packages";
            }
        }, 1000);
    }
}

$(function() {
    $(window).resize(function() {
        $("#webtv").width('100%');
    });
    if (!window.addEventListener) {
        window.attachEvent("orientationchange", function() {
            $("#webtv,#webtv div").width('100%');
        });
    }
    else {
        window.addEventListener("orientationchange", function() {
            $("#webtv,#webtv div").width('100%');
        }, false);
    }
});

$( document ).ready(function() {
    var result = typeof content_object === 'undefined' ? '' : content_object;
    if(result != ''){
        if (checkAndroidVersion() >= 4.0 && checkAndroidVersion() < 4.3) {
            html5Video(result);
            return;
        }
        if (checkAndroidVersion() >= 4.3) {
            html5Video(result);
            //directStream(result);
            return;
        }
        if (idevice) {
            html5Video(result);
            return;
        }
        $("#webtv").slideDown('fast', function() {
            playerStrobe(result);
        });
    }
});

$("input[id='fake-rating']").rating({
    'showClear':false,
    'disabled':true,
    'showCaption':false,
});

$( document ).ready(function() {
    if( !$('#flash-messanger').is(':empty') ) {
        var opts = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-bottom-right",
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        $.each($("div[class^='wrapper']"),function(){
            var message = $(this).data("message");
            var type = $(this).data("type");
            switch(type){
                case 'success': 
                    toastr.success(message, null, opts);
                    break;
                case 'info' : 
                    toastr.info(message, null, opts);
                    break;
                case 'error' :
                    toastr.error(message, null, opts);
                    break;
                case 'warning' :
                    toastr.warning(message, null, opts);
                    break;
                default:
                    break;
            }
        })
    }
})
