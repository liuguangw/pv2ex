<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\SiteModel;
use liuguang\pv2ex\model\BkModel;
use liuguang\mvc\DataMap;
use liuguang\mvc\liuguang\mvc;
use liuguang\pv2ex\model\liuguang\pv2ex\model;

class Admin extends BaseController {
	/**
	 * 后台管理中心首页
	 *
	 * @return void
	 */
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$siteInfoM = new SiteModel ( $this );
		$siteInfo = $siteInfoM->getSiteInfo ( array (
				'sitename' 
		) );
		$title = $siteInfo ['sitename'] . ' › 后台管理中心首页';
		$dbTypeStr = $this->getDbTypeStr ();
		$dbBgSave = $siteInfoM->hasBgSave () ? 'true' : 'false';
		Templatel::tplStart ();
		include Templatel::view ( '/admin/index.html' );
		Templatel::tplEnd ();
	}
	/**
	 * 后台首页异步请求获取网站状态信息
	 *
	 * @return void
	 */
	public function siteStatAction() {
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$siteInfoM = new SiteModel ( $this );
		$siteStat = $siteInfoM->getSiteStat ();
		$this->jsonReturn ( $siteStat );
	}
	/**
	 * 执行后台保存数据库命令
	 *
	 * @return void
	 */
	public function bgSaveDbAction() {
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$siteInfoM = new SiteModel ( $this );
		if ($siteInfoM->hasBgSave ()) {
			$siteInfoM->saveDb ();
		}
		$result = array (
				'success' => true 
		);
		$this->jsonReturn ( $result );
	}
	/**
	 * 返回json格式的网站配置设置
	 *
	 * @return void
	 */
	public function siteSetsAction() {
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$siteInfoM = new SiteModel ( $this );
		$result = $siteInfoM->getSiteInfo ( array () );
		$result ['create_time'] = date ( 'Y-m-d H:i:s P', $result ['create_time'] );
		$this->jsonReturn ( $result );
	}
	public function bktreeAction() {
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$bkM = new BkModel ( $this );
		if (! isset ( $_POST ['pid'] )) {
			$arr [] = array (
					'id' => 0,
					'name' => '根节点',
					'isParent' => $bkM->hasChildBk ( 0 ) 
			);
			$this->jsonReturn ( $arr );
			return;
		} else
			$pid = intval ( $_POST ['pid'] );
		$childPids = $bkM->getChildrenBks ( $pid );
		$arr = array ();
		foreach ( $childPids as $newpid ) {
			$bkInfo = $bkM->getBkinfo ( $newpid, array (
					'bkname' 
			) );
			$arr [] = array (
					'id' => $newpid,
					'name' => $bkInfo ['bkname'],
					'isParent' => $bkM->hasChildBk ( $newpid ) 
			);
		}
		$this->jsonReturn ( $arr );
	}
	/**
	 * 添加板块的ajax异步请求
	 *
	 * @return void
	 */
	public function addbkAction() {
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$bkM = new BkModel ( $this );
		$postData = new DataMap ( $_POST );
		$pid = intval ( $postData->get ( 'pid', 0 ) );
		$bkname = $postData->get ( 'bkname', '新节点' );
		// 判断父节点pid是否存在
		if (! $bkM->bkIdExists ( $pid )) {
			$arr = array (
					'success' => false 
			);
		} else {
			$bkid = $bkM->createBk ( $pid, $bkname );
			$arr = array (
					'success' => true 
			);
			$arr ['nodeInfo'] = array (
					'id' => $bkid,
					'pId' => $pid,
					'isParent' => false,
					'name' => $bkname 
			);
		}
		$this->jsonReturn ( $arr );
	}
	/**
	 * 返回后台提交的用户名或者邮箱信息
	 *
	 * @return void
	 */
	public function getUserInfoAction() {
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		// 判断提交的为用户名还是邮箱
		$postData = new DataMap ( $_POST );
		$inputText = $postData->get ( 'input_text', '' );
		if ($inputText == '') {
			$arr = array (
					'success' => false,
					'msg' => '用户名或者email不能不空' 
			);
			$this->jsonReturn ( $arr );
			return;
		}
		if ($user->isEmail ( $inputText )) {
			if ($user->isEmailExists ( $inputText )) {
				$t_uid = $user->getEmailUid ( $inputText );
				$userinfo = $user->getUidInfo ( $t_uid );
				// 防止密码外泄
				unset ( $userinfo ['pass'] );
				$arr = array (
						'success' => true,
						'info' => $userinfo 
				);
				$this->jsonReturn ( $arr );
				return;
			} else {
				$arr = array (
						'success' => false,
						'msg' => '此email不存在' 
				);
				$this->jsonReturn ( $arr );
				return;
			}
		} elseif ($user->isUsername ( $inputText )) {
			if ($user->isUsernameExists ( $inputText )) {
				$t_uid = $user->getUsernameUid ( $inputText );
				$userinfo = $user->getUidInfo ( $t_uid );
				unset ( $userinfo ['pass'] );
				$arr = array (
						'success' => true,
						'info' => $userinfo 
				);
				$this->jsonReturn ( $arr );
				return;
			} else {
				$arr = array (
						'success' => false,
						'msg' => '此用户名不存在' 
				);
				$this->jsonReturn ( $arr );
				return;
			}
		} else
			$arr = array (
					'success' => false,
					'msg' => '请输入正确的用户名或者邮箱' 
			);
		$this->jsonReturn ( $arr );
		return;
	}
	/**
	 * 处理异步提交过来的站点配置的修改
	 * 
	 * @return void
	 */
	public function saveConfAction(){
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
		$isAdmin = $user->isSuperAdmin ( $uid );
		if (! $isAdmin) {
			$this->needAdmin ();
			return;
		}
		$postData=new DataMap($_POST);
		$sitename=$postData->get('sitename','');
		$noticeOn=$postData->get('notice_on',0);
		$noticeText=$postData->get('notice_text','');
		$openCompress=$postData->get('open_compress',0);
		$siteM=new SiteModel($this);
		$newSets=array();
		$newSets['sitename']=$sitename;
		$newSets['notice_on']=($noticeOn==0)?'0':'1';
		$newSets['notice_text']=$noticeText;
		$newSets['open_compress']=($openCompress==0)?'0':'1';
		$siteM->updateSiteInfo($newSets);
		$ajaxReturn=array('success'=>true);
		$this->jsonReturn($ajaxReturn);
	}
	/**
	 * 显示权限不足的提示页面
	 *
	 * @return void
	 */
	private function needAdmin() {
	}
}