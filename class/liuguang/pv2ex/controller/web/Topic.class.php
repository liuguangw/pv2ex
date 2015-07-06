<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\User as UserModel;
class Topic extends BaseController {
	public function postNewAction() {
		$this->forceInstall ();
		$title = 'V2EX › 创作新主题';
		$session = new USession ( $this );
		$user = new UserModel ( $this );
		Templatel::tplStart ();
		include Templatel::view ( '/postnew.html' );
		Templatel::tplEnd ();
	}
}