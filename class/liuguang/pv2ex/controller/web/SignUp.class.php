<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;
use liuguang\mvc\DataMap;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\pv2ex\model\USession;
use liuguang\mvc\liuguang\mvc;

/**
 * 显示注册页面,处理用户注册
 *
 * @author liuguang
 *        
 */
class SignUp extends BaseController {
	/**
	 * 显示注册页面
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->forceInstall ();
		$this->showRegForm ();
	}
	
	/**
	 * 表单处理
	 *
	 * @return void
	 */
	public function doAction() {
		$this->forceInstall ();
		if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
			$this->showRegForm ();
			return;
		}
		$postData = new DataMap ( $_POST );
		$user = new UserModel ( $this );
		$session = new USession ( $this );
		$sessionData = $session->getSessionData ();
		$errArr = array ();
		$username = $postData->get ( 'username', '' );
		$nickname = $postData->get ( 'nickname', '' );
		$email = $postData->get ( 'email', '' );
		$pass1 = $postData->get ( 'pass1', '' );
		$pass2 = $postData->get ( 'pass2', '' );
		$rcodePost = $postData->get ( 'rcode', '' );
		$rcode = $sessionData->get ( 'rcode', '' );
		$sessionData->set ( 'rcode', '' );
		if (! $user->isUsername ( $username ))
			$errArr [] = $user->getErrMsg ();
		if (! $user->isNickname ( $nickname ))
			$errArr [] = $user->getErrMsg ();
		if (! $user->isEmail ( $email ))
			$errArr [] = $user->getErrMsg ();
		if ($pass1 != $pass2)
			$errArr [] = '两次输入的密码不一致';
		if (! $user->isPass ( $pass1 ))
			$errArr [] = $user->getErrMsg ();
		if ($rcode == '')
			$errArr [] = '请打开验证码图片显示';
		if (strcasecmp ( $rcodePost, $rcode ) != 0)
			$errArr [] = '验证码输入有误';
		if (! empty ( $errArr )) {
			$this->showRegForm ( '', $errArr );
			return;
		}
		// 判断用户名、邮箱是否已经被使用
		if ($user->isUsernameExists ( $username ))
			$errArr [] = '用户名' . $username . '已经被注册了';
		if ($user->isEmailExists ( $email ))
			$errArr [] = '邮箱' . $email . '已经被使用了';
		if (! empty ( $errArr )) {
			$this->showRegForm ( '', $errArr );
			return;
		}
		// 添加账号
		$uid = $user->addAccount ( $username, $nickname, $pass1, $email );
		if ($uid == - 1) {
			$errArr [] = '注册账号失败,请稍后再试';
			$this->showRegForm ( '', $errArr );
		} else
			//注册成功
			var_dump ( $uid );
	}
	/**
	 * 显示注册页面
	 *
	 * @param string $signMsg
	 *        	提示消息
	 * @param array $signErrArr
	 *        	所有的错误数组
	 * @return void
	 */
	private function showRegForm($signMsg = '', array $signErrArr = array()) {
		$urlHandler = $this->getApp ()->getUrlHandler ();
		$doRegUrl = $urlHandler->createUrl ( 'web/SignUp', 'do', array () );
		$captchaUrl = $urlHandler->createUrl ( 'web/Captcha', 'index', array () );
		$captchaUrlT = $urlHandler->createUrl ( 'web/Captcha', '--rand--', array (), false );
		$title = 'V2EX › 注册';
		$postData = new DataMap ( $_POST );
		$username = $postData->get ( 'username', '' );
		$nickname = $postData->get ( 'nickname', '' );
		$email = $postData->get ( 'email', '' );
		Templatel::tplStart ();
		include Templatel::view ( '/reg.html' );
		Templatel::tplEnd ();
	}
}