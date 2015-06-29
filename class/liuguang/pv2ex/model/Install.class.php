<?php

namespace liuguang\pv2ex\model;

use liuguang\pv2ex\model\BaseController;

class Install {
	private $controller;
	private $installed;
	private $dbType;
	private $conn;
	private $redis;
	private $errMsg;
	public function __construct(BaseController $controller) {
		// 配置文件中检测安装设置
		$this->controller = $controller;
		$this->installed = $controller->getAppConfig ()->get ( 'appinit', false );
		$this->dbType = $controller->getDbType ();
	}
	/**
	 * 判断数据库连接是否正常
	 *
	 * @return boolean
	 */
	public function statReady() {
		if ($this->dbType == BaseController::DB_MYSQL) {
			try {
				$this->conn = $this->controller->getConn ( true );
				return true;
			} catch ( \PDOException $e ) {
				$this->errMsg = $e->getMessage ();
				return false;
			}
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			try {
				$this->redis = $this->controller->getRedis ( true );
				return true;
			} catch ( \RedisException $e ) {
				$this->errMsg = $e->getMessage ();
				return false;
			}
		} else {
			$this->errMsg = '未知的数据库类型';
			return false;
		}
	}
	/**
	 * 判断是否已安装
	 *
	 * @return boolean
	 */
	public function hasInstalled() {
		return $this->installed;
	}
	/**
	 * 获取错误信息
	 *
	 * @return string
	 */
	public function getErrMsg() {
		return $this->errMsg;
	}
	/**
	 * 初始化数据库
	 *
	 * @return void
	 */
	public function initDb() {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->initDbMysql ();
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->initDbRedis ();
	}
	/**
	 * 初始化数据库mysql
	 *
	 * @return void
	 */
	private function initDbMysql() {
	}
	/**
	 * 初始化数据库redis
	 *
	 * @return void
	 */
	private function initDbRedis() {
		$redis = $this->redis;
		$tablePre = $this->controller->getTablePre ();
		// 清空当前数据库
		$redis->flushDB ();
		// 计数器
		$redis->hMset ( $tablePre . 'counter', array (
				'uid' => 0,
				'topicid' => 0,
				'replyid' => 0,
				'bkid' => 0 
		) );
		// 创建默认节点
		$bkid = $redis->hIncrBy ( $tablePre . 'counter', 'bkid', 1 );
		// 设置上级,节点的排序默认按id
		$redis->zAdd ( $tablePre . 'bkid:0:children', $bkid, $bkid );
		// 设置节点信息
		$redis->hMset ( $tablePre . 'bkid:' . $bkid . ':bkinfo', array (
				'pid' => 0,
				'bkid' => $bkid,
				'is_bk' => 1,
				'is_open' => 1,
				'need_login' => 0,
				'bkname' => '默认节点',
				'bk_alt' => '本节点是安装时自动生成的节点',
				'create_time' => time () 
		) );
		// 发表一篇默认帖子
		$topicModel=new Topics($this->controller);
		$topicModel->postTopic(0, $bkid, '你好世界', '你好，这是一个测试帖子');
		// 网站配置信息
		$redis->hMset ( $tablePre . 'site_conf', array (
				'sitename' => '流光论坛',
				'create_time' => time(),
				'notice_on' => 1,
				'notice_text' => '流光论坛安装成功',
				'index_bkid' => $bkid,
				'open_compress' => 1 
		) );
	}
}