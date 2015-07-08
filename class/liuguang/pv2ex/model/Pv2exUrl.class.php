<?php

namespace liuguang\pv2ex\model;

use liuguang\mvc\UrlHandler;
use liuguang\mvc\DataMap;

class Pv2exUrl implements UrlHandler {
	private $cKey;
	private $aKey;
	private $defaultC;
	private $defaultA;
	private $err404C;
	private $urlRoute;
	private $urlData;
	private $appContext;
	private $appEntry;
	public function __construct(DataMap $config) {
		$this->cKey = $config->get ( 'cKey' );
		$this->aKey = $config->get ( 'aKey' );
		$this->defaultC = $config->get ( 'defaultC' );
		$this->defaultA = $config->get ( 'defaultA' );
		$this->err404C = $config->get ( '404C' );
		$this->urlRoute = $config->get ( 'url_route', array () );
		$this->urlData = $this->parseUrl ( $_SERVER ['REQUEST_URI'] );
		$this->appContext = $config->get ( 'app_context' );
		$this->appEntry = $config->get ( 'app_entry' );
	}
	/*
	 * !CodeTemplates.overridecomment.nonjd! @see \liuguang\mvc\UrlHandler::getUrlData()
	 */
	public function getUrlData() {
		return $this->urlData;
	}
	/*
	 * !CodeTemplates.overridecomment.nonjd!
	 * @see \liuguang\mvc\UrlHandler::parseUrl()
	 */
	public function parseUrl($url) {
		$data = array ();
		$urlData = new DataMap ( $data );
		$url = parse_url ( $url, PHP_URL_PATH );
		if (($url == 'null') || ($url == '/') || $url == '') {
			$urlData->set ( $this->cKey, $this->defaultC );
			$urlData->set ( $this->aKey, $this->defaultA );
		} else {
			// 判断url格式是否正确
			if (! preg_match ( '/^(\\/[^\\/]{1,32}){1,6}\\/?$/', $url )) {
				$urlData->set ( $this->cKey, $this->err404C );
				$urlData->set ( $this->aKey, $this->defaultA );
				return $urlData;
			}
			// 安装界面和处理安装的url
			if (in_array ( $url, array (
					'/install',
					'/install/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/Install' );
				$urlData->set ( $this->aKey, 'index' );
			} elseif (in_array ( $url, array (
					'/install/do',
					'/install/do/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/Install' );
				$urlData->set ( $this->aKey, 'do' );
			} elseif (in_array ( $url, array (
					'/signin',
					'/signin/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/SignIn' );
				$urlData->set ( $this->aKey, 'index' );
			} elseif (in_array ( $url, array (
					'/signin/do',
					'/signin/do/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/SignIn' );
				$urlData->set ( $this->aKey, 'do' );
			} elseif (in_array ( $url, array (
					'/signup',
					'/signup/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/SignUp' );
				$urlData->set ( $this->aKey, 'index' );
			} elseif (in_array ( $url, array (
					'/signup/do',
					'/signup/do/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/SignUp' );
				$urlData->set ( $this->aKey, 'do' );
			} elseif (in_array ( $url, array (
					'/forgot',
					'/forgot/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/Forgot' );
				$urlData->set ( $this->aKey, 'index' );
			} elseif (preg_match ( '/^\\/captcha(\\/([^\\/]+\\/?)?)?$/', $url )) {
				$urlData->set ( $this->cKey, 'web/Captcha' );
				$urlData->set ( $this->aKey, 'index' );
			} elseif (preg_match ( '/^\\/signout\\/([a-z0-9]{32})$/', $url, $data1 )) {
				$urlData->set ( $this->cKey, 'web/SignOut' );
				$urlData->set ( $this->aKey, 'index' );
				$urlData->set ( 'rand', $data1 [1] );
			} elseif (in_array ( $url, array (
					'/new',
					'/new/' 
			) )) {
				$urlData->set ( $this->cKey, 'web/Topic' );
				$urlData->set ( $this->aKey, 'postNew' );
			} elseif (preg_match ( '/^\\/member\\/([^\\/]+)(\\/([^\\/]+))?$/', $url, $data1 )) {
				$urlData->set ( $this->cKey, 'web/UserCenter' );
				$urlData->set ( $this->aKey, 'index' );
				$urlData->set ( 'username', $data1 [1] );
				if(isset($data1[3]))
					$urlData->set ( $this->aKey, $data1[3]);
			}elseif (preg_match ( '/^\\/hadmin(\\/([^\\/]+))?$/', $url, $data1 )) {
				$urlData->set ( $this->cKey, 'web/Admin' );
				$urlData->set ( $this->aKey, 'index' );
				if(isset($data1[1]))
					$urlData->set ( $this->aKey, $data1[2]);
			}
			 else {
				$urlData->set ( $this->cKey, $this->err404C );
				$urlData->set ( $this->aKey, $this->defaultA );
			}
		}
		return $urlData;
	}
	
	/*
	 * !CodeTemplates.overridecomment.nonjd! @see \liuguang\mvc\UrlHandler::getCname()
	 */
	public function getCname() {
		return $this->urlData->get ( $this->cKey, $this->defaultC );
	}
	
	/*
	 * !CodeTemplates.overridecomment.nonjd! @see \liuguang\mvc\UrlHandler::getAname()
	 */
	public function getAname() {
		return $this->urlData->get ( $this->aKey, $this->defaultA );
	}
	
	/*
	 * !CodeTemplates.overridecomment.nonjd! @see \liuguang\mvc\UrlHandler::createUrl()
	 */
	public function createUrl($cname, $aname, array $data, $xmlSafe = true) {
		$url_head = $this->appContext . '/';
		if ($cname == 'web/Install') {
			$url = $url_head . 'install';
			if ($aname != 'index')
				$url .= ('/' . $aname);
		} elseif ($cname == 'web/SignIn') {
			$url = $url_head . 'signin';
			if ($aname != 'index')
				$url .= ('/' . $aname);
		} elseif ($cname == 'web/SignOut') {
			$url = $url_head . 'signout';
			if ($aname == 'index') {
				$rand = '12345';
				if (isset ( $data ['rand'] ))
					$rand = $data ['rand'];
				$url .= ('/' . $rand);
			} else
				$url .= ('/' . $aname);
		} elseif ($cname == 'web/SignUp') {
			$url = $url_head . 'signup';
			if ($aname != 'index')
				$url .= ('/' . $aname);
		} elseif ($cname == 'web/Forgot') {
			$url = $url_head . 'forgot';
			if ($aname != 'index')
				$url .= ('/' . $aname);
		} elseif ($cname == 'web/Captcha') {
			$url = $url_head . 'captcha';
			if ($aname != 'index')
				$url .= ('/' . $aname);
		} elseif ($cname == 'web/Topic') {
			if ($aname == 'postNew') {
				$url = $url_head . 'new';
			}
		} elseif ($cname == 'web/UserCenter') {
			$url = $url_head . 'member/' . $data ['username'];
			if ($aname != 'index') {
				$url .= ('/'.$aname);
			}
		} elseif ($cname == 'web/Admin') {
			$url = $url_head . 'hadmin';
			if ($aname != 'index')
				$url .= ('/' . $aname);
		}
		if ($xmlSafe)
			$url = str_replace ( '&', '&amp;', $url );
		return $url;
	}
}