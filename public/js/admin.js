/**
 * 后台管理中心
 */
function switchNavbar(navbarId) {
	var listItem = $("div.box>a.list-group-item"), i;
	for (i = 0; i < listItem.length; i++) {
		if (i != navbarId)
			listItem[i].className = "list-group-item";
		else
			listItem[i].className = "list-group-item active";
	}
}
/**
 * 根据导航栏目的序号，显示不同的界面
 * 
 * @param int
 *            navid
 * @return void
 */
function showAdminForm(navid) {
	if (navid == 0)
		showAdminIndex();
	else if (navid == 1)
		showAdminSets();
	else if (navid == 2)
		showBkTree();
	else if (navid == 3)
		showUserlist();
}
function bgSaveDb() {
	$.ajax({
		"type" : "POST",
		"url" : "/hadmin/bgSaveDb",
		"dataType" : "json",
		"success" : function(data) {
			if (data.success) {
				document.getElementById("saveTip").innerHTML = "已发出保存命令";
			}
		}
	});
}
function showAdminIndex() {
	var sep20 = document.createElement("div"), mainDiv = document
			.getElementById("Main"), boxElement = sep20.cloneNode();
	mainDiv.innerHTML = '';
	sep20.className = "sep20";
	mainDiv.appendChild(sep20);
	mainDiv.appendChild(boxElement);
	mainDiv.appendChild(sep20.cloneNode());
	boxElement.className = "box";
	var boxElementContent = boxElement.cloneNode();
	mainDiv.appendChild(boxElementContent);
	boxElement.innerHTML = '<div class="header">\
					<a href="/">'
			+ sitename
			+ '</a> <span class="chevron">&nbsp;›&nbsp;</span>\
					后台管理中心首页&nbsp;\
			</div>';
	$(boxElementContent).append(
			"<div class=\"inner\">" + dbTypeStr + "服务器状态</div>");
	$
			.ajax({
				"type" : "POST",
				"url" : "/hadmin/siteStat",
				"dataType" : "json",
				"success" : function(data) {
					var innerDiv = document.createElement("div");
					innerDiv.className = "inner";
					var severInfoTb = document.createElement("table"), trTmp, tnode, trIndex = -1;
					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = document.createElement("TH");
					tnode.innerHTML = "状态属性名";
					trTmp.appendChild(tnode);
					tnode = tnode.cloneNode();
					tnode.innerHTML = "状态值";
					trTmp.appendChild(tnode);
					for (key in data) {
						trTmp = severInfoTb.insertRow(++trIndex);
						tnode = trTmp.insertCell(0);
						tnode.innerHTML = key;
						tnode = trTmp.insertCell(1);
						tnode.innerHTML = data[key];
					}
					innerDiv.appendChild(severInfoTb);
					boxElementContent.appendChild(innerDiv);
				},
				"error" : function() {

				},
				"complete" : function() {
					if (dbBgSave) {
						$(boxElementContent).append("<hr/>");
						$(boxElementContent).append(
								"<div class=\"inner\"><h2>" + dbTypeStr
										+ "数据库保存</h2></div>");
						$(boxElementContent)
								.append(
										"<div class=\"inner\">\
					<button type=\"button\" onclick=\"bgSaveDb();\">保存数据库</button>\
					<span id=\"saveTip\" class=\"fade\">点击立刻保存数据</span></div>");
					}
				}
			});
}
/**
 * 修改站点配置
 * 
 * @return void
 */
function saveSiteConf() {
	$.ajax({
		"type" : "POST",
		"url" : "/hadmin/saveConf",
		"data" : {
			"sitename" : $("#sitename").val(),
			"notice_on" : $("#notice_on").val(),
			"notice_text" : $("#notice_text").val(),
			"open_compress" : $("#open_compress").val()
		},
		"dataType" : "json",
		"success" : function(data) {
			if(data.success)
				$("#update_stat").html("修改站点配置成功 !").show();
			else
				$("#update_stat").html("修改站点配置失败 !").show();
		},
		"error" : function() {
			$("#update_stat").html("修改站点配置失败 !").show();
		}
	});
}
/**
 * 显示站点设置
 */
function showAdminSets() {
	var sep20 = document.createElement("div"), mainDiv = document
			.getElementById("Main"), boxElement = sep20.cloneNode();
	mainDiv.innerHTML = '';
	sep20.className = "sep20";
	mainDiv.appendChild(sep20);
	mainDiv.appendChild(boxElement);
	mainDiv.appendChild(sep20.cloneNode());
	boxElement.className = "box";
	var boxElementContent = boxElement.cloneNode();
	mainDiv.appendChild(boxElementContent);
	boxElement.innerHTML = '<div class="header">\
					<a href="/">'
			+ sitename
			+ '</a> <span class="chevron">&nbsp;›&nbsp;</span>\
					站点设置&nbsp;\
			</div>';
	$(boxElementContent).append(
			"<div class=\"inner\">" + dbTypeStr + "站点设置</div><p id=\"update_stat\" style=\"display:none\"></p>");
	$
			.ajax({
				"type" : "POST",
				"url" : "/hadmin/siteSets",
				"dataType" : "json",
				"success" : function(data) {
					var innerDiv = document.createElement("div");
					innerDiv.className = "inner";
					var severInfoTb = document.createElement("table"), trTmp, tnode, trIndex = -1;
					innerDiv.appendChild(severInfoTb);
					boxElementContent.appendChild(innerDiv);

					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = trTmp.insertCell(0);
					tnode.innerHTML = "网站名称";
					tnode = trTmp.insertCell(1);
					tnode.innerHTML = "<input type=\"text\" id=\"sitename\" value=\""
							+ data.sitename + "\" />";

					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = trTmp.insertCell(0);
					tnode.innerHTML = "创建时间";
					tnode = trTmp.insertCell(1);
					tnode.innerHTML = data.create_time;

					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = trTmp.insertCell(0);
					tnode.innerHTML = "是否显示公告";
					tnode = trTmp.insertCell(1);
					if (data.notice_on == 1)
						tnode.innerHTML = "<select id=\"notice_on\">\
						<option value=\"1\" selected=\"selected\">显示</option>\
						<option value=\"0\">不显示</option>\
						</select>";
					else
						tnode.innerHTML = "<select id=\"notice_on\">\
					<option value=\"1\">显示</option>\
					<option value=\"0\" selected=\"selected\">不显示</option>\
					</select>";

					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = trTmp.insertCell(0);
					tnode.innerHTML = "公告文本";
					tnode = trTmp.insertCell(1);
					tnode.innerHTML = "<textarea id=\"notice_text\">"
							+ data.notice_text + "</textarea>";

					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = trTmp.insertCell(0);
					tnode.innerHTML = "是否开启压缩功能";
					tnode = trTmp.insertCell(1);
					if (data.open_compress == 1)
						tnode.innerHTML = "<select id=\"open_compress\">\
						<option value=\"1\" selected=\"selected\">开启压缩</option>\
						<option value=\"0\">关闭压缩</option>\
						</select>";
					else
						tnode.innerHTML = "<select id=\"open_compress\">\
					<option value=\"1\">开启压缩</option>\
					<option value=\"0\" selected=\"selected\">关闭压缩</option>\
					</select>";

					trTmp = severInfoTb.insertRow(++trIndex);
					tnode = trTmp.insertCell(0);
					tnode.innerHTML = "保存修改";
					tnode = trTmp.insertCell(1);
					tnode.innerHTML = "<button type=\"button\" class=\"btn btn-super\" onclick=\"saveSiteConf();\">修改配置</button>"
				},
				"error" : function() {

				}
			});
}
function needSelectNode() {
	$.fDialog.message("执行此操作前,需要先选择节点!!!", "wrong", {
		"x" : "center", // 水平居中
		"y" : "center", // 垂直居中
		"time" : 1300
	});
}
/**
 * 弹窗要求输入节点名称
 * 
 * @param function
 *            fn(bkname)
 * @return void
 */
function getInputBkName(fn) {
	$
			.fDialog({
				"id" : "new_bk", // id
				"title" : "请输入节点名称", // 标题
				"content" : "<div class=\"inner\"><input type=\"text\" class=\"sl\" id=\"bkname\" autofocus=\"autofocus\" spellcheck=\"false\" placeholder=\"新节点名称\"/></div>",
				"w" : 400, // 宽
				"h" : 170, // 高
				"buttons" : [ {
					"text" : "取消",
					"cls" : "gray"
				}, {
					"text" : "确定",
					"cls" : "success",
					"click" : function() {
						var bkname = $("#bkname").val();
						if (bkname != "")
							fn(bkname);
						return true;
					}
				} ],
				"x" : "center", // 水平居中
				"y" : "center" // 垂直居中
			});
}
/**
 * 添加子节点操作
 * 
 * @return void
 */
function addChildBk() {
	var zTree = $.fn.zTree.getZTreeObj("bk_tree"), nodes = zTree
			.getSelectedNodes(), treeNode = nodes[0];
	if (nodes.length == 0) {
		needSelectNode();
		return;
	}
	getInputBkName(function(bkname) {
		$.ajax({
			"type" : "POST",
			"url" : "/hadmin/addbk",
			"data" : {
				"pid" : treeNode.id,
				"bkname" : bkname
			},
			"success" : function(data) {
				if (data.success)
					zTree.addNodes(treeNode, data.nodeInfo);
			}
		});
	});
}
/**
 * 显示节点分支编辑页面
 * 
 * @return void
 */
function showBkTree() {
	var sep20 = document.createElement("div"), mainDiv = document
			.getElementById("Main"), boxElement = sep20.cloneNode();
	mainDiv.innerHTML = '';
	sep20.className = "sep20";
	mainDiv.appendChild(sep20);
	mainDiv.appendChild(boxElement);
	mainDiv.appendChild(sep20.cloneNode());
	boxElement.className = "box";
	var boxElementContent = boxElement.cloneNode();
	mainDiv.appendChild(boxElementContent);
	boxElement.innerHTML = '<div class="header">\
					<a href="/">'
			+ sitename
			+ '</a> <span class="chevron">&nbsp;›&nbsp;</span>\
					节点管理&nbsp;\
			</div>';
	$(boxElementContent)
			.append(
					"<div style=\"text-align:left\">"
							+ "<div>操作: <button type=\"button\" class=\"btn btn-super\" onclick=\"addChildBk();\">添加子节点</button> <button type=\"button\" class=\"btn btn-super\" onclick=\"editBkname();\">修改节点名称</button> <button type=\"button\" class=\"btn btn-super\" onclick=\"deleteBk();\">删除节点</button></div>"
							+ "<ul id=\"bk_tree\" class=\"ztree\"></ul></div>");
	var setting = {
		"async" : {
			"enable" : true,
			"url" : "/hadmin/bktree",
			"autoParam" : [ "id=pid" ]
		},
		"edit" : {
			"enable" : true
		}
	};
	$.fn.zTree.init($("#bk_tree"), setting);
}
/**
 * 显示搜索到的用户信息修改页面
 * 
 * @return void
 */
function searchUser() {
	$.ajax({
		"type" : "POST",
		"url" : "/hadmin/getUserInfo",
		"data" : {
			"input_text" : $("#username").val()
		},
		"success" : function(data) {
			/* 显示用户详细信息界面 */
			if (data.success) {
				var tableStr = "<table>", key;
				for (key in data.info) {
					tableStr += ("<tr><td>" + key + "</td><td>"
							+ data.info[key] + "</td></tr>");
				}
				tableStr += "</table>";
				$("#user_info").html(tableStr);
			} else {
				$("#user_info").html(data.msg);
			}
		}
	});
}
/**
 * 显示用户列表界面
 * 
 * @return void
 */
function showUserlist() {
	var sep20 = document.createElement("div"), mainDiv = document
			.getElementById("Main"), boxElement = sep20.cloneNode();
	mainDiv.innerHTML = '';
	sep20.className = "sep20";
	mainDiv.appendChild(sep20);
	mainDiv.appendChild(boxElement);
	mainDiv.appendChild(sep20.cloneNode());
	boxElement.className = "box";
	var boxElementContent = boxElement.cloneNode();
	mainDiv.appendChild(boxElementContent);
	boxElement.innerHTML = '<div class="header">\
					<a href="/">'
			+ sitename
			+ '</a> <span class="chevron">&nbsp;›&nbsp;</span>\
					用户管理&nbsp;\
			</div>';
	$(boxElementContent)
			.append(
					"<div class=\"inner\">"
							+ "查找用户: <input type=\"text\" class=\"sl\" id=\"username\" autofocus=\"autofocus\" spellcheck=\"false\" placeholder=\"用户名或电子邮箱地址\"/>"
							+ "<button type=\"button\" class=\"btn btn-super\" onclick=\"searchUser();\">查找用户</button>"
							+ "</div>");
	$(boxElementContent).append(
			"<div id=\"user_info\" class=\"inner\">用户详细信息说明。。</div>");
	/*
	 * $(boxElementContent).append("<div class=\"inner fenye_div\">\ <a
	 * href=\"javascript:void(0)\" class=\"page_normal\">1</a><a
	 * href=\"javascript:void(0)\" class=\"page_normal\">2</a><span
	 * class=\"page_current\">3</span>\ </div>");
	 */
}