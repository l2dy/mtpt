<?php
require_once("include/bittorrent.php");
dbconn();

$id = $_GET["torrent_id"];
if (is_numeric($id) && $id > 0) {
	$result = sql_query("SELECT name, descr, small_descr, category, source, url, dburl FROM torrents WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
	$data = array();
	if (mysql_num_rows($result)) {
		$data = mysql_fetch_array($result);
		$data["url"] = ($data["url"]) ? build_imdb_url($data["url"]) : "";
		$data["dburl"] = ($data["dburl"]) ? build_douban_url($data["dburl"]) : "";
		$data["exist"] = "yes";
	} else {
		$data["exist"] = "no";
	}
	$data = json_encode($data);
	echo $data;
}
?>