<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;

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
		$title = 'V2EX › 找回密码';
		Templatel::tplStart ();
		include Templatel::view ( '/forgot.html' );
		Templatel::tplEnd ();
	}
}