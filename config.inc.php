<?php
$config = array ();
$config ['errHandler'] = 'liuguang\\pv2ex\\model\\ErrHandler';
$config ['urlHandler'] = 'liuguang\\pv2ex\\model\\Pv2exUrl';
$config ['dblist'] = array (
		0 => array (
				'dsn' => 'mysql:host=localhost;port=3306;dbname=test',
				'username' => 'root',
				'password' => 'root',
				'options' => array (
						PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' 
				) 
		) 
);
$config ['fslist'] = array (
		0 => array (
				'type' => 'Local',
				'config' => array (
						'bucketName' => 'upfile' 
				) 
		) 
);
$config ['controllerNs'] = 'liuguang\\pv2ex\\controller';
$config ['db_type'] = 'redis'; // or mysql
$config ['db_id'] = 0;
$config ['table_pre'] = 'pv2ex';
// -----
$config ['redis_host'] = '127.0.0.1';
$config ['redis_port'] = 6379;
$config ['redis_pass'] = 'root';
$config ['appinit'] = false;