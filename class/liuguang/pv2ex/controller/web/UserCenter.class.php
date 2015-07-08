<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\SiteModel;

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
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › '. $userInfo ['nickname'];
		Templatel::tplStart ();
		include Templatel::view ( '/userindex.html' );
		Templatel::tplEnd ();
	}
}