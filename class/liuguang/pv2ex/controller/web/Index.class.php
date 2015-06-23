<?php

namespace liuguang\pv2ex\controller\web;
use liuguang\pv2ex\model\BaseController;
class Index extends BaseController{
	public function indexAction() {
		$this->forceInstall();
	}
}