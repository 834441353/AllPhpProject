$(document).ready(function(){
	//顶部切换
	$(".navigation_area").mouseout(function(){
		$(this).removeClass("list_item_sel");
		$(".site_navigation").removeClass("navigation_open");
	})
	
	$(".navigation_area .list_item").mouseout(function(){
		$(this).removeClass("list_item_sel");
	}).mouseover(function(e){
		$(".site_navigation").addClass("navigation_open");
		$(this).addClass("list_item_sel");
	});	
	
	//播放地址切换
	$(".box_tt_tab h2").click(function(){
		var cTab = $(".box_tt_tab h2");
		cTab.removeClass();
		$(this).addClass("on");	
		var listData = $(this).attr("data");	
		$(".showplayul[style!='none']").hide("slow",function(){
			$(".box_con[style!='none']").css("display","none");
			$("#tip_"+listData).removeAttr("style");
			$("#list_"+listData).show("slow");
		});
	});

	//scroll
	var scrollTop = 0;
	$(window).scroll( function() {
		var currentTop = $(document).scrollTop();
		//向下
		if(currentTop >= scrollTop){
			if(currentTop > 130 && $(".site_head").css("display") != "none")
				$(".site_navigation").slideUp(100,function(){$(".site_head").slideUp(200);});				
		}
		else if($(".site_head").css("display") == "none"){
			$(".site_head").slideDown(200,function(){$(".site_navigation").slideDown(100);});
		}
		scrollTop = currentTop;
	});
});