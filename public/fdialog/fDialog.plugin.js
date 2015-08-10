/* 修改 fDialog 有一些默认配置 */
$.fDialog.alert.defaults.buttons = ['确定'];
$.fDialog.alert.defaults.title = ['系统提示'];



/* 入场退场动画插件 */

//入场
$.fDialog.extend.init.animated = function($obj,opts){
	if(typeof opts.animated == 'string'){
		$obj.addClass('animated '+opts.animated);
		setTimeout(function(){
			$obj.removeClass('animated '+opts.animated);
		},1000);
	}
}

//退场
$.fDialog.extend.remove.animated = function($obj,opts){
	if(typeof opts.removeAnimated == 'string' && (opts.ie==0||opts.ie>9)){
		var time = 900;
		var arr = opts.removeAnimated.split('|');
		if(arr.length>1){
			arr[1] = parseInt(arr[1]);
			time = !isNaN(arr[1])?arr[1]:time;
		}
		$obj.addClass('animated '+arr[0]);
		setTimeout(function(){
			$obj.fDialog('remove',true);
		},time);
		return false;
	}
	return true;
}

//拖拽插件
$.fDialog.extend.init.drag = function($obj,opts){
	if(opts.drag===true){
		var dragging = false;
		var iX, iY, oX, oY;
		var $header = $obj.find('.fdialog-header');
		var $loading = $obj.find('.fdialog-loading');
		var opacity = $loading.css('opacity');
		$header.mousedown(function(e) {
			dragging = true;
			$header.css('cursor','move');
			$loading.css('opacity',0).show();
			iX = e.clientX - $obj[0].offsetLeft;
			iY = e.clientY - $obj[0].offsetTop;
			$obj[0].setCapture && $obj[0].setCapture();
			return false;
		});
		document.onmousemove = function(e) {
			if(dragging){
				var e = e || window.event;
				oX = e.clientX - iX;
				oY = e.clientY - iY;
				
				oX = oX<0?0:oX;
				oY = oY<0?0:oY;
				
				$obj.css({"left":oX + "px", "top":oY + "px"});
				return false;
			}
		};
		$(document).mouseup(function(e){
			dragging = false;
			$obj[0].releaseCapture && $obj[0].releaseCapture();
			e.cancelBubble = true;
			$loading.css('opacity',opacity).hide();
			$header.css('cursor','auto');
			$obj.fDialog({
				x : oX,
				y : oY
			});
		})
	}		
}