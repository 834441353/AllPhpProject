<?php
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtaosoft.com
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 广告控制器
 */
if(!defined('IN_WSTMART')) {
	exit('Access Denied');
}

class Mysql {
	var $link;
	var $tablepre;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $dbcharset, $pconnect = 0, $tablepre='', $time = 0) {
		$this->tablepre = $tablepre;
		if($pconnect) {
			if(!$this->link = mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				die('{status:-1,msg:"Can not connect to MySQL server"}');
			}
		} else {
			if(!$this->link = mysql_connect($dbhost, $dbuser, $dbpw, 1)) {
				die('{status:-1,msg:"Can not connect to MySQL server"}');
			}
		}

		if($this->version() > '4.1') {
			if($dbcharset) {
				mysql_query("SET character_set_connection=".$dbcharset.", character_set_results=".$dbcharset.", character_set_client=binary", $this->link);
			}

			if($this->version() > '5.0.1') {
				mysql_query("SET sql_mode=''", $this->link);
			}
		}
		if($dbname) {
			$db_selected = mysql_select_db($dbname, $this->link);
			if (!$db_selected) {
				$sql="CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8;";
				self::query($sql);
				mysql_select_db($dbname, $this->link);
			}
		}

	}

	function query($sql) {
		return mysql_query($sql, $this->link);
	}

	function version() {
		return mysql_get_server_info($this->link);
	}

	function close() {
		return mysql_close($this->link);
	}
}

?>