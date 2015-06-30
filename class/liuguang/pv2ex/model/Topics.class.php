<?php

namespace liuguang\pv2ex\model;

/**
 *
 * 论坛帖子的接口
 *
 * @author liuguang
 *        
 */
class Topics {
	private $conn;
	private $redis;
	private $dbType;
	private $errMsg;
	private $tablePre;
	private $dictPath;
	public function __construct(BaseController $controller) {
		$this->dbType = $controller->getDbType ();
		$this->tablePre = $controller->getTablePre ();
		$this->dictPath = $controller->getAppConfig ()->get ( 'mvc_static_path' ) . DIRECTORY_SEPARATOR . 'scws' . DIRECTORY_SEPARATOR . 'dict.utf8.xdb';
		if ($this->dbType == BaseController::DB_MYSQL) {
			$this->conn = $controller->getConn ();
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			$this->redis = $controller->getRedis ();
		}
	}
	/**
	 * 发表帖子的方法
	 *
	 * @param int $uid        	
	 * @param int $bkid        	
	 * @param string $title        	
	 * @param string $content        	
	 * @return int 返回帖子的id
	 */
	public function postTopic($uid, $bkid, $title, $content) {
		if ($this->dbType == BaseController::DB_MYSQL) {
			return $this->postTopicM ( $uid, $bkid, $title, $content );
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			return $this->postTopicR ( $uid, $bkid, $title, $content );
		}
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function postTopicM($uid, $bkid, $title, $content) {
	}
	private function postTopicR($uid, $bkid, $title, $content) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		// 获取帖子id
		$topicid = $redis->hIncrBy ( $tablePre . 'counter', 'topicid', 1 );
		// 获取帖子的关键字
		if (function_exists ( 'scws_new' )) {
			$so = scws_new ();
			$so->set_charset ( 'utf-8' );
			$words = array ();
			$textArr = array (
					$title,
					$content 
			);
			$so->set_dict ($this->dictPath);
			foreach ( $textArr as $text ) {
				$so->send_text ( $text );
				while ( $tmp = $so->get_result () ) {
					foreach ( $tmp as $strTmp ) {
						if (mb_strlen ( $strTmp ['word'], 'UTF-8' ) > 1)
							$words [] = $strTmp ['word'];
					}
				}
			}
			$so->close ();
			$words = array_unique ( $words );
			//批量添加关键字索引脚本
			$lua='local arrLength=#(ARGV)
			local topicid=ARGV[arrLength]
			local tablePre=ARGV[arrLength-1]
			for i=1, (arrLength-2) do      
			    redis.call(\'zadd\',tablePre..\'keywords:\'..ARGV[i]..\':topiclist\',topicid,topicid)
			end';
			$words[]=$tablePre;
			$words[]=$topicid;
			$redis->eval($lua,$words);
		}
		// 向全局最新帖子列表中加入此帖子id
		$redis->lPush ( $tablePre . 'new_posts', $topicid );
		// 向所在板块最新帖子列表中加入此帖子id
		$redis->lPush ( $tablePre . 'bkid:' . $bkid . ':new_posts', $topicid );
		//向所在板块的父节点插入帖子记录
		$pid=$redis->hGet($tablePre.'bkid:'.$bkid.':bkinfo','pid');
		$redis->lPush ( $tablePre . 'bkid:' . $pid . ':child_posts', $topicid );
		// 将帖子加入所在板块的帖子有序集合中,按时间排序
		$postTime = time ();
		$redis->zAdd ( $tablePre . 'bkid:' . $bkid . ':posts', $postTime, $topicid );
		// 存放帖子信息
		$topicInfo = array (
				'topicid' => $topicid,
				'bkid'=>$bkid,
				'uid' => $uid,
				'title' => $title,
				'content' => $content,
				'post_time' => $postTime,
				'last_update' => 0,
				'view_num' => 0,
				'reply_num' => 0,
				'score' => 0,
				'score_uid' => 0,
				'score_comment' => 0 
		);
		$redis->hMset ( $tablePre . 'topicid:' . $topicid . ':info', $topicInfo );
		return $bkid;
	}
}