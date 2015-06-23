<?php

namespace liuguang\pv2ex\controller;
use liuguang\mvc\Application;
class Err404 {
	public function indexAction() {
		$app=Application::getApp();
		$errHandler=$app->getErrHandler();
		$errHandler->handle(404, '很抱歉，您要访问的页面不存在！');
	}
}