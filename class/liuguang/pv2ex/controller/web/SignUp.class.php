<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;

/**
 * 显示注册页面,处理用户注册
 *
 * @author liuguang
 *        
 */
class SignUp extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$urlHandler = $this->getApp ()->getUrlHandler ();
		$doRegUrl = $urlHandler->createUrl ( 'web/SignUp', 'do', array () );
		$captchaUrl=$urlHandler->createUrl('web/Captcha', 'index', array());
		$captchaUrlT=$urlHandler->createUrl('web/Captcha', '--rand--',array(),false);
		$title = 'V2EX › 注册';
		Templatel::tplStart ();
		include Templatel::view ( '/reg.html' );
		Templatel::tplEnd ();
	}
}