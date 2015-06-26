<?php

namespace liuguang\pv2ex\controller\web;
use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;
class Index extends BaseController{
	public function indexAction() {
		$this->forceInstall();
		$title = 'V2EX › 测试页面';
		Templatel::setCompress (true);
		Templatel::tplStart ();
		include Templatel::view ( '/test.html' );
		Templatel::tplEnd ();
	}
}