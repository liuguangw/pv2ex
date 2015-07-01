<?php

namespace liuguang\pv2ex\controller\web;
use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use think\Verify;
class Captcha extends BaseController{
	public function indexAction() {
		$sessionObj=new USession($this);
		$v=new Verify();
		$rcode=$v->createCodeStr();
		$sessionObj->getSessionData()->set('rcode', $rcode);
		$v->entry();
	}
}