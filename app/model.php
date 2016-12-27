<?php

class DB {

	public $db;

	public static function connect() {
		return new self;
	}

	function __construct() {

		$this->db = new PDO('sqlite:' . ROOT . '/app/base.db');

		// Notes table
		$this->db->exec("CREATE TABLE IF NOT EXISTS notes (
			id     INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			title  TEXT,
			txt    TEXT        NOT NULL,
			search TEXT,
			media  TEXT,
			pos    INTEGER     NOT NULL,
			shared BOOLEAN     NOT NULL,
			uid    INTEGER     NOT NULL,
			date   DATETIME    NOT NULL
		)");

		// Users table
		$this->db->exec("CREATE TABLE IF NOT EXISTS users (
			id     INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
			login  VARCHAR(32) NOT NULL UNIQUE,
			pass   VARCHAR(32) NOT NULL,
			param  TEXT
		)");
	}
}

class NoteModel {

	public static function load($offset=0, $count=10, $uid) {
		$q = DB::connect()->db->query("SELECT id, title, txt, pos, date, shared, media 
			FROM notes WHERE uid = '$uid' ORDER BY pos DESC LIMIT '$offset', '$count'");
		return $q->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function get($id, $uid) {
		$q = DB::connect()->db->query("SELECT id, title, txt, pos, date, shared, media 
			FROM notes WHERE id = '$id' AND uid = '$uid'");
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public static function getLast($uid) {
		$q = DB::connect()->db->query("SELECT id, title, txt, pos, date, shared, media 
			FROM notes WHERE uid = '$uid' ORDER BY id DESC LIMIT 0, 1");
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public static function getShared($id) {
		$q = DB::connect()->db->query("SELECT id, title, txt, pos, date, shared, media 
			FROM notes WHERE id = '$id' AND shared = 1");
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public static function update($id, $title, $txt, $uid) {
		$date   = time();
		$media  = Helper::mediaStr($txt);
		$media  = SQLite3::escapeString($media);
		$search = Helper::searchStr($title, $txt);
		$search = SQLite3::escapeString($search);
		$title  = SQLite3::escapeString($title);
		$txt    = SQLite3::escapeString($txt);
		DB::connect()->db->exec("UPDATE OR IGNORE notes 
			SET title = '$title', txt = '$txt', date = '$date', search = '$search', media = '$media' 
			WHERE id = '$id' AND uid = '$uid'");
	}

	public static function write($title, $txt, $uid) {
		$date   = time();
		$media  = Helper::mediaStr($txt);
		$media  = SQLite3::escapeString($media);
		$search = Helper::searchStr($title, $txt);
		$search = SQLite3::escapeString($search);
		$title  = SQLite3::escapeString($title);
		$txt    = SQLite3::escapeString($txt);
		$max = DB::connect()->db->query("SELECT MAX(pos) AS max FROM notes WHERE uid = '$uid'");
		$max = $max->fetch(PDO::FETCH_ASSOC)['max']+1;
		DB::connect()->db->exec("INSERT INTO notes (title, txt, pos, date, search, media, shared, uid) 
			VALUES ('$title', '$txt', '$max', '$date', '$search', '$media', 0, '$uid')");
	}

	public static function delete($id, $uid) {
		DB::connect()->db->exec("DELETE FROM notes WHERE id = '$id' AND uid = '$uid'");
	}

	public static function share($id, $shared, $uid) {
		DB::connect()->db->exec("UPDATE notes SET shared = '$shared' WHERE id = '$id' AND uid = '$uid'");
	}

	public static function order($id, $pos, $new, $uid) {
		$max = max($pos, $new);
		$min = min($pos, $new);
		$add = ($pos > $new) ? 1 : -1;
		// Get rows for change
		$q = DB::connect()->db->query("SELECT id,pos FROM notes WHERE pos BETWEEN '$min' AND '$max' AND uid = '$uid'");
		$row = $q->fetchAll(PDO::FETCH_ASSOC);
		// Update pos
		$sql = '';
		foreach ($row as $k => $v) {
			$v['pos'] = ($v['id'] != $id) ? $v['pos'] + $add : $new;
			$sql .= "UPDATE notes SET pos='$v[pos]' WHERE id='$v[id]';";
		}
		DB::connect()->db->exec($sql);
	}

	public static function search($str, $uid) {
		$str = trim($str);
		$str = mb_strtoupper($str);
		$q = DB::connect()->db->query("SELECT id, title, txt, pos, date, shared, media FROM notes 
			WHERE search LIKE '%$str%' OR media LIKE '%$str%' AND uid = '$uid' ORDER BY pos DESC");
		return $q->fetchAll(PDO::FETCH_ASSOC);
	}
}

class UserModel {

	public static function add($login, $pass) {
		DB::connect()->db->exec("INSERT INTO users (login, pass) VALUES ('$login', '$pass')");
	}

	public static function getId($login) {
		$q = DB::connect()->db->query("SELECT id FROM users WHERE login = '$login'");
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public static function getPass($id) {
		$q = DB::connect()->db->query("SELECT pass FROM users WHERE id = '$id'");
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public static function setPass($id, $pass) {
		DB::connect()->db->exec("UPDATE OR IGNORE users SET pass = '$pass' WHERE id = '$id'");
	}

	public static function getParam($id) {
		$q = DB::connect()->db->query("SELECT param FROM users WHERE id = '$id'");
		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public static function setParam($id, $param) {
		DB::connect()->db->exec("UPDATE OR IGNORE users SET param = '$param' WHERE id = '$id'");
	}

	public static function delete($id) {
		DB::connect()->db->exec("DELETE FROM users WHERE id = '$id'");
		DB::connect()->db->exec("DELETE FROM notes WHERE uid = '$id'");
	}
}

?>