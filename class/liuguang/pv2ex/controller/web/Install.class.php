<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\pv2ex\model\Install as InstallModel;
use liuguang\pv2ex\model\User as UserModel;
use liuguang\mvc\Templatel;
use liuguang\mvc\DataMap;

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
		$installStep = 0;
		if ($installModel->hasInstalled ()) {
			$installStep = 3;
		} elseif (! $installModel->statReady ()) {
			$errArr [] = $installModel->getErrMsg ();
			$installStep = 1;
		}
		$this->showInstallForm ( $installStep, $errArr );
	}
	/**
	 * 处理提交的安装表单
	 *
	 * @return void
	 */
	public function doAction() {
		$installModel = new InstallModel ( $this );
		$errArr = array ();
		$installStep = 0;
		if ($installModel->hasInstalled ()) {
			$installStep = 3;
			$this->showInstallForm ( $installStep, $errArr );
			return;
		}
		if (! $installModel->statReady ())
			$errArr [] = $installModel->getErrMsg ();
			// 检测post提交的数据
		$user = new UserModel ( $this );
		$postData = new DataMap ( $_POST );
		$username = $postData->get ( 'username', '' );
		$nickname = $postData->get ( 'nickname', '' );
		$email = $postData->get ( 'email', '' );
		if (! $user->isUsername ( $username ))
			$errArr [] = $user->getErrMsg ();
		if (! $user->isNickname ( $nickname ))
			$errArr [] = $user->getErrMsg ();
		if (! $user->isEmail ( $email ))
			$errArr [] = $user->getErrMsg ();
		$pass1 = $postData->get ( 'pass1', '' );
		$pass2 = $postData->get ( 'pass2', '' );
		if ($pass1 != $pass2)
			$errArr [] = '两次输入的密码不一致';
		if (! $user->isPass ( $pass1 ))
			$errArr [] = $user->getErrMsg ();
		if (! empty ( $errArr )) {
			$installStep = 1;
			$this->showInstallForm ( $installStep, $errArr );
			return;
		}
		// 执行安装操作
		$installModel->initDb ();
		// 添加管理员账号
		$uid = $user->addAccount ( $username, $nickname, $pass1, $email );
		if ($uid == - 1) {
			$installStep = 1;
			$this->showInstallForm ( $installStep, array (
					'添加用户账号失败' 
			) );
			return;
		}
		// 添加管理员权限
		$user->addSuperAdmin ( $uid );
		$installStep = 2;
		$this->showInstallForm ( $installStep, array () );
	}
	/**
	 * 显示安装界面
	 *
	 * @param int $installStep
	 *        	安装进行到的步骤
	 *        	为0时,表示未安装,界面显示安装界面
	 *        	为1时，表示进行安装时出现错误,显示错误信息
	 *        	为2时,表示执行安装成功，显示关闭安装的步骤
	 *        	为3时,表示系统已安装,此时提示禁止安装，以及重新执行安装的方法
	 * @param array $errArr
	 *        	错误消息提示
	 * @return void
	 */
	private function showInstallForm($installStep, array $errArr = array()) {
		$urlHandler = $this->getApp ()->getUrlHandler ();
		// var_dump($urlHandler->getUrlData());exit();
		$doInstallUrl = $urlHandler->createUrl ( 'web/Install', 'do', array () );
		$title = 'V2EX › 安装';
		Templatel::tplStart ();
		include Templatel::view ( '/install.html' );
		Templatel::tplEnd ();
	}
}