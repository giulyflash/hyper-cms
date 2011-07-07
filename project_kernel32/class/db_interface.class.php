<?php
interface db_interface{
	public function __destruct();
	public function query($query);
	public static function escstr($string);
	public function insert_id();
}
?>