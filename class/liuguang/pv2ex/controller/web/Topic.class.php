<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\pv2ex\model\SiteModel;

class Topic extends BaseController {
	public function postNewAction() {
		$this->forceInstall ();
		$session = new USession ( $this );
		$uid = $session->getUid ();
		if ($uid == 0) {
			$urlHandler = $this->getApp ()->getUrlHandler ();
			$signInUrl = $urlHandler->createUrl ( 'web/SignIn', 'index', array () );
			header ( 'Location: ' . $signInUrl );
			return;
		}
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › 创建新主题';
		$user = new UserModel ( $this );
		Templatel::tplStart ();
		include Templatel::view ( '/postnew.html' );
		Templatel::tplEnd ();
	}
}