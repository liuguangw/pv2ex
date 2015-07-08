<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;
use liuguang\mvc\DataMap;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\SiteModel;

/**
 * 显示登录页面,处理用户登录
 *
 * @author liuguang
 *        
 */
class SignIn extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$this->showLoginForm ();
	}
	private function showLoginForm($signMsg = '', array $signErrArr = array()) {
		$urlHandler = $this->getApp ()->getUrlHandler ();
		$doLoginUrl = $urlHandler->createUrl ( 'web/SignIn', 'do', array () );
		$resetPassUrl = $urlHandler->createUrl ( 'web/Forgot', 'index', array () );
		$captchaUrl = $urlHandler->createUrl ( 'web/Captcha', 'index', array () );
		$captchaUrlT = $urlHandler->createUrl ( 'web/Captcha', '--rand--', array (), false );
		if (! empty ( $_POST ['url'] ))
			$url = $_POST ['url'];
		elseif (! empty ( $_SERVER ['HTTP_REFERER'] ))
			$url = $_SERVER ['HTTP_REFERER'];
		else
			$url = '';
		if (! empty ( $_POST ['username'] ))
			$username = $_POST ['username'];
		else
			$username = '';
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › 登录';
		Templatel::tplStart ();
		include Templatel::view ( '/login.html' );
		Templatel::tplEnd ();
	}
	public function doAction() {
		$this->forceInstall ();
		if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
			$this->showLoginForm ();
			return;
		}
		$postData = new DataMap ( $_POST );
		$user = new UserModel ( $this );
		$session = new USession ( $this );
		$sessionData = $session->getSessionData ();
		$errArr = array ();
		$username = $postData->get ( 'username', '' );
		$pass = $postData->get ( 'pass', '' );
		$rcodePost = $postData->get ( 'rcode', '' );
		$rcode = $sessionData->get ( 'rcode', '' );
		$sessionData->set ( 'rcode', '' );
		$urlPost = $postData->get ( 'url', '' );
		$loginType = 1;
		if ($user->isUsername ( $username ))
			$loginType = 1;
		elseif ($user->isEmail ( $username ))
			$loginType = 2;
		else
			$errArr [] = '请输入正确的用户名或者邮箱地址';
		if (! $user->isPass ( $pass ))
			$errArr [] = $user->getErrMsg ();
		if ($rcode == '')
			$errArr [] = '请打开验证码图片显示';
		if (strcasecmp ( $rcodePost, $rcode ) != 0)
			$errArr [] = '验证码输入有误';
		if (! empty ( $errArr )) {
			$this->showLoginForm ( '', $errArr );
			return;
		}
		// 判断用户名是否存在
		if (($loginType == 1) && ! $user->isUsernameExists ( $username ))
			$errArr [] = '用户名' . $username . '不存在';
		if (($loginType == 2) && ! $user->isEmailExists ( $username ))
			$errArr [] = '邮箱' . $username . '不存在';
		if (! empty ( $errArr )) {
			$this->showLoginForm ( '', $errArr );
			return;
		}
		$uid = $user->authPass ( $username, $pass, $loginType );
		if ($uid == - 1) {
			if ($loginType == 1)
				$errArr [] = '用户名或密码错误';
			elseif ($loginType == 2)
				$errArr [] = '邮箱或密码错误';
			$this->showLoginForm ( '', $errArr );
		} else {
			$session->setUid ( $uid );
			$session->updateLifetime ( 30 * 24 * 3600 );
			$url = $postData->get ( 'url', '' );
			if (empty ( $url )) {
				$urlHandler = $this->getApp ()->getUrlHandler ();
				$url = $urlHandler->createUrl ( 'web/Index', 'index', array (), false );
			}
			header ( 'Location: ' . $url );
		}
	}
}