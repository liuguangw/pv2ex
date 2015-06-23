<?php

namespace liuguang\pv2ex\controller;
use liuguang\pv2ex\model\BaseController;

class Index extends BaseController{
	public function indexAction() {
		$this->getApp()->callController('web/Index', 'index');
	}
}