//@todo event update-channel za statistiku ne radi kako treba, ako se pusti jedan kanal, trigeruje se update-channel/channel_id na 
// odredjeni interval, kada se pusti drugi kanal, trigeruje se update-channel sa id-em tog kanala ali se trigeruje i prvi event i 	
// dobija se netacna statistika

var ua = navigator.userAgent;
var video = document.createElement("video"),
        idevice = /ip(hone|ad|od)/i.test(ua),
        noflash = flashembed.getVersion()[0] === 0;
//simulate = !idevice && noflash && !!(video.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"').replace(/no/, ''));
var BASE_URL = 'http://' + window.location.hostname + '/';
var fp3config;
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
    var windowHeight = $(window).height();
    var headerHeight = $("#header").height();
    var footerHeight = $("#footer").height();
    var channelsHeight = $(".channels").height();
    var heightSum = headerHeight + footerHeight + channelsHeight;
    var webTv = $("#webtv").width();
    var h = webTv * 9 / 16;
    return h;
}
function alertFn() {
    alert("Već ste ulogovani sa drugog uređaja.")
    window.location.href = BASE_URL + "user/login";
    return;
}
/*function streamValidationCheck() {
    if (time != false) {
        reloadInterval = setInterval(function() {
            checkSession();
            //updateChannel();
        }, stat_interval * 1000);
    } else {
        clearInterval(reloadInterval);
    }
}
function checkSession() {
    jQuery.ajax({
        url: BASE_URL + 'ajax/check-session',
        type: 'POST',
        data: {
            channel: current_channel_id
        },
        dataType: "json",
        success: function(result) {
            if (result != false) {
                alertFn();
            }
        }
    });
}
function updateChannel() {
    jQuery.ajax({
        url: BASE_URL + 'ajax/update-channel',
        type: 'POST',
        data: {
            channel: current_channel_id
        },
        dataType: "json"
    });
}
function streamLoopTest() {
    timerHelper = timerHelper + 1;
    console.log(timerHelper);
}*/
function onJavaScriptBridgeCreated(playerId)
{
    if (player == null) {
        player = document.getElementById(playerId);

        if (checkHelper == null) {
            checkHelper = true;
            time = true;
            //streamValidationCheck();
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
        src: obj.url + "playlist.m3u8",
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

    var wmodeValue = "direct";
    var wmodeOptions = ["direct", "opaque", "transparent", "window"];
    if (parameters.hasOwnProperty("wmode"))
    {
        if (wmodeOptions.indexOf(parameters.wmode) >= 0)
        {
            wmodeValue = parameters.wmode;
        }
        delete parameters.wmode;
    }

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
        name: "StrobeMediaPlayback"
    }
    );
}

function playerInitialize(obj) {
    $("#webtv").width("100%");
    $("#webtv").height(resizeVideoContainer());
    fp3config = {
        key: '#$e3c796a02c1cb268250',
        buffering: false,
        logo: {
            fullscreenOnly: false,
            displayTime: 2000
        },
        clip: {
            scaling: 'fit',
            autoPlay: true,
            url: obj.url + "playlist.m3u8",
            ipadUrl: obj.url + "playlist.m3u8",
            urlResolvers: ["httpstreaming", "brselect"],
            provider: "httpstreaming",
            //image: obj.img,
            bitrates: [
                {
                    bitrate: 600,
                    label: "LOW",
                    sd: true
                },
                {
                    bitrate: 2500,
                    label: "HIGH",
                    hd: true
                },
            ],
            onStart: function(clip) {
                time = true;
                //streamValidationCheck();
            }
        },
        plugins: {
            menu: {
                url: BASE_URL + "js/flowplayer/flowplayer.menu-3.2.12.swf",
                items: [
                    {
                        label: "select bitrate:",
                        enabled: false
                    }
                ]
            },
            brselect: {
                url: BASE_URL + "js/flowplayer/flowplayer.bitrateselect-3.2.13.swf",
                menu: true
            },
            httpstreaming: {
                url: BASE_URL + "js/flowplayer/flowplayer.httpstreaminghls-3.2.10.swf"
            },
            controls: {
                url: BASE_URL + "js/flowplayer/flowplayer.controls-3.2.15.swf"
            }
        },
        onLoad: function() {
        }
    };
    $f("webtv", {
        src: BASE_URL + "js/flowplayer/flowplayer.commercial-3.2.16.swf",
        debug: true
    }, fp3config).ipad({
        simulateiDevice: simulate
    }); // sets up the player on iOS
    $f("webtv").onFullscreen(function() {
        this.setClip({
            scaling: "scale"
        });
        this.play();
    });
    $f("webtv").onFullscreenExit(function() {
        this.setClip({
            scaling: "fit"
        });
        this.play();
    });
}
function videoJsInitialize(obj) {
    var videoTag = "";
    var h = resizeVideoContainer();
    videoTag += '<div style="height:' + h + ';width:' + $("#webtv").width() + '">';
    videoTag += '<video class="video-js" width="100%" height="100%" preload="none" controls x-webkit-airplay="allow">';
    videoTag += '  <source src="' + obj.url + 'playlist.m3u8" type="application/vnd.apple.mpegurl" />';
    videoTag += '</video>';
    videoTag += '</div>';
    $("#webtv").html(videoTag);
    VideoJS.setupAllWhenReady();
}
function html5Video(obj) {
    var videoTag = "";
    var h = resizeVideoContainer();
    videoTag += '<div style="position:relative;height:' + h + ';width:' + $("#webtv").width() + '">';
    videoTag += '<video id="webtv_html5Video" style="position:relative;z-index:5" width="100%" height="' + h + '" preload="none" autoplay controls x-webkit-airplay="allow" src="' + obj.url + 'playlist.m3u8" poster="' + obj.img + '">';
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

    });
}
function directStream(obj) {
    var player = document.getElementById("webtv");
    var h = resizeVideoContainer();
    $("#webtv").removeClass('fp3');
    player.innerHTML = '<a href="' + obj.url + 'playlist.m3u8" style="height:' + h + 'px;display:block;width:100%;background:url(' + obj.img + ') no-repeat center center"></a>';
}

function noFlashFn() {
    $("#webtv").html("<div id='strobe' style='color:#fff;margin-top:15px;'><h2></h2><p></p></div>");
    $("#strobe h2").html("Flash verzija 10.1 ili veća je obavezna")
    $("#strobe p").html("Nemate instaliran flash plugin<br>Možete ga skinuti klikom na sledeći <a href='http://get.adobe.com/flashplayer/' target='_blank'>Link</a>");
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
    
    if (checkAndroidVersion() >= 4.0 && checkAndroidVersion() < 4.3) {
        html5Video(result);
        //streamValidationCheck();
        return;
    }
    if (checkAndroidVersion() >= 4.3) {
        directStream(result);
        //streamValidationCheck();
        return;
    }
    if (idevice) {
        html5Video(result);
        //streamValidationCheck();
        return;
    }
    $("#webtv").slideDown('fast', function() {
        playerStrobe(result);
    });
});

function validateForm(){
    var first_name = $('input[name="first_name"]').val();
    var last_name = $('input[name="last_name"]').val();
    var card_number = $('input[name="card_number"]').val();
    var zip_code = $('input[name="zip_code"]').val();
    var email = $('input[name="email"]').val();
    var expiration_month = $('input[name="expiration_month"]').val();
    var expiration_year = $('input[name="expiration_year"]').val();
    
    if(first_name == ''){
        alert('Enter first name!');
        return false;
    }

    if(last_name == ''){
        alert('Enter your last name!');
        return false;
    }

    if(card_number.length != 16){
        alert('Card number not valid!');
        return false;
    }

    if(zip_code.length < 4){
        alert('Zip code not valid!');
        return false;
    }

    if(!(/^\d+$/.test(card_number))){
        alert('Card number not valid, digits only!');
        return false;
    }

    if(!(/^\d+$/.test(zip_code))){
        alert('Zip code not valid, digits only!');
        return false;
    }

    if(expiration_month > 12 || expiration_month < 1){
        alert('Month in expiration date not valid!');
        return false;   
    }

    if(expiration_year < 2015){
        alert('Year in expiration date not valid!');
        return false;   
    }
    
    var email_regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!email_regex.test(email)){
        alert('Email address not valid!');
        return false;
    }

    return true;
}

$("input[id='fake-rating']").rating({
    'showClear':false,
    'disabled':true,
    'showCaption':false,
});