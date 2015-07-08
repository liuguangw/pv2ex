<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;
use liuguang\pv2ex\model\USession;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\pv2ex\model\SiteModel;

class Index extends BaseController {
	public function indexAction() {
		$this->forceInstall ();
		$siteInfoM=new SiteModel($this);
		$siteInfo=$siteInfoM->getSiteInfo(array('sitename'));
		$title = $siteInfo['sitename'].' › 测试页面';
		$session = new USession ( $this );
		$user = new UserModel ( $this );
		Templatel::tplStart ();
		include Templatel::view ( '/index.html' );
		Templatel::tplEnd ();
	}
}