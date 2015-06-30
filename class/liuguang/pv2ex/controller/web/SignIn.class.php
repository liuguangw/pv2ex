<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;

/**
 * 显示登录页面,处理用户登录
 *
 * @author liuguang
 *        
 */
class SignIn extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$urlHandler = $this->getApp ()->getUrlHandler ();
		$doLoginUrl = $urlHandler->createUrl ( 'web/SignIn', 'do', array () );
		$resetPassUrl = $urlHandler->createUrl ( 'web/Forgot', 'index', array () );
		$title = 'V2EX › 登录';
		Templatel::tplStart ();
		include Templatel::view ( '/login.html' );
		Templatel::tplEnd ();
	}
}