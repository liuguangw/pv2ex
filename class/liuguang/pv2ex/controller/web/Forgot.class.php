<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\SiteModel;

/**
 * 找回密码
 *
 * @author liuguang
 *        
 */
class Forgot extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$urlHandler = $this->getApp ()->getUrlHandler ();
		$doResetUrl = $urlHandler->createUrl ( 'web/Forgot', 'do', array () );
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › 找回密码';
		Templatel::tplStart ();
		include Templatel::view ( '/forgot.html' );
		Templatel::tplEnd ();
	}
}