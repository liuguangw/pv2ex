<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\mvc\Templatel;
class SignOut extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$urlData=$this->getApp()->getUrlHandler()->getUrlData();
		$session=new USession($this);
		$sessionRand=$session->getSessionData()->get('rand','');
		$urlRand=$urlData->get('rand','');
		if(empty($sessionRand)||empty($urlRand))
		$loginOutOk=false;
		else 
			$loginOutOk=($sessionRand==$urlRand);
		if($loginOutOk)
			$session->destroy();
		$title='V2EX › 登出';
		Templatel::tplStart ();
		include Templatel::view ( '/logout.html' );
		Templatel::tplEnd ();
	}
}