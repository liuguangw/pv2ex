<?php

namespace liuguang\pv2ex\model;

use liuguang\mvc\Application;
use liuguang\mvc\PdoDb;

class BaseController {
	const DB_REDIS = 1;
	const DB_MYSQL = 2;
	protected $app;
	protected $appConfig;
	protected $tablePre;
	protected $dbType;
	protected $redis;
	protected $conn;
	public function __construct() {
		$app = Application::getApp ();
		$appConfig = $app->getAppConfig ();
		$tablePre = $appConfig->get ( 'table_pre', 'pv2ex' );
		$dbType = $appConfig->get ( 'db_type', 'mysql' );
		$this->app = $app;
		$this->appConfig = $appConfig;
		$this->tablePre = $tablePre;
		if ($dbType == 'redis')
			$this->dbType = self::DB_REDIS;
		else
			$this->dbType = self::DB_MYSQL;
	}
	/**
	 * 获取应用的单例
	 *
	 * @return Application
	 */
	public function getApp() {
		return $this->app;
	}
	
	/**
	 * 获取应用的配置对象
	 *
	 * @return \liuguang\mvc\DataMap
	 */
	public function getAppConfig() {
		return $this->appConfig;
	}
	
	/**
	 * 获取表前缀
	 *
	 * @return string
	 */
	public function getTablePre() {
		return $this->tablePre;
	}
	
	/**
	 * 获取数据库类型
	 *
	 * @return int
	 */
	public function getDbType() {
		return $this->dbType;
	}
	public function getDbTypeStr() {
		if ($this->dbType == self::DB_MYSQL)
			return 'mysql';
		elseif ($this->dbType == self::DB_REDIS)
			return 'redis';
		else
			return 'undefined';
	}
	
	/**
	 * 获取redis实例
	 *
	 * @param boolean $throwErr
	 *        	首次连接出错时，是否抛出异常
	 *        	如果为true，则出错时抛出异常，否则调用错误处理器处理错误信息.值默认为false
	 *        	
	 * @return \Redis
	 */
	public function getRedis($throwErr = false) {
		if (! isset ( $this->redis )) {
			$redis = new \Redis ();
			$appConfig = $this->appConfig;
			$redisHost = $appConfig->get ( 'redis_host', '127.0.0.1' );
			$redisPort = $appConfig->get ( 'redis_port', 6379 );
			$pass = $appConfig->get ( 'redis_pass', '' );
			$dbId = $appConfig->get ( 'db_id', 0 );
			if ($throwErr) {
				if (! $redis->connect ( $redisHost, $redisPort, 2.5 ))
					throw new \RedisException ( '连接redis服务器失败' );
				if (! $redis->auth ( $pass ))
					throw new \RedisException ( 'redis服务器密码错误' );
				if (! $redis->select ( $dbId ))
					throw new \RedisException ( '选择redis数据库失败' );
			} else {
				$errHandler = $this->app->getErrHandler ();
				try {
					if (! $redis->connect ( $redisHost, $redisPort, 2.5 ))
						throw new \RedisException ( '连接redis服务器失败' );
					if (! $redis->auth ( $pass ))
						throw new \RedisException ( 'redis服务器密码错误' );
					if (! $redis->select ( $dbId ))
						throw new \RedisException ( '选择redis数据库失败' );
				} catch ( \RedisException $e ) {
					$errHandler->handle ( 500, $e->getMessage () );
				}
			}
			$this->redis = $redis;
		}
		return $this->redis;
	}
	
	/**
	 * 获取数据库对象
	 *
	 * @param boolean $throwErr
	 *        	首次连接出错时，是否抛出异常
	 *        	如果为true，则出错时抛出异常，否则调用错误处理器处理错误信息.值默认为false
	 *        	
	 * @return \PDO
	 */
	public function getConn($throwErr = false) {
		if (! isset ( $this->conn )) {
			$dbId = $this->appConfig->get ( 'db_id', 0 );
			if ($throwErr) {
				$this->conn = PdoDb::getConn ( $dbId, true );
			} else {
				try {
					$this->conn = PdoDb::getConn ( $dbId, true );
				} catch ( \PDOException $e ) {
					$errHandler = $this->app->getErrHandler ();
					$errHandler->handle ( $e->getCode (), $e->getMessage () );
				}
			}
		}
		return $this->conn;
	}
	/**
	 * 返回项目的安装状态，已安装则返回true，否则返回false
	 *
	 * @return boolean
	 */
	public function isAppInit() {
		return $this->appConfig->get ( 'appinit', false );
	}
	/**
	 * 除安装页面外，每个控制器必须执行的检测安装状态
	 *
	 * @return void
	 */
	public function forceInstall() {
		if (! $this->isAppInit ()) {
			$urlHandler = $this->app->getUrlHandler ();
			header ( 'Location: ' . $urlHandler->createUrl ( 'web/Install', 'index', array (), false ) );
			exit ();
		}
	}
	public function jsonReturn(array $arr) {
		header ( 'Content-type: application/json; charse=utf-8' );
		echo json_encode ( $arr );
	}
}