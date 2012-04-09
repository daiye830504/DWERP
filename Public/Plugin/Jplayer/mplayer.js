/*
* Mplayer
*/

(function($) {
	$.jPlayerCount = 0;
	
	var methods = {
		jPlayer: function(options) {
			$.jPlayerCount++;
			
			var config = {
				ready: null,
				cssPrefix: "jqjp",
				swfPath: "",
				quality: "high",
				width: 0,
				height: 0,
				top: 0,
				left: 0,
				position: "absolute",
				bgcolor: "#ffffff"
			};

			$.extend(config, options);

			var configWithoutOptions = {
				id: $(this).attr("id"),
				swf: config.swfPath + ((config.swfPath != "") ? "/" : "") + "Jplayer.swf",
				fid: config.cssPrefix + "_flash_" + $.jPlayerCount,
				hid: config.cssPrefix + "_force_" + $.jPlayerCount
			};
			
			$.extend(config, configWithoutOptions);

			$(this).data("jPlayer.config", config);
			
			var events = {
				change: function(e, f) {
					var fid = $(this).data("jPlayer.config").fid;
					var m = $(this).data("jPlayer.getMovie")(fid);
					m.fl_change_mp3(f);
					$(this).trigger("jPlayer.screenUpdate", false);
				},
				play: function(e) {
					var fid = $(this).data("jPlayer.config").fid;
					var m = $(this).data("jPlayer.getMovie")(fid);
					var r = m.fl_play_mp3();
					if(r) {
						$(this).trigger("jPlayer.screenUpdate", true);
					}
					
				},
				pause: function(e) {
					var fid = $(this).data("jPlayer.config").fid;
					var m = $(this).data("jPlayer.getMovie")(fid);
					var r = m.fl_pause_mp3();
					if(r) {
						$(this).trigger("jPlayer.screenUpdate", false);
					}
				},
				stop: function(e) {
					var fid = $(this).data("jPlayer.config").fid;
					var m = $(this).data("jPlayer.getMovie")(fid);
					var r = m.fl_stop_mp3();
					if(r) {
						$(this).trigger("jPlayer.screenUpdate", false);
					}
				},
				playHead: function(e, p) {
					var fid = $(this).data("jPlayer.config").fid;
					var m = $(this).data("jPlayer.getMovie")(fid);
					var r = m.fl_play_head_mp3(p);
					if(r) {
						$(this).trigger("jPlayer.screenUpdate", true);
					}
				},
				volume: function(e, v) {
					var fid = $(this).data("jPlayer.config").fid;
					var m = $(this).data("jPlayer.getMovie")(fid);
					m.fl_volume_mp3(v);
				},
				screenUpdate: function(e, playing) {
					var playId = $(this).data("jPlayer.cssId.play");
					var pauseId = $(this).data("jPlayer.cssId.pause");
					var prefix = $(this).data("jPlayer.config").cssPrefix;

					if(playId != null && pauseId != null) {
						if(playing) {
							var style = $(this).data("jPlayer.cssDisplay.pause");
							$("#"+playId).css("display", "none");
							$("#"+pauseId).css("display", style);
						} else {
							var style = $(this).data("jPlayer.cssDisplay.play");
							$("#"+playId).css("display", style);
							$("#"+pauseId).css("display", "none");
						}
					}
				}
			};
			
			for(var event in events) {
				var e = "jPlayer." + event;
				$(this).unbind(e);
				$(this).bind(e, events[event]);
			}

			var getMovie = function(fid) {
				if ($.browser.msie) {
					return window[fid];
				} else {
					return document[fid];
				}
			};
			$(this).data("jPlayer.getMovie", getMovie);
			
			if($.browser.msie) {
				var html_obj = '<object id="' + config.fid + '"';
				html_obj += ' classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"';
				html_obj += ' codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab"';
				html_obj += ' type="application/x-shockwave-flash"';
				html_obj += ' width="' + config.width + '" height="' + config.height + '">';
				html_obj += '</object>';
			
				var obj_param = new Array();
				obj_param[0] = '<param name="movie" value="' + config.swf + '" />';
				obj_param[1] = '<param name="quality" value="high" />';
				obj_param[2] = '<param name="FlashVars" value="id=' + escape(config.id) + '&fid=' + escape(config.fid) + '" />';
				obj_param[3] = '<param name="allowScriptAccess" value="always" />';
				obj_param[4] = '<param name="bgcolor" value="' + config.bgcolor + '" />';
				
				var ie_dom = document.createElement(html_obj);
				for(var i=0; i < obj_param.length; i++) {
					ie_dom.appendChild(document.createElement(obj_param[i]));
				}
				$(this).html(ie_dom);
			} else {
				var html_embed = '<embed name="' + config.fid + '" src="' + config.swf + '"';
				html_embed += ' width="' + config.width + '" height="' + config.height + '" bgcolor="' + config.bgcolor + '"';
				html_embed += ' quality="high" FlashVars="id=' + escape(config.id) + '&fid=' + escape(config.fid) + '"';
				html_embed += ' allowScriptAccess="always"';
				html_embed += ' type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
				$(this).html(html_embed);
			}
			
			var html_hidden = '<div id="' + config.hid + '"></div>';
			$(this).append(html_hidden);
			
			$(this).css({'position':config.position, 'top':config.top, 'left':config.left});
			$("#"+config.hid).css({'text-indent':'-9999px'});
			
			return $(this);
		},
		change: function(f) {
			$(this).trigger("jPlayer.change", f);
		},
		play: function() {
			$(this).trigger("jPlayer.play");
		},
		changeAndPlay: function(f) {
			$(this).trigger("jPlayer.change", f);
			$(this).trigger("jPlayer.play");
		},
		pause: function() {
			$(this).trigger("jPlayer.pause");
		},
		stop: function() {
			$(this).trigger("jPlayer.stop");
		},
		playHead: function(p) {
			$(this).trigger("jPlayer.playHead", p);
		},
		volume: function(v) {
			$(this).trigger("jPlayer.volume", v);
		},
		jPlayerId: function(fn, id) {
			if(id != null) {
				var isValid = eval("$(this)."+fn);
				if(isValid != null) {
					$(this).data("jPlayer.cssId." + fn, id);
					var jPlayerId = $(this).data("jPlayer.config").id;
					eval("var myHandler = function(e) { $(\"#" + jPlayerId + "\")." + fn + "(e); return false; }");
					$("#"+id).click(myHandler).hover(this.rollOver, this.rollOut).data("jPlayerId", jPlayerId);
					
					var display = $("#"+id).css("display");
					$(this).data("jPlayer.cssDisplay." + fn, display);
					
					if(fn == "pause") {
						$("#"+id).css("display", "none");
					}
				} else {
					alert("Unknown function assigned in: jPlayerId( fn="+fn+", id="+id+" )");
				}
			} else {
				id = $(this).data("jPlayer.cssId." + fn);
				if(id != null) {
					return id;
				} else {
					alert("Unknown function id requested: jPlayerId( fn="+fn+" )");
					return false;
				}
			}
		},
		loadBar: function(e) {
			var lbId = $(this).data("jPlayer.cssId.loadBar");
			if( lbId != null ) {
				var offset = $("#"+lbId).offset();
				var x = e.pageX - offset.left;
				var w = $("#"+lbId).width();
				var p = 100*x/w;
				$(this).playHead(p);
			}
		},
		playBar: function(e) {
			this.loadBar(e);
		},
		onProgressChange: function(fn) {
			$(this).data("jPlayer.jsFn.onProgressChange", fn);
		},
		updateProgress: function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) { // Called from Flash
			var lbId = $(this).data("jPlayer.cssId.loadBar");
			if (lbId != null) {
				$("#"+lbId).width(loadPercent+"%");
			}
			var pbId = $(this).data("jPlayer.cssId.playBar");
			if (pbId != null ) {
				$("#"+pbId).width(playedPercentRelative+"%");
			}
			var onProgressChangeFn = $(this).data("jPlayer.jsFn.onProgressChange");
			if(onProgressChangeFn != null) {
				onProgressChangeFn(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime);
			}
			if (lbId != null || pbId != null || onProgressChangeFn != null) {
				this.forceScreenUpdate();
				return true;
			} else {
				return false;
			}
		},
		volumeMin: function() {
			$(this).volume(0);
		},
		volumeMax: function() {
			$(this).volume(100);
		},
		volumeBar: function(e) {
			var vbId = $(this).data("jPlayer.cssId.volumeBar");
			if( vbId != null ) {
				var offset = $("#"+vbId).offset();
				var x = e.pageX - offset.left;
				var w = $("#"+vbId).width();
				var p = 100*x/w;
				$(this).volume(p);
			}
		},
		volumeBarValue: function(e) {
			this.volumeBar(e);
		},
		updateVolume: function(v) { // Called from Flash
			var vbvId = $(this).data("jPlayer.cssId.volumeBarValue");
			if( vbvId != null ) {
				$("#"+vbvId).width(v+"%");
				this.forceScreenUpdate();
				return true;
			}
		},
		onSoundComplete: function(fn) {
			$(this).data("jPlayer.jsFn.onSoundComplete", fn);
		},
		finishedPlaying: function() { // Called from Flash
			var onSoundCompleteFn = $(this).data("jPlayer.jsFn.onSoundComplete");
			$(this).trigger("jPlayer.screenUpdate", false);
			if(onSoundCompleteFn != null) {
				onSoundCompleteFn();
				return true;
			} else {
				return false;
			}
		},
		setBufferState: function (b) { // Called from Flash
			var lbId = $(this).data("jPlayer.cssId.loadBar");
			if( lbId != null ) {
				var prefix = $(this).data("jPlayer.config").cssPrefix;
				if(b) {
					$("#"+lbId).addClass(prefix + "_buffer");
				} else {
					$("#"+lbId).removeClass(prefix + "_buffer");
				}
				return true;
			} else {
				return false;
			}
		},
		bufferMsg: function() {
			// Empty: Initialized to enable jPlayerId() to work.
			// See setBufferMsg() for code.
		},
		setBufferMsg: function (msg) { // Called from Flash
			var bmId = $(this).data("jPlayer.cssId.bufferMsg");
			if( bmId != null ) {
				$("#"+bmId).html(msg);
				return true;
			} else {
				return false;
			}
		},
		forceScreenUpdate: function() { // For Safari and Chrome
			var hid = $(this).data("jPlayer.config").hid;
			$("#"+hid).html(Math.random());
		},
		rollOver: function() {
			var jPlayerId = $(this).data("jPlayerId");
			var prefix = $("#"+jPlayerId).data("jPlayer.config").cssPrefix;
			$(this).addClass(prefix + "_hover");
		},
		rollOut: function() {
			var jPlayerId = $(this).data("jPlayerId");
			var prefix = $("#"+jPlayerId).data("jPlayer.config").cssPrefix;
			$(this).removeClass(prefix + "_hover");
		},
		flashReady: function() { // Called from Flash
			var readyFn = $(this).data("jPlayer.config").ready;
			if(readyFn != null) {
				readyFn();
			}
		}
	};

	$.each(methods, function(i) {
		$.fn[i] = this;
	});
})(jQuery);


/**
 * 滚屏代码 
 */
jQuery.fn.extend({
	mousewheel: function(up, down, preventDefault) {
		return this.hover(
			function() {
				jQuery.event.mousewheel.giveFocus(this, up, down, preventDefault);
			},
			function() {
				jQuery.event.mousewheel.removeFocus(this);
			}
		);
	},
	mousewheeldown: function(fn, preventDefault) {
		return this.mousewheel(function(){}, fn, preventDefault);
	},
	mousewheelup: function(fn, preventDefault) {
		return this.mousewheel(fn, function(){}, preventDefault);
	},
	unmousewheel: function() {
		return this.each(function() {
			jQuery(this).unmouseover().unmouseout();
			jQuery.event.mousewheel.removeFocus(this);
		});
	},
	unmousewheeldown: jQuery.fn.unmousewheel,
	unmousewheelup: jQuery.fn.unmousewheel
});


jQuery.event.mousewheel = {
	giveFocus: function(el, up, down, preventDefault) {
		if (el._handleMousewheel) jQuery(el).unmousewheel();
		
		if (preventDefault == window.undefined && down && down.constructor != Function) {
			preventDefault = down;
			down = null;
		}
		
		el._handleMousewheel = function(event) {
			if (!event) event = window.event;
			if (preventDefault)
				if (event.preventDefault) event.preventDefault();
				else event.returnValue = false;
			var delta = 0;
			if (event.wheelDelta) {
				delta = event.wheelDelta/120;
				if (window.opera) delta = -delta;
			} else if (event.detail) {
				delta = -event.detail/3;
			}
			if (up && (delta > 0 || !down))
				up.apply(el, [event, delta]);
			else if (down && delta < 0)
				down.apply(el, [event, delta]);
		};
		
		if (window.addEventListener)
			window.addEventListener('DOMMouseScroll', el._handleMousewheel, false);
		window.onmousewheel = document.onmousewheel = el._handleMousewheel;
	},
	
	removeFocus: function(el) {
		if (!el._handleMousewheel) return;
		
		if (window.removeEventListener)
			window.removeEventListener('DOMMouseScroll', el._handleMousewheel, false);
		window.onmousewheel = document.onmousewheel = null;
		el._handleMousewheel = null;
	}
};

/*
*	功能设置代码
*/
$(function(){
	$("#jquery_jplayer").jPlayer({
		swfPath:ROOT+"/Public/Plugin/Jplayer"			
	});

	$("#jquery_jplayer").jPlayerId("play", "player_play");
	$("#jquery_jplayer").jPlayerId("pause", "player_pause");
	$("#jquery_jplayer").jPlayerId("stop", "player_stop");
	$("#jquery_jplayer").jPlayerId("loadBar", "player_progress_load_bar");
	$("#jquery_jplayer").jPlayerId("playBar", "player_progress_play_bar");
	$("#jquery_jplayer").jPlayerId("volumeMin", "player_volume_min");
	$("#jquery_jplayer").jPlayerId("volumeMax", "player_volume_max");
	$("#jquery_jplayer").jPlayerId("volumeBar", "player_volume_bar");
	$("#jquery_jplayer").jPlayerId("volumeBarValue", "player_volume_bar_value");
	 
	$("#jquery_jplayer").onProgressChange( function(loadPercent, playedPercentRelative, playedPercentAbsolute, playedTime, totalTime) {
		var myPlayedTime = new Date(playedTime);
		//var ptMin = (myPlayedTime.getMinutes() < 10) ? "0" + myPlayedTime.getMinutes() : myPlayedTime.getMinutes();
		//var ptSec = (myPlayedTime.getSeconds() < 10) ? "0" + myPlayedTime.getSeconds() : myPlayedTime.getSeconds();
		var myTotalTime = new Date(totalTime);
		//var ttMin = (myTotalTime.getMinutes() < 10) ? "0" + myTotalTime.getMinutes() : myTotalTime.getMinutes();
		//var ttSec = (myTotalTime.getSeconds() < 10) ? "0" + myTotalTime.getSeconds() : myTotalTime.getSeconds();
		var secAll = dateDiff("S",myPlayedTime,myTotalTime);
		var sec = "00";
		var min = "00";
		//console.log( parseInt(secAll%60)<10  );
		if( parseInt(secAll%60)<10 ){
			sec = "0"+  parseInt(secAll%60);
		}else{
            sec = parseInt(secAll%60)
		}
		if((Math.floor(secAll/60)) <10 ){
			min = "0"+  (Math.floor(secAll/60));
		}else{
			min = (Math.floor(secAll/60));
		}
		$("#play_time").text(min+":"+sec);
	});
	/*
	使用方法: alert(dateDiff('h','2007-4-14','2007-4-19'));
	H 表示 hour , D 表示 day , M 表示minute , S 表示 second
	支持 豪秒，秒，分，时，天
	*/
	function dateDiff(interval, date1, date2){
			 var objInterval = {'D' : 1000*60*60*24,'H' : 1000*60*60,'M' : 1000*60,'S' : 1000};
			 interval = interval.toUpperCase();
			// var dt1 = Date.parse(date1.replace(/-/g,'/'));
			// var dt2 = Date.parse(date2.replace(/-/g,'/'));
			 var dt1 = date1;
			 var dt2 = date2;
			 try
			 {
				   return Math.round((dt2 - dt1) / eval('(objInterval.' + interval + ')'));
			 }catch(e)
			 {
				   return e.message;
			 }
	}
	$("#jquery_jplayer").onSoundComplete(endOfSong);
	function endOfSong() {
		playListNext();
	}

	$("#ctrl_prev").click( function() {
		playListPrev();
		return false;
	});
 
	$("#ctrl_next").click( function() {
		playListNext();
		return false;
	});
 
	function playListChange( src ,obj) {
		$("#player_progress,#play_time").remove();
		$('<div id="play_time"></div><div id="player_progress"><div id="player_progress_load_bar" class="jqjp_buffer"><div id="player_progress_play_bar"></div></div></div>').prependTo(obj);
		$(".playlist_content li a").removeClass("controllinkpause").addClass("controllink");
		$("a.controllink",obj).addClass("controllinkpause");
		$("#jquery_jplayer").changeAndPlay(src);
	}
	
	var playItem = 0;
	var myPlayListLength = $(".playlist_content li").length;
	function playListNext() {
		var currentIndex = $(".playlist_content li").index($(".playlist_current"));
		playItem = currentIndex;
		var index = (playItem+1 < myPlayListLength) ? playItem+1 : 0;
		var $liindex = $(".playlist_content li:eq("+index+")") ;
		var playListSrc= $liindex.attr("jplayer");
		playListChange(playListSrc , $liindex );//播放mp3
		$liindex.addClass("playlist_current").siblings().removeClass("playlist_current");
	}
 
	function playListPrev() {
		var currentIndex = $(".playlist_content li").index($(".playlist_current"));
		playItem = currentIndex;	
		var index = (playItem-1 >= 0) ? playItem-1 :myPlayListLength-1;
		var $liindex = $(".playlist_content li:eq("+index+")") ;
		var playListSrc= $liindex.attr("jplayer");
		playListChange(playListSrc , $liindex );//播放mp3
		$liindex.addClass("playlist_current").siblings().removeClass("playlist_current");
	}
	function play_zhijie() {
		var $liindex = $(".playlist_content li:eq(0)");
		var playListSrc= $liindex.attr("jplayer");
		playListChange(playListSrc , $liindex );//播放mp3
		$liindex.addClass("playlist_current").siblings().removeClass("playlist_current");
	}

	/*音乐列表*/
	$("#playlist_list ul.playlist_content li").click(function(){
		var src = $(this).attr("jplayer");
		if(!$(this).hasClass("playlist_current")) {
			$('#jquery_jplayer').pause(); 
			playListChange(src , $(this));//播放mp3
		}else{
			if(	$('#jquery_jplayer').data("flag") ){
				$("a",$(this)).removeClass("controllink controllinkpause").addClass("controllinkpause");
				$('#jquery_jplayer').play(); 
				$('#jquery_jplayer').data("flag",false);
			}else{
				$("a",$(this)).removeClass("controllink controllinkpause").addClass("controllink");
				$('#jquery_jplayer').pause(); 
				$('#jquery_jplayer').data("flag",true);
			}
		}
		$(this).addClass("playlist_current").siblings().removeClass("playlist_current");
	}).hover(function() {
		if (!$(this).hasClass("playlist_current")) {
			$(this).addClass("playlist_hover");
		}
	},function(){
		$(this).removeClass("playlist_hover");
	});

	/*歌曲名 自动滚屏*/
	var songerTime = "";
	var marginLeftWidth = $(".playlist_content_songer_txt").parent().width();
	function setTime() {
		$(".playlist_content_songer_txt").animate({marginLeft:"-=1px"},0,function(){
			if(Math.abs(parseInt($(this).css("marginLeft"))) >= marginLeftWidth){
				$(this).css("marginLeft",marginLeftWidth+"px");
			}
		});
	}
	$(".playlist_content_songer_txt").parent().hover(function(){
		if(songerTime){ clearInterval(songerTime);}
	},function(){
		songerTime = setInterval(function(){
			setTime();
		},30);
	});
	
	$(".list_reuturn a").click(function(){
		//停止歌曲名自动滚屏
		$(".playlist_content_songer_txt").parent().trigger("mouseenter");
		//切换到歌曲列表
		$(".playlist_wrap").animate({left:"20px"},500,function(){
			$(".playlist_footer li.list_power").fadeOut(200,function(){
				$(".playlist_footer li:not(.list_power)").fadeIn(200);
			});
		});
		return false;
	});

	//暂停后 播放音乐
	$("#player_play a").click(function(){
		$('#jquery_jplayer').play(); 
		return false;
	});
	//暂停音乐
	$("#player_pause a").click(function(){
		$('#jquery_jplayer').pause(); 
		return false;
	});

	/*上下翻*/
	var len = $(".playlist_content li").length;
	var per = 8;
	var num = Math.ceil(len/8);
	var i = 1;
	var height_top = $(".playlist_cc").outerHeight(true);
	//下翻
	$(".list_down a").click(function(){
		if(!$('.playlist_content').is(":animated")){
			if(i>=num){
				return false;
			}else{ 
				$('.playlist_content').animate({top:"-="+height_top+"px"},600);
			}
			i++;
		}
		return false;
	});
	//上翻
	$(".list_up a").click(function(){
		if(!$('.playlist_content').is(":animated")){
			if(i<=1){
				return false;
			}else{
				$('.playlist_content').animate({top:"+="+height_top+"px"},600);
			}
			i--;
		}
		return false;
	});
	/*鼠标滚轮事件*/
	$(".playlist_cc").mousewheel(function(objEvent, intDelta){
		if (intDelta > 0){
		   $(".list_up a").trigger("click");
		}else if (intDelta < 0){
		   $(".list_down a").trigger("click");
		}
	});
	//随便听听
	$(".list_ramdom a").click(function(){
		var index = Math.round(Math.random()*len);
		if(index<=len){
			$("#playlist_list ul.playlist_content li:eq("+index+")").trigger("click");
		}else{
			alert("暂时不支持随机播放!请稍后再试!");
		}
		return false;
	});
	/*上一首*/
	$(".list_up_one").click(function(){
		playListPrev();
		return false;
	});
	
	/*下一首*/
	$(".list_down_one").click(function(){
		playListNext();
		return false;
	});
	/*直接播放*/
	$(".list_play_zhi").click(function(){
		play_zhijie();
		return false;
	});
});