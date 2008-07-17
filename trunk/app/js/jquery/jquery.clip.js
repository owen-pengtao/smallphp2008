	(function($){

		$.fn.customImg = function(limitWidth,targetId,targetWidth,targetHeight){
			var me = this;
			me.result = null;
			
			var img = new Image();
			img.src = $(this)[0].src;
			me.img = img;

			if(me.width()>limitWidth){me.width(limitWidth)}
			var rootObj = null,rootRectObj = null,rootImgObj = null,rootBackImgObj = $(this);
			var targetRootObj,targetDivObj,targeImgObj;

			/*---------------------------------------------------------------------------------------------Prepare*/	
			var targetWidth = targetWidth || 100,targetHeight = targetHeight || 100;					//生成图像大小
			var changePara = 3/5;																		//计算比例
			var rectWidth = targetWidth / changePara,rectHeight = targetHeight / changePara;			//裁剪窗口大小

			if(rectWidth > me.width() || rectHeight > me.height()){
				//alert("图片不合法");
				return me;
			}
			var rootOrigObj = $(this).parent();

			$(this).wrap("<div></div>");
			rootObj = $(this).parent();
			rootObj.css({
				position:"relative",
				width:rootBackImgObj[0].offsetWidth+"px",
				height:rootBackImgObj[0].offsetHeight+"px",
				float:"left"
			})

			rootBackImgObj.before(rootBackImgObj.clone());											//复制图片以裁剪后图片可视窗口
			rootBackImgObj.css("opacity","0.2")

			rootBackImgObj.prev().wrap("<div></div>")												//可视遮罩
			rootImgObj = rootBackImgObj.prev();
			rootImgObj.css({position:"absolute",left:"0",top:"0",overflow:"hidden"})

			rootImgObj.before('<div></div>')														//拖动窗口
			rootRectObj	= rootImgObj.prev();
			rootRectObj.css({
				width:rectWidth+"px",
				height:rectHeight+"px",
				border:"1px solid #F60",
				position:"absolute",
				left:"0",
				top:"0",
				zIndex:"999",
				cursor:"move"
			})


			var rootParam = RootParam();
			if(!targetId){
				rootObj.parent().append("<div></div>")
				targetRootObj = rootObj.next();
				targetRootObj.css({
					float:"left",
					border: "1px solid #000",
					marginLeft:"10px",
					overflow:"hidden"
				})
			}else{
				targetRootObj = $("#"+targetId)
				targetRootObj.css({overflow:"hidden",textAlign:"center"})
			}
			
			targetRootObj.append("<div><img src='"+img.src+"' /></div>")
			targetDivObj = targetRootObj.children().eq(0);
			targetDivObj.css({width:targetWidth + "px",height: targetHeight + "px",overflow:"hidden",position:"relative"})
			targetImgObj = targetDivObj.children().eq(0);
			targetImgObj.css("position","absolute")
			updateResult();
			
			var targetParam = TargetParam();
			rootRectObj.css({
				left:(rootParam.width - rectWidth) / 2 + "px",
				top:(rootParam.height - rectHeight) /2 + "px"
			})
			updateResult();

			/*---------------------------------------------------------------------------------------------Rect init*/
			var pointStyle = {zIndex:"9999",display:"block",width:"8px",height:"8px",lineHeight:"1px",overflow:"hidden",background:"#F60",position:"absolute",margin:"0",padding:"0"}
			for(var i=0;i<4;i++){
				var obj = rootRectObj;
				obj.append("<div class='"+i+"'></div>");
				var tmp = obj.children().eq(i);
				tmp.css(pointStyle);
				switch (i){
					case 0:
						tmp.css({left:"0",top:"0"});
						break;
					case 1:
						tmp.css({right:"0",top:"0"})
						break;	
					case 2:
						tmp.css({right:"0",bottom:"0"})
						break;
					case 3:
						tmp.css({left:"0",bottom:"0"})
						break;
					default:
				}
				obj.children().eq(i).mouseover(
					function(e){setCursor(this)}	
				)

				obj.children().eq(i).mousedown(
					function(e){
						var e = e || window.event;
						stopBubble(e);
						var me = this;
						
						addCapture(me);
						setCursor(me);
						document.onmousemove = function(event){
							
							var e = event || window.event;
							stopBubble(e);
							var node = rootRectObj[0];
							var newObj = calculateRectByTag(me,e);
							if(newObj.width <= rectWidth/2 || newObj.height <= rectHeight/2){}
							else{
								setPos(node,{x:newObj.x,y:newObj.y})
								setSize(node,{width:newObj.width,height:newObj.height});
							}
							updateResult();
						}
						document.onmouseup = function(event){stopAll(me)}
					}
				)

				obj.children().eq(i).mouseup(function(e){stopAll(this)})
			}
			rootRectObj.append("<div style='background:#000;width:98%;height:98%;opacity:0;filter:alpha(opacity=0)'></div>");

			$("div:last",rootRectObj).mousedown(
				function(e){
					var e = e || window.event;
					var me = this.parentNode;
					stopBubble(e);
					addCapture(me);
					setCursor(me);
					var origX = MouseEvent(e).x - rootParam.left - me.offsetLeft;
					var origY = MouseEvent(e).y - rootParam.top - me.offsetTop;
					document.onmousemove = function(e){
						var newPos = calculateRectByDrag(e,origX,origY);
						setPos(me,newPos);
						updateResult();
					}
					document.onmouseup = function(e){stopAll()}
				}
			)
			$("div:last",rootRectObj).mouseup(function(e){stopAll()})
			$("div:last",rootRectObj).blur(function(e){stopAll()})


			function calculateRectByTag(node,e){
				var e = e || window.event;
				var _width,_height,_left,_right;
				
				var rootRectParam = RootRectParam();
				var pos = MouseEvent(e);
				var mouseX = pos.x - rootParam.left,mouseY = pos.y - rootParam.top;
				if(mouseX < 0 || mouseY < 0 || mouseX >rootParam.width || mouseY > rootParam.height){stopAll(node)}
				switch (+node.className){
					case 0:
						_width	= rootParam.width - mouseX - rootRectParam.right;
						_height = rootParam.height - mouseY - rootRectParam.bottom;
						_width	= checkScale(_width,_height).width;
						_height = checkScale(_width,_height).height;
						_left	= rootParam.width - _width - rootRectParam.right;
						_top	= rootParam.height - _height - rootRectParam.bottom;
						break;
					case 1:
						_width	= mouseX - rootRectParam.left;
						_height = rootParam.height - mouseY - rootRectParam.bottom;
						_width	= checkScale(_width,_height).width;
						_height = checkScale(_width,_height).height;
						_left	= rootRectParam.left;
						_top	= rootParam.height - _height - rootRectParam.bottom;
						break;	
					case 2:
						_left = rootRectParam.left;
						_top = rootRectParam.top;
						_width = mouseX - _left;
						_height = mouseY - _top;
						_width = checkScale(_width,_height).width;
						_height = checkScale(_width,_height).height;
						break;
					case 3:
						_top	= rootRectParam.top;
						_width	= rootParam.width - mouseX - rootRectParam.right;
						_height = mouseY - _top;
						_width	= checkScale(_width,_height).width;
						_height = checkScale(_width,_height).height;
						_left	= rootParam.width - _width - rootRectParam.right;
						break;
					default:
				}
				_width = _width - rootRectParam.l - rootRectParam.r;
				_height = _height - rootRectParam.t - rootRectParam.b;
				if($.browser.msie){	_left = _left - parseInt(rootOrigObj.css("paddingLeft"))}
				//边界判断
				if(_left < 0){_left = 0}
				if(_top < 0){_top = 0}
				if(_width + _left - rootRectParam.r > rootParam.width){_width = rootParam.width - _left - rootRectParam.r}
				if(_height + _top - rootRectParam.b > rootParam.height){_height = rootParam.height - _top - rootRectParam.b}
				return {x:_left,y:_top,width:_width,height:_height}
			}

			function calculateRectByDrag(e,origX,origY){
				var e = e || window.event;
				var rootParam = RootParam();
				var rootRectParam = RootRectParam();
				var mouseX = MouseEvent(e).x - rootParam.left;
				var mouseY = MouseEvent(e).y - rootParam.top;
				if(mouseX > rootParam.width || mouseY > rootParam.height || mouseX < 0 || mouseY <0){stopAll()}
				var x = mouseX - origX < 0 ? 0:mouseX - origX;
				var y = mouseY - origY < 0 ? 0:mouseY - origY;
				if (x>rootParam.width - rootRectParam.width){x = rootParam.width - rootRectParam.width}
				if (y>rootParam.height - rootRectParam.height){y = rootParam.height - rootRectParam.height}
				return {x:x,y:y}
			}

			/*---------------------------------------------------------------------------------------------Stop All*/
			function stopAll(node){
				document.onmousemove = null;
				document.onmousedown = null;
				if(node){setCursor(node,"default");removeCapture(node);}
				removeCapture(rootRectObj[0]);
				updateResult();
			}
			function updateResult(img){
				var rootParam = RootParam(); 
				var rootRectParam = RootRectParam();
				var targetParam = TargetParam();

				var _width = rootRectParam.width + rootRectParam.left;
				var _height = rootRectParam.height + rootRectParam.top;
				var _x = rootRectParam.left;
				var _y = rootRectParam.top;
				if($.browser.msie){
					_width -= parseInt(rootOrigObj.css("paddingLeft"))
					_x -= parseInt(rootOrigObj.css("paddingLeft"));
				}
				
				rootImgObj.css({
					width:_width + "px",
					height:_height + "px",
					clip:"rect("+ _y +"px,auto,auto,"+ _x +"px"+")"
				})
				
				var para = targetWidth / rootRectParam.width;
				
				var _l = rootRectParam.left * para;
				var _t = rootRectParam.top * para;
				var _w = rootParam.width  * para;
				var _h = rootParam.height * para;
				targetImgObj.css({
					left: "-"+ _l + "px",
					top: "-" + _t + "px",
					width: _w + "px",
					height: _h + "px"
				})
				if($.browser.msie){
					_l -= parseInt(rootRectObj.css("borderLeftWidth"));
					if(_l < 0){_l=0}
				}
				//me.result = {left:parseInt(_l),top:parseInt(_t),width:parseInt(_w),height:parseInt(_h)}
				para = me.img.width / _w;
				me.result = {width:targetWidth * para,height:targetHeight * para,left: _l * para,top: _t * para}
			}
			/*---------------------------------------------------------------------------------------------util*/
			this.doSubmit = function(){
				var targetParam = TargetParam();
				return {width:parseInt(targetParam.css("width")),height:parseInt(targetParam.css("height"))}
			}

			//计算容器属性
			function RootParam(){
				var root = rootObj.get(0);
				var abs = toAbsPos(root);
				return {left:abs.left,top:abs.top,width:root.offsetWidth,height:root.offsetHeight}
				//return {left:root.screenLeft,top:root.screenTop,width:root.offsetWidth,height:root.offsetHeight}
			}
			function RootRectParam(){
				var rootParam = RootParam();
				var rect = rootRectObj.get(0);
				var _width = rect.offsetWidth;
				var _height = rect.offsetHeight;
				var _left = rect.offsetLeft;
				var _top = rect.offsetTop;
				var _right = rootParam.width - _width - _left;
				var _bottom = rootParam.height - _height - _top;

				return {
					width:_width, height:_height, left: _left, top: _top, right: _right, bottom:_bottom, 
					t:parseInt(rootRectObj.css("borderTopWidth")),
					r:parseInt(rootRectObj.css("borderRightWidth")),
					b:parseInt(rootRectObj.css("borderBottomWidth")),
					l:parseInt(rootRectObj.css("borderLeftWidth"))
				}
			}
			function RootImgParam(){
				var img = rootImgObj.get(0);
				return {left:img.offsetLeft,top:img.offsetTop,width:rootImgObj.width(),height:rootImgObj.height()}
			}
			
			function TargetParam(){
				var rootParam = RootParam();
				var rootRectParam = RootRectParam();
				var _width = rootParam.width / rootRectParam.width * targetWidth;
				var _height = rootParam.height / rootRectParam.height * targetHeight;
				var _rect1 = rootParam.height / _height * rootRectParam.top;
				var _rect2 = rootParam.width / _width * rootRectParam.left;
				return {width:_width,height:_height,rect1:_rect1,rect2:_rect2}
			}
			//其他
			function checkScale(_width,_height){
				//比例调整
				var para = rectWidth/rectHeight;
				if(_width / _height < para){
					_height = _width / para;
				}else{
					_width = _height * para;
				}
				return {width:_width,height:_height}
			}
			function setCursor(node,flag){
				var jObj = $(node);
				if(flag == "default"){
					jObj.css({cursor:"default"})
				}else{
					switch (node.className){
						case "0":
							jObj.css({cursor:"nw-resize"})
							break;
						case "1":
							jObj.css({cursor:"ne-resize"})
							break;
						case "2":
							jObj.css({cursor:"se-resize"})
							break
						case "3":
							jObj.css({cursor:"sw-resize"})
							break
						default:
							jObj.css({cursor:"move"})
							break
					}
				}
			}
			function setPos(node,pos){
				$(node).css({left:pos.x+"px",top:pos.y+"px"})
			}
			function setSize(node,size){
				$(node).css({width:size.width+"px",height:size.height+"px"})
			}
			function MouseEvent(e){
				if(navigator.appName.indexOf("Explorer") > -1){
					return {x:e.clientX + document.documentElement.scrollLeft - document.documentElement.clientLeft,y:e.clientY + document.documentElement.scrollTop  - document.documentElement.clientTop}
				}else{
					return {x:e.pageX,y:e.pageY}
				}
			}
			function toAbsPos(e){
				var _y		= e.offsetTop;
				var _x	= e.offsetLeft;
				while(e=e.offsetParent){
					_y +=e.offsetTop;
					_x +=e.offsetLeft;
				}
				return {left:_x,top:_y}
			} 
			function stopBubble(e){
				var e = e || window.event;
				if (window.event){
					e.cancelBubble = true;
				}else{
					e.stopPropagation();
				}
			}
			function addCapture(obj){
				if(navigator.appName.indexOf("Explorer") > -1){obj.setCapture();}
			}
			function removeCapture(obj){
				if(navigator.appName.indexOf("Explorer") > -1){obj.releaseCapture();}
			}

			return this;
		}
	})(jQuery);


