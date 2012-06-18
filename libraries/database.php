<?php
/*
 * @package Hycus.Framework
 * @copyright Copyright 2010, Hycus.com. All rights reserved.
 * @license	GNU/GPL, see gpl.html
 * Hycus-CMS! is free and open source PHP based content management System.
 */

defined( 'HYCUSPAGEPROTECT' ) or die( 'You don\'t have permission to view this page.' );

class hdatabase {
	//commented for raw code display bug by vasanth
	//var $dbhost, $dbname, $dbuser, $dbpass, $dbtype, $dbprefix;

	function hdatabase(){
		global $dbhost,$dbname,$dbuser,$dbpass,$dbtype;
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbtype = $dbtype;
		$this->dblink();
	}

	function dblink() {
		//connects to the mysql/postgresql.
		if($this->dbtype == "MySQL"){
			$link = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass);
			mysql_select_db($this->dbname);
			mysql_query("set names 'utf8'");
			mysql_query("set character set utf8");
		} else if($this->dbtype == "PostgreSQL"){
			$link = pg_connect("host={$this->dbhost} dbname={$this->dbname} user={$this->dbuser} password={$this->dbpass}");
		}
		return $link;
	}

	function get_recs($table, $fields, $where="", $order="", $start="", $limit=""){
		//used to retrieve data from multiple rows in mysql/postgresql.

		$table = $this->prefixsetter($table);
		$q = "select $fields from $table";
		if($where) $q .= " where $where";
		if($order) $q .= " order by $order";
		if($limit && $start) $q .= " LIMIT $start, $limit";
		elseif($limit) $q .= " LIMIT 0, $limit";

		if($this->dbtype == 'MySQL') $result = mysql_query($q);
		else if($this->dbtype == 'PostgreSQL') $result = pg_query($q);
		if(!$result) return false;
		if($this->dbtype == 'MySQL') while($rec = mysql_fetch_object($result)) $recs[] = $rec;
		else if($this->dbtype == 'PostgreSQL') while($rec = pg_fetch_object($result)) $recs[] = $rec;
		return $recs;
	}

	function get_rec($table, $fields, $where="", $order=""){
		//used to retrieve data from single row in mysql/postgresql.

		$table = $this->prefixsetter($table);
		$q = "select $fields from $table";
		if($where) $q .= " where $where";
		if($order) $q .= " order by $order";

		if($this->dbtype == 'MySQL') {
			$result = mysql_query($q);
			if($result) $rec = mysql_fetch_object($result);
			else return false;
		} else if($this->dbtype == 'PostgreSQL') {
			$result = pg_query($q);
			if($result) $rec = pg_fetch_object($result);
			else return false;
		}
		return $rec;
	}

	function fetch_objects($result){
		/*used to fetch data from the object.
		 * We haven't used this method in hycus cms as this code has been made as part of the get_recs function*/

		if(!$result) return false;
		if($this->dbtype == 'MySQL') while($rec = mysql_fetch_object($result)) $recs[] = $rec;
		else if($this->dbtype == 'PostgreSQL') while($rec = pg_fetch_object($result)) $recs[] = $rec;
		return $recs;
	}

	function count_recs($result){
		//used to get the number of rows retrieved.

		if(!$result) return false;
		if($this->dbtype == 'MySQL') $rec_count = mysql_num_rows($result);
		else if($this->dbtype == 'PostgreSQL') $rec_count = pg_num_rows($result);
		return $rec_count;
	}

	function db_update($table, $pairs, $where){
		//updates the database table

		$table = $this->prefixsetter($table);
		if(is_array($pairs)) $fields = implode(", ", $pairs);
		else $fields = $pairs;
		$q = "update $table set $fields where $where";
		if($this->dbtype == 'MySQL') $result = mysql_query($q);
		else if($this->dbtype == 'PostgreSQL') $result = pg_query($q);
		if($result) return true;
		else return false;
	}

	function db_replace($table, $fields, $values){
		//replaces table fields.
		$table = $this->prefixsetter($table);
		$q = "replace into $table ($fields) values ($values)";
		if($this->dbtype == 'MySQL') {
			$result = mysql_query($q);
			//$id = mysql_insert_id();
		} else if($this->dbtype == 'PostgreSQL') {
			$result = pg_query($q);
			$r = $this->get_rec($table, "id", pg_last_oid());
			$id = $r->id;
		}
		if($result) return true;
		else return false;
	}

	function db_insert($table, $fields, $values){
		//inserts a new row in a table
		$table = $this->prefixsetter($table);
		$q = "insert into $table ($fields) values ($values)";
		if($this->dbtype == 'MySQL') {
			$result = mysql_query($q);
			$id = mysql_insert_id();
		} else if($this->dbtype == 'PostgreSQL') {
			$result = pg_query($q);
			$r = $this->get_rec($table, "id", pg_last_oid());
			$id = $r->id;
		}
		if($result) return $id;
		else return false;
	}

	function db_delete($table, $where){
		//deletes a row from table.
		$table = $this->prefixsetter($table);
		$q = "delete from $table";
		if($where)
		$q .= " where $where";
		if($this->dbtype == 'MySQL') $result = mysql_query($q);
		else if($this->dbtype == 'PostgreSQL') $result = pg_query($q);
		if($result) return true;
		else return false;
	}
	function prefixsetter($table)
	{
		//sets the actual database prefix to the query.
		global $dbprefix;
		return str_replace("#__", $dbprefix, "$table");
	}
}
?>
