<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\SiteModel;
use liuguang\pv2ex\model\BkModel;
use liuguang\mvc\DataMap;

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
		if (!isset( $_POST ['pid'] )){
			$arr [] = array (
					'id' => 0,
					'name' => '根节点',
					'isParent' => $bkM->hasChildBk(0)
			);
			$this->jsonReturn ( $arr );
			return;
		}
		else
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
					'isParent' => $bkM->hasChildBk($newpid)
			);
		}
		$this->jsonReturn ( $arr );
	}
	/**
	 * 添加板块的ajax异步请求
	 * 
	 * @return void
	 */
	public function addbkAction(){
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
		$postData=new DataMap($_POST);
		$pid = intval ( $postData->get('pid',0));
		$bkname=$postData->get('bkname','新节点');
		//判断父节点pid是否存在
		if(!$bkM->bkIdExists($pid)){
			$arr=array('success'=>false);
		}else{
			$bkid=$bkM->createBk($pid, $bkname);
			$arr=array('success'=>true);
			$arr['nodeInfo']=array('id'=>$bkid,'pId'=>$pid,'isParent'=>false,'name'=>$bkname);
		}
		$this->jsonReturn ( $arr );
	}
	/**
	 * 显示权限不足的提示页面
	 *
	 * @return void
	 */
	private function needAdmin() {
	}
}