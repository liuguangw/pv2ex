<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;

class Index extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$title = 'V2EX › 测试页面';
		$session = new USession ( $this );
		$user = new UserModel ( $this );
		Templatel::tplStart ();
		include Templatel::view ( '/index.html' );
		Templatel::tplEnd ();
	}
}