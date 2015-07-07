<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\mvc\Templatel;

class UserCenter extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$session = new USession ( $this );
		$uid = $session->getUid ();
		$urlHandler = $this->getApp ()->getUrlHandler ();
		if ($uid == 0) {
			$signInUrl = $urlHandler->createUrl ( 'web/SignIn', 'index', array () );
			header ( 'Location: ' . $signInUrl );
			return;
		}
		$user = new UserModel ( $this );
		$uid = $session->getUid ();
		$rand = $session->createNewSid ();
		$session->getSessionData ()->set ( 'rand', $rand );
		$userInfo = $user->getUidInfo ( $uid, array (
				'username',
				'nickname',
				'user_img',
				'regtime' 
		) );
		$isAdmin=$user->isSuperAdmin($uid);
		$session = null;
		$title = 'V2EX › ' . $userInfo ['nickname'];
		Templatel::tplStart ();
		include Templatel::view ( '/userindex.html' );
		Templatel::tplEnd ();
	}
}