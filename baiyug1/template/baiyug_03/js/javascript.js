/*
* @Author: Administrator
* @Date:   2016-04-09 09:31:50
* @Last Modified by:   Administrator
* @Last Modified time: 2016-04-14 17:45:47
*/

jQuery(document).ready(function($) {
	addCarousel("new-movie");
});
function addCarousel(id) {
	var $car =  $("#"+id);
	var $tips = $car.parent().find(" .carousel-tips");
	$tips.on("click","span",function(event){
		$(this).addClass("active").siblings().removeClass("active");
		var i = $(this).parent().find("span").index($(this));
		$car.css({"left": -i*100+"%"});
		});
	
	$tips.find("span").eq(1).one("click",function(){
		$car.find(".movie-item").eq(1).find("img").each(function(){
			$(this).attr("src",$(this).data("src"));
		});
	});
	$tips.find("span").eq(2).one("click",function(){
		$car.find(".movie-item").eq(2).find("img").each(function(){
			$(this).attr("src",$(this).data("src"));
		});
	});
	var $button = $car.parent().prev().find(".car-btn");
		console.log($button);
		$button.on("click","button",function(event){
		var index = $(this).data("index");
		if( index==1) {
			$tips.find(".active").prev().trigger("click");
		} else {
			$tips.find(".active").next().trigger("click");
		}
	});
}