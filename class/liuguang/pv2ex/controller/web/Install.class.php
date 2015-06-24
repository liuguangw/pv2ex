<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\Install as InstallModel;
use liuguang\mvc\Templatel;

/**
 * 安装程序控制器
 *
 * @author liuguang
 *        
 */
class Install extends BaseController {
	/**
	 * 显示安装界面
	 *
	 * @return void
	 */
	public function indexAction() {
		$installModel = new InstallModel ( $this );
		$errArr = array ();
		if (! $installModel->statReady ()) {
			$errArr [] = $installModel->getErrMsg ();
		}
		$this->showInstallForm ( $errArr );
	}
	/**
	 * 处理提交的安装表单
	 *
	 * @return void
	 */
	public function doAction() {
		$installModel = new InstallModel ( $this );
		$errArr = array ();
		if (! $installModel->statReady ()) {
			$errArr [] = $installModel->getErrMsg ();
			$this->showInstallForm ( $errArr );
			return ;
		}
	}
	/**
	 * 显示安装界面
	 *
	 * @param array $errArr
	 *        	错误消息提示
	 * @return void
	 */
	private function showInstallForm(array $errArr = array()) {
		$urlHandler = $this->getApp ()->getUrlHandler ();
		// var_dump($urlHandler->getUrlData());exit();
		$doInstallUrl = $urlHandler->createUrl ( 'web/Install', 'do', array () );
		$title = 'V2EX › 安装';
		$cssList = array (
				'/public/css/main.css',
				'/public/css/font-awesome.min.css' 
		);
		Templatel::setCompress ( true );
		Templatel::tplStart ();
		include Templatel::view ( '/install.html' );
		Templatel::tplEnd ();
	}
}