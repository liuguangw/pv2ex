<?php

namespace liuguang\pv2ex\controller\web;

use liuguang\pv2ex\model\BaseController;
use liuguang\mvc\Templatel;

/**
 * 安装程序控制器
 *
 * @author liuguang
 *        
 */
class Install extends BaseController {
	public function indexAction() {
		$urlHandler=$this->getApp()->getUrlHandler();
		var_dump($urlHandler->getUrlData());exit();
		$title = 'V2EX › 安装';
		$cssList = array (
				'/public/css/main.css',
				'/public/css/font-awesome.min.css',
				/*'http://www.v2ex.com/static/css/style.css?v=2c35821f086f4210d98170308c7c5c6a', 
				'http://www.v2ex.com/css/desktop.css?v=3.9.0',
				'http://www.v2ex.com/static/css/highlight.css?v=4e42339469340aed94a0df881f682e48',
				'http://www.v2ex.com/static/css/jquery.textcomplete.css?v=5a041d39010ded8724744170cea6ce8d',
				'http://www.v2ex.com/static/js/select2/select2.css'*/
		);
		Templatel::setCompress ( true );
		Templatel::tplStart ();
		include Templatel::view ( '/install.html' );
		Templatel::tplEnd ();
	}
}