var FF = {
    'Home': {
        'Url': document.URL,
        'Tpl': 'defalut',
        'Channel': '',
        'GetChannel': function($sid) {
            if ($sid == '1') return 'vod';
            if ($sid == '2') return 'news';
            if ($sid == '3') return 'special';
        },
			
		'Js': function() {
			//获取频道名
			this.Channel = this.GetChannel(Sid);
			//搜索默认关键字
			if($("#wd").length>0){ 
				//改变action目标地址
				if(Sid == '2'){
					$key = '输入关键字';
					$('#ffsearch').attr('action', Root+'index.php?s=news-search');
				}else{
					$key = '输入影片名称或主演名称';
				}
				//默认搜索框关键字
				if($('#wd').val() == ''){
					$('#wd').val($key);
				}
				//搜索框获得焦点
				$('#wd').focus(function(){
					if($('#wd').val() == $key){
						$('#wd').val('');
					}
				});
				//搜索框失去焦点
				$('#wd').blur(function(){
					if($('#wd').val() == ''){
						$('#wd').val($key);
					}
				});
			}	
			
            $("#fav").click(function() {
                var url = window.location.href;
                try {
                    window.external.addFavorite(url, document.title);
                } catch(err) {
                    try {
                        window.sidebar.addPanel(document.title, url, "");
                    } catch(err) {
                        alert("请使用Ctrl+D为您的浏览器添加书签！");
                    }
                }
            });
			$("img.lazy").error(function(){
				$(this).attr('src',Root +'Tpls/PPTV/nophoto.jpg');
			});
			$("img.bigimg").error(function(){
				$(this).attr('src',Root +'Tpls/PPTV/nophoto2.jpg');
			});
            $('.img_txt img,.pic_list_comm .pic_list img').hover(function() {
                $(this).css('border-color','#333333');
			},
            function() {
                $(this).css('border-color','#dddddd');
            });
        }
    },
    'UpDown': {
		'Vod': function($ajaxurl) {
			if($("#Up").length || $("#Down").length){
				this.Ajax($ajaxurl,'vod','');
			}
			$('.Up').click(function(){					
				FF.UpDown.Ajax($ajaxurl,'vod','up');
			})
			$('.Down').click(function(){
				FF.UpDown.Ajax($ajaxurl,'vod','down');
			})
		},
		'News': function($ajaxurl) {
			if($("#Digup").length || $("#Digdown").length){
				this.Ajax($ajaxurl,'news','');
			}else{
				FF.UpDown.Show($("#Digup_val").html()+':'+$("#Digdown_val").html(),'news');
			}
			$('.Digup').click(function(){
				FF.UpDown.Ajax($ajaxurl,'news','up');
			})
			$('.Digdown').click(function(){					
				FF.UpDown.Ajax($ajaxurl,'news','down');
			})
		},		
		'Ajax': function($ajaxurl,$model,$ajaxtype){
			$.ajax({
				type: 'get',
				url: $ajaxurl+'-type-'+$ajaxtype,
				timeout: 5000,
				dataType:'json',
				success:function($html){
					if(!$html.status){
						alert($html.info);
					}else{
						FF.UpDown.Show($html.data,$model);
						//if($ajaxtype){alert($html.info);}
					}
				}
			});
		},
		'Show': function ($html,$model){
			if($model == 'vod'){
				$(".Up>i").html($html.split(':')[0]);
				$(".Down>i").html($html.split(':')[1]);
			}else if($model = 'news'){
				var Digs = $html.split(':');
				var sUp = parseInt(Digs[0]);
				var sDown = parseInt(Digs[1]);
				var sTotal = sUp+sDown;
				var spUp=(sUp/sTotal)*100;
				spUp = Math.round(spUp*10)/10;
				var spDown = 100-spUp;
				spDown = Math.round(spDown*10)/10;
				if(sTotal!=0){
					$('#Digup_val').html(sUp);
					$('#Digdown_val').html(sDown);
					$('#Digup_sp').html(spUp+'%');
					$('#Digdown_sp').html(spDown+'%');
					$('#Digup_img').width(parseInt((sUp/sTotal)*55));
					$('#Digdown_img').width(parseInt((sDown/sTotal)*55));
				}				
			}
		}
	},


'Playlist': {
        'Show': function() {
            var $title = $("#playlistit a");
            var $content = $("#playlist .playlist");
            $title.mousemove(function() {
                var index = $title.index($(this));
                $(this).addClass("on").siblings().removeClass("on");
                $content.hide();
                $($content.get(index)).show();
                return false;
            });
        }
    },
    'Suggest': {
        'Show': function($id, $limit, $ajaxurl, $jumpurl) {
            $("#" + $id).autocomplete($ajaxurl, {
				width:'',
                scrollHeight: 320,
                minChars: 1,
                matchSubset: 1,
                max: $limit,
                cacheLength: 10,
                multiple: true,
                matchContains: true,
                autoFill: false,
                dataType: "json",
                parse: function(obj) {
                    if (obj.status) {
                        var parsed = [];
                        for (var i = 0; i < obj.data.length; i++) {
                            parsed[i] = {
                                data: obj.data[i],
                                value: obj.data[i].vod_name,
                                result: obj.data[i].vod_name
                            };
                        }
                        return parsed;
                    } else {
                        return {
                            data: '',
                            value: '',
                            result: ''
                        };
                    }
                },
                formatItem: function(row, i, max) {
                    return row.vod_name;
                },
                formatResult: function(row, i, max) {
                    return row.vod_name;
                }
            }).result(function(event, data, formatted) {
                location.href = $jumpurl + encodeURIComponent(data.vod_name);
            });
        }
    },
   //Cookie FF.Cookie.Set(name,value,days);
	'Cookie': {
		'Set': function(name,value,days){
			var exp = new Date();
			exp.setTime(exp.getTime() + days*24*60*60*1000);
			var arr=document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
			document.cookie=name+"="+escape(value)+";path=/;expires="+exp.toUTCString();
		},
		'Get': function(name){
			var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
			if(arr != null){
				return unescape(arr[2]);
				return null;
			}
		},
		'Del': function(name){
			var exp = new Date();
			exp.setTime(exp.getTime()-1);
			var cval = this.Get(name);
			if(cval != null){
				document.cookie = name+"="+escape(cval)+";path=/;expires="+exp.toUTCString();
			}
		}
	},
	//评论
	'Comment': {
		'Default': function($ajaxurl) {
			if($("#Comment").length>0){
				FF.Comment.Show($ajaxurl);
			}
		},
		'Show': function($ajaxurl) {
			$.ajax({
				type: 'get',
				url: $ajaxurl,
				timeout: 5000,
				error: function(){
					$("#Comment").html('评论加载失败...');
				},
				success:function($html){	
					$("#Comment").html($html);
				}
			});
		},
		'Post':function CommentPost(){
			if($("#comment_content").val() == '请在这里发表您的个人看法，最多200个字。'){
				$('#comment_tips').html('请发表您的评论观点！');
				return false;
			}
			var $data = "cm_sid="+Sid+"&cm_cid="+Id+"&cm_content="+$("#comment_content").val();
			$.ajax({
				type: 'post',
				url: Root+'index.php?s=Cm-insert',
				data: $data,
				dataType:'json',
				success:function($string){
					if($string.status == 1){
						FF.Comment.Show(Root+"index.php?s=Cm-Show-sid-"+Sid+"-id-"+Id+"-p-1");
					}
					alert($string.info);
				}
			});
		}
	}
}

var pagego = function($url,$total){
	$page = document.getElementById('page').value;
	if($page>0 && ($page<=$total)){
		$url=$url.replace('{!page!}',$page);
		if($url.split('index-1')){
			$url=$url.split('index-1')[0];
		}
		top.location.href = $url;
	}
	return false;
}

$(function(){$("a").focus(function(){$(this).blur()});$(".scrImg a").mouseover(function(){$(this).addClass("bf")}).mouseout(function(){$(this).removeClass("bf")});$(".jsonPP ul li:nth-child(4n)").addClass("r");$(".jsonPT ul li:nth-child(4n)").addClass("r");$(".sbox ul li:nth-child(2n)").addClass("r");$(".gbookList ul li:nth-child(2n)").addClass("r");$(".boxList.mtop ul li:nth-child(2n)").addClass("r");var videoInfo=$('.cont'),videoInfoDom=videoInfo.find('span').eq(1),oldInfo=videoInfoDom.html();videoInfo.find('.contMore').toggle(function(){$(this).html('[隐藏]');videoInfoDom.html(videoInfoDom.attr('txt'))},function(){$(this).html('[更多]');videoInfoDom.html(oldInfo)})});




//滚动开始
$.fn.ItemScroll=function(options,callback){var settings={BoxObjID:"",ItemConID:"",ItemConItem:"",ItemTabID:"",ItemTabItem:"",Timer:3000,Optrue:true,Rfunction:function(){}};if(options){jQuery.extend(settings,options)};var sWidth=$(settings.BoxObjID).width();var len=$(settings.ItemConID).find(settings.ItemConItem).length;var index=0;var picTimer;var showPics=function(index){var nowLeft=-index*sWidth;$(settings.BoxObjID).find(settings.ItemConID).stop(true,false).animate({"left":nowLeft},300,function(){settings.Rfunction($(settings.BoxObjID).find(settings.ItemConID).find("li:eq("+index+")"))});$(settings.BoxObjID).find(settings.ItemTabID).find(settings.ItemTabItem).removeClass("current").eq(index).addClass("current");if(settings.Optrue)$(settings.BoxObjID).find(settings.ItemTabID).find(settings.ItemTabItem).stop(true,false).animate({"opacity":"0.5"},300).eq(index).stop(true,false).animate({"opacity":"1"},300)};$(settings.BoxObjID).find(settings.ItemTabID).find(settings.ItemTabItem).mouseenter(function(){index=$(settings.BoxObjID).find(settings.ItemTabID).find(settings.ItemTabItem).index(this);showPics(index)}).eq(0).trigger("mouseenter");if(settings.Optrue){$(settings.BoxObjID).find('a.prev,a.next').hover(function(){$(this).stop().animate({opacity:1})},function(){$(this).stop().animate({opacity:0.2})})};$(settings.BoxObjID).find('a.prev').click(function(){index-=1;if(index==-1){index=len-1}showPics(index)});$(settings.BoxObjID).find('a.next').click(function(){index+=1;if(index==len){index=0}showPics(index)});$(settings.BoxObjID).find(settings.ItemConID).css("width",sWidth*(len));$(settings.BoxObjID).hover(function(){clearInterval(picTimer)},function(){picTimer=setInterval(function(){showPics(index);index++;if(index==len){index=0}},settings.Timer)}).trigger("mouseleave")};
//滚动结束


$(document).ready(function(){FF.Home.Js();FF.Playlist.Show();FF.Suggest.Show('wd',12,Root+'index.php?s=plus-search-vod',Root+'index.php?s=vod-search-wd-');FF.UpDown.Vod(Root+'index.php?s=Updown-'+FF.Home.Channel+'-id-'+Id);FF.UpDown.News(Root+'index.php?s=Updown-'+FF.Home.Channel+'-id-'+Id);FF.Comment.Default(Root+"index.php?s=Cm-Show-sid-"+Sid+"-id-"+Id+"-p-1");if($("#gold").length>0){$("#gold").studyplay_star({MaxStar:10,CurrentStar:0,Enabled:true,Ajaxurl:Root+'index.php?s=Gold-'+FF.Home.Channel+'-id-'+Id},function(value){})}$(".mov").hover(function(){$(this).addClass("movTop")},function(){$(this).removeClass("movTop")});var c=$(".imgBox .ext").parent();c.find(".pic").mouseover(function(){var d=$(this).parent();if(d.hasClass("extOpen")){if(b){clearTimeout(b)}}else{a=setTimeout(function(){d.addClass("extOpen")},400)}}).mouseout(function(){if(a){clearTimeout(a)}});c.mouseout(function(){var d=$(this);b=setTimeout(function(){d.removeClass("extOpen")},20)});$(".mtop li").hover(function(){$(this).addClass("gxTop")},function(){$(this).removeClass("gxTop")});var c=$(".gxt .gx").parent();c.find(".tit").mouseover(function(){var d=$(this).parent();if(d.hasClass("gxOpen")){if(b){clearTimeout(b)}}else{a=setTimeout(function(){d.addClass("gxOpen")},400)}}).mouseout(function(){if(a){clearTimeout(a)}});c.mouseout(function(){var d=$(this);b=setTimeout(function(){d.removeClass("gxOpen")},20)});$(".openBtn span").click(function(){$(".open").toggle(100);$(".openBtn").toggleClass("aon")});$(".lighton span.s1").click(function(){$html=$(this).html();try{if($html=='关灯'){$(this).html('开灯')}else{$(this).html('关灯')}}catch(e){}$(".ligopen").toggle(0);$(".lighton").toggleClass("son");$("#playBox").toggleClass("ztop")});$(".lighton span.s2").click(function(){$html=$(this).html();try{if($html=='关闭广告'){$(this).html('显示广告')}else{$(this).html('关闭广告')}}catch(e){}$(".adbox").toggleClass("adon");$(this).toggleClass("son")});$(".newsList li:last,.ztList li:last").addClass("nob");$(".mtop li:last,ul.UL1 li:last,ul.UL2 li:last,ul.UL3 li:last,ul.UL4 li:last,ul.UL5 li:last,ul.UL6 li:last,ul.UL7 li:last,ul.UL8 li:last,ul.UL9 li:last,ul.UL10 li:last,ul.UL11 li:last,ul.UL12 li:last,ul.UL13 li:last,ul.UL14 li:last,ul.UL15 li:last").addClass("nb");$('#rollTop').hide();$(window).scroll(function(){if($(window).scrollTop()>=500){$('#rollTop').fadeIn(500)}else{$('#rollTop').fadeOut(100)}});$('#rollTop').click(function(){$('html,body').animate({scrollTop:'0px'},500)});$('#rollBottom').click(function(){$('html,body').animate({scrollTop:$('#footer').offset().top},500)});$("#favmy").click(function(){AddMy()});$(".plMore").click(function(){$html=$(this).html();try{if($html=='展开列表'){$(this).html('收起列表');$(this).parent(".pmoreContain").attr('class','playMoreB pmoreContain');$(this).parent(".pmoreContain").prev(".all-plist").show()}else{$(this).html('展开列表');$(this).parent(".pmoreContain").attr('class','playMoreA pmoreContain');$(this).parent(".pmoreContain").prev(".all-plist").hide()}}catch(e){alert(e.message)}})});

function AddMy(){var url=window.location.href;try{window.external.addMyorite(url,document.title)}catch(err){try{window.sidebar.addPanel(document.title,url,"")}catch(err){alert("加入收藏失败，请使用Ctrl+D进行添加")}}};function SetHome(obj,vrl){try{obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl)}catch(e){if(window.netscape){try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect")}catch(e){alert("温馨提示:\n浏览器不允许网页设置首页。\n请手动进入浏览器选项设置主页。")}var prefs=Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);prefs.setCharPref('browser.startup.homepage',vrl)}}};

(function($){$.fn.charCount=function(options){var defaults={allowed:200,warning:20,css:'counter',counterElement:'span',counterEm:'em',cssWarning:'warning',cssExceeded:'exceeded',counterText:'剩余字数:',counterText1:'个字'};var options=$.extend(defaults,options);function calculate(obj){var count=$(obj).val().length;var available=options.allowed-count;if(available<=options.warning&&available>=0){$(obj).next().addClass(options.cssWarning)}else{$(obj).next().removeClass(options.cssWarning)}if(available<0){$(obj).next().addClass(options.cssExceeded)}else{$(obj).next().removeClass(options.cssExceeded)}$(obj).next().html(options.counterText+'<'+options.counterEm+'>'+available+'</'+options.counterEm+'>'+options.counterText1)};this.each(function(){$(this).after('<'+options.counterElement+' class="'+options.css+'">'+options.counterText+'</'+options.counterElement+'>');calculate(this);$(this).keyup(function(){calculate(this)});$(this).change(function(){calculate(this)})})}})(jQuery);

function toptiphtml(){var now,hour;now=new Date();hour=now.getHours();if(hour<5){$('#topTip').html("夜深了，请注意休息哦！"+SiteShortTips+"")}else if(hour<9){$('#topTip').html("早上好，欢迎访问"+SiteShortName+"！"+SiteShortTips+"")}else if(hour<12){$('#topTip').html("上午好，欢迎访问"+SiteShortName+"！"+SiteShortTips+"")}else if(hour<14){$('#topTip').html("中午好，欢迎访问"+SiteShortName+"！"+SiteShortTips+"")}else if(hour<18){$('#topTip').html("下午好，欢迎访问"+SiteShortName+"！"+SiteShortTips+"")}else if(hour<24){$('#topTip').html("晚上好，欢迎访问"+SiteShortName+"！"+SiteShortTips+"")}}var writeCookies=function(a,b,c){try{"number"==typeof c&&(c={exp:c});var d="object"==typeof c?c:{},e=new Date,f=d.exp?d.exp:1,g=d.path?d.path:"/",h=d.domain?d.domain:null,i=d.secure?d.secure:!1;f!=null&&f>=0&&e.setTime(e.getTime()+f*24*60*60*1*1e3),document.cookie=a+"="+b+(f==null||f<0?f==-1?";expires=-1":"":";expires="+e.toGMTString())+(g==null?"":"; path="+g)+(h==null?"":"; domain="+h)+(i==!0?"; secure":"")}catch(j){}},readCookies=function(a){try{var b=document.cookie.split("; ");for(var c=0;c<b.length;c++){var d=b[c].split("=");if(a==d[0])return d.length>1?unescape(d[1]):""}return""}catch(e){}},lvs=function(a,b){var c=jQuery,d=c(".plist");a==2?(d.find("span")[0].className="ltA",d.find("span")[1].className="lzB"):(d.find("span")[0].className="ltB",d.find("span")[1].className="lzA"),writeCookies("lvs",a,1)},lvsRead=function(){var a=jQuery,b=a(".plist"),c=readCookies("lvs")||2;c==1?jQuery("#lzA").click():jQuery("#ltA").click()};jQuery(function(){jQuery("#lzA").click(function(){var a=jQuery;a("#videoData").removeClass("jsonPP").addClass("jsonPT")}),jQuery("#ltA").click(function(){var a=jQuery;a("#videoData").removeClass("jsonPT").addClass("jsonPP")}),lvsRead()});

function boxopen(index){$(".operation .openbox").css("display","none");if(index==1){$("#sharebox").css("display","block");}if(index==2){$("#urlbox").css("display","block");}if(index==3){$("#errorbox").css("display","block");}$(".operation .off").click(function(){$(".operation .openbox").css("display","none");});}

function checkcookie(){}checkcookie();$(document).ready(function(){$(".historyA > li:odd").addClass("odd");$(".historyA > li:even").addClass("even");$(".historyA > li").hover(function(){$(this).addClass("active")},function(){$(this).removeClass("active")});$(".hisBox").hover(function(){$(this).find(".looked-box").show();$(this).find(".his-tip-show").hide()},function(){$(this).find(".looked-box").hide();$(this).find(".his-tip-show").show()});$(".close-his").click(function(){$(".his-tip-show").show();$(".looked-box").hide()});$(".show-tipinfo a").hover(function(){$(this).parent().parent().find(".tipInfo").show()},function(){$(this).parent().parent().find(".tipInfo").hide()});$(".slist li").hover(function(){$(this).addClass("over")},function(){$(this).removeClass("over")});$("#wish").trigger('click')});

$(document).ready(function(){$("#ffsearch").submit(function(){if($('#wd').val()=='输入影片名称或主演名称'){alert('请输入搜索关键字！');return false}if($('#wd').val()=='输入关键字'){alert('请输入搜索关键字！');return false}if($('#wd').val()==''){alert('请输入搜索关键字！');return false}});$("#sffsearch").submit(function(){if($('#swd').val()==''){alert('请输入搜索关键字！');return false}})});

function setContentTab(name,curr,n){for(i=1;i<=n;i++){var menu=document.getElementById(name+i);var cont=document.getElementById("con_"+name+"_"+i);menu.className=i==curr?"on":"";if(i==curr){cont.style.display="block";lazyloadForPart($(cont))}else{cont.style.display="none"}}};function lazyloadForPart(container){container.find('img').each(function(){var original=$(this).attr("original");if(original){$(this).attr('src',original).removeAttr('original')}})};

//获取搜索焦点 
$(document).ready(function(){$('.input,.btminput').focus(function(){$('.s1,.btmSearch').addClass('onfocus')});$('.input,.btminput').blur(function(){$('.s1,.btmSearch').removeClass('onfocus')});$('.cg').focus(function(){$('#search').addClass('allfocus')});$('.cg').blur(function(){$('#search').removeClass('allfocus')});$(".n1 a:last,.mTab a:last").addClass("noline");$(".lList li .pic,.lList1 li .pic").hover(function(){$(this).addClass("movTop")},function(){$(this).removeClass("movTop")});var c=$(".imgBox .ext").parent();c.find(".pic").mouseover(function(){var d=$(this).parent();if(d.hasClass("extOpen")){if(b){clearTimeout(b)}}else{a=setTimeout(function(){d.addClass("extOpen")},400)}}).mouseout(function(){if(a){clearTimeout(a)}});c.mouseout(function(){var d=$(this);b=setTimeout(function(){d.removeClass("extOpen")},20)});$('.topList .pic').hover(function(){$(".cover",this).stop().animate({bottom:'1px',opacity:1},{queue:false,duration:200})},function(){$(".cover",this).stop().animate({bottom:'30px',opacity:0},{queue:false,duration:200})});$(".taglist dd a").hover(function(){$(this).addClass("tgName")},function(){$(this).removeClass("tgName")});$(".topList li a.pic").hover(function(){$(this).addClass("movOn")},function(){$(this).removeClass("movOn")});$(".nav .navmore,.vodImg").hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover")})});

//$(function(){$(".taglist a").hover(function(){$(this).find(".tagTitle").animate({opacity:"show",top:"20"},"slow")},function(){$(this).find(".tagTitle").animate({opacity:"hide",top:"-50"},"fast")});$(".slist li").hover(function(){$(this).find(".specialBox").animate({"height":285},200);$(this).find("b").animate({"height":278,"bottom":-85},200);$(this).find("dd.sjj").animate({opacity:"show"},200)},function(){$(this).find(".specialBox").animate({"height":200},30);$(this).find("b").animate({"height":190,"bottom":1},30);$(this).find("dd.sjj").animate({opacity:"hide"},30)})});$(function(){responsive();$(window).resize(function(){responsive()});if($.browser.msie){$('html').addClass('ie')}else if($.browser.safari){$('html').addClass('safari')}else if($.browser.mozilla){$('html').addClass('firefox')}else{}$('#umbBtn li:last').addClass('ielast');$('.tagpic dd:last').addClass('dlast')});function responsive(){if($('body').width()>1600){$('html').removeClass('hd1024 hd1280 hd1440 hdall');$('html').addClass('hdall')}else if($('body').width()>1418){$('html').removeClass('hd1024 hd1280 hd1440 hdall');$('html').addClass('hd1440')}else if($('body').width()>1258){$('html').removeClass('hd1024 hd1280 hd1440 hdall');$('html').addClass('hd1280')}else if($('body').width()>1002){$('html').removeClass('hd1024 hd1280 hd1440 hdall');$('html').addClass('hd1024')}else if($('body').width()<1002){$('html').removeClass('hd1024 hd1280 hd1440 hdall');$('html').addClass('hd1024')}}