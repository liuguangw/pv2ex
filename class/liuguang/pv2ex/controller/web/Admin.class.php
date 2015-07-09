<?php

namespace liuguang\pv2ex\controller\web;
use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\SiteModel;
class Admin extends BaseController{
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
		$isAdmin=$user->isSuperAdmin($uid);
		if(!$isAdmin){
			$this->needAdmin();
			return;
		}
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › 后台管理中心首页';
		$dbTypeStr=$this->getDbTypeStr();
		$dbBgSave=$siteInfoM->hasBgSave()?'true':'false';
		Templatel::tplStart ();
		include Templatel::view ( '/admin/index.html' );
		Templatel::tplEnd ();
	}
	/**
	 * 后台首页异步请求获取网站状态信息
	 * 
	 * @return void
	 */
	public function siteStatAction(){
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
		$isAdmin=$user->isSuperAdmin($uid);
		if(!$isAdmin){
			$this->needAdmin();
			return;
		}
		$siteInfoM=new SiteModel($this);
		$siteStat=$siteInfoM->getSiteStat();
		$this->jsonReturn($siteStat);
	}
	/**
	 * 执行后台保存数据库命令
	 * 
	 * @return void
	 */
	public function bgSaveDbAction(){
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
		$isAdmin=$user->isSuperAdmin($uid);
		if(!$isAdmin){
			$this->needAdmin();
			return;
		}
		$siteInfoM=new SiteModel($this);
		if($siteInfoM->hasBgSave()){
			$siteInfoM->saveDb();
		}
		$result=array('success'=>true);
		$this->jsonReturn($result);
	}
	/**
	 * 返回json格式的网站配置设置
	 * 
	 * @return void
	 */
	public function siteSetsAction(){
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
		$isAdmin=$user->isSuperAdmin($uid);
		if(!$isAdmin){
			$this->needAdmin();
			return;
		}
		$siteInfoM=new SiteModel($this);
		$result=$siteInfoM->getSiteInfo(array());
		$result['create_time']=date('Y-m-d H:i:s P',$result['create_time']);
		$this->jsonReturn($result);
	}
	/**
	 * 显示权限不足的提示页面
	 * 
	 * @return void
	 */
	private function needAdmin() {
	}
}