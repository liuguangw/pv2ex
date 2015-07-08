<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\SiteModel;

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
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › 登出';
		Templatel::tplStart ();
		include Templatel::view ( '/logout.html' );
		Templatel::tplEnd ();
	}
}