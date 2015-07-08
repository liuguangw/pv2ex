/**
 * 后台管理中心
 */
function switchNavbar(navbarId){
	var listItem=$("div.box>a.list-group-item"),i;
	for(i=0;i<listItem.length;i++){
		if(i!=navbarId)
			listItem[i].className="list-group-item";
		else
			listItem[i].className="list-group-item active";
	}
}
/** 
 * 根据导航栏目的序号，显示不同的界面
 *
 * @param int navid
 * @return void
 */
function showAdminForm(navid){
	if(navid==0){
		showAdminIndex();
	}
}
function bgSaveDb(){
	$.ajax({
		"type":"POST",
		"url":"/hadmin/bgSaveDb",
		"dataType":"json",
		"success":function(data){
			if(data.success){
				document.getElementById("saveTip").innerHTML="已发出保存命令";
			}
		}
	});
}
function showAdminIndex(){
	var sep20=document.createElement("div"),
		mainDiv=document.getElementById("Main"),
		boxElement=sep20.cloneNode();
	mainDiv.innerHTML='';
	sep20.className="sep20";
	mainDiv.appendChild(sep20);
	mainDiv.appendChild(boxElement);
	mainDiv.appendChild(sep20.cloneNode());
	boxElement.className="box";
	var boxElementContent=boxElement.cloneNode();
	mainDiv.appendChild(boxElementContent);
	boxElement.innerHTML='<div class="header">\
					<a href="/">'+sitename+'</a> <span class="chevron">&nbsp;›&nbsp;</span>\
					后台管理中心首页&nbsp;\
			</div>';
	$(boxElementContent).append("<div class=\"inner\">"+dbTypeStr+"服务器状态</div>");
	$.ajax({
		"type":"POST",
		"url":"/hadmin/siteStat",
		"dataType":"json",
		"success":function(data){
			var innerDiv=document.createElement("div");
				innerDiv.className="inner";
			var severInfoTb=document.createElement("table"),trTmp,tnode;
			trTmp=severInfoTb.insertRow();
			tnode=document.createElement("TH");
			tnode.innerHTML="状态属性名";
			trTmp.appendChild(tnode);
			tnode=tnode.cloneNode();
			tnode.innerHTML="状态值";
			trTmp.appendChild(tnode);
			for(key in data){
				trTmp=severInfoTb.insertRow();
				tnode=trTmp.insertCell(0);
				tnode.innerHTML=key;
				tnode=trTmp.insertCell(1);
				tnode.innerHTML=data[key];
			}
			innerDiv.appendChild(severInfoTb);
			boxElementContent.appendChild(innerDiv);
		},
		"error":function(){
			
		},
		"complete":function(){
			if(dbBgSave){
				$(boxElementContent).append("<hr/>");
				$(boxElementContent).append("<div class=\"inner\"><h2>"+dbTypeStr+"数据库保存</h2></div>");
				$(boxElementContent).append("<div class=\"inner\">\
					<button type=\"button\" onclick=\"bgSaveDb();\">保存数据库</button>\
					<span id=\"saveTip\" class=\"fade\">点击立刻保存数据</span></div>");
			}
		}
	});
}

/**
 * 显示站点设置
 */
function showAdminSets(){
	var sep20=document.createElement("div"),
		mainDiv=document.getElementById("Main"),
		boxElement=sep20.cloneNode();
	mainDiv.innerHTML='';
	sep20.className="sep20";
	mainDiv.appendChild(sep20);
	mainDiv.appendChild(boxElement);
	mainDiv.appendChild(sep20.cloneNode());
	boxElement.className="box";
	var boxElementContent=boxElement.cloneNode();
	mainDiv.appendChild(boxElementContent);
	boxElement.innerHTML='<div class="header">\
					<a href="/">'+sitename+'</a> <span class="chevron">&nbsp;›&nbsp;</span>\
					站点设置&nbsp;\
			</div>';
	$(boxElementContent).append("<div class=\"inner\">"+dbTypeStr+"服务器状态</div>");
	$.ajax({
		"type":"POST",
		"url":"/hadmin/siteSets",
		"dataType":"json",
		"success":function(data){
		},
		"error":function(){
			
		}
	});
}