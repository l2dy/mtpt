<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

function die400($text){
	header('Status: 400');
	die($text);
}

function die500($text){
	header('Status: 500');
	die($text);
}

if(!isset($_GET['type'])) die400("Missing Request Param");
if(!isset($_GET['url'])) die400("Missing Request Param");
$request_type = $_GET['type'];
$target_url = $_GET['url'];
if (!filter_var($target_url, FILTER_VALIDATE_URL))
	die400("Invalid Request Param");
$target_url_components = parse_url($target_url);

switch ($request_type){
case 'info':
	$allowed_url_hosts = array('www.imdb.com');
	if (!in_array($target_url_components['host'], $allowed_url_hosts))
		die400('Invalid Request Param');
	if (substr($target_url_components['path'], 0, 7) != "/title/")
		die400('Invalid Request Param');
	$subject_id = substr($target_url_components['path'], 7);
	$subject_id = rtrim($subject_id, '/');
	$info_api = "http://www.theimdbapi.org/api/movie?movie_id=$subject_id";
	// Initialize cUrl Object
	$curl_obj = curl_init($info_api);
	$curl_options = array(
		CURLOPT_DNS_CACHE_TIMEOUT => 1,
		CURLOPT_FORBID_REUSE => true,
		CURLOPT_FRESH_CONNECT => true,
		CURLOPT_TIMEOUT => 5,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
		CURLOPT_RETURNTRANSFER => true
	);
	curl_setopt_array($curl_obj, $curl_options);
	$data = curl_exec($curl_obj);
	$error_no = curl_errno($curl_obj);
	curl_close($curl_obj);
	if ($error_no !== 0)
		die500('An error occurred, cUrl error code is ' . $error_no);
	$data = json_encode(json_decode($data, true));
	header('Content-Type: application/json');
	echo $data;
	break;
case 'image':
	/*$allowed_url_hosts = array('images-na.ssl-images-amazon.com');
	if (!in_array($target_url_components['host'], $allowed_url_hosts))
		die400('Invalid Request Param');
	$curl_obj = curl_init($target_url);
	$curl_options = array(
		CURLOPT_FORBID_REUSE => true,
		CURLOPT_FRESH_CONNECT => true,
		CURLOPT_TIMEOUT => 5,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
		CURLOPT_RETURNTRANSFER => true
	);
	curl_setopt_array($curl_obj, $curl_options);
	$data = curl_exec($curl_obj);
	$error_no = curl_errno($curl_obj);
	curl_close($curl_obj);
	if ($error_no !== 0)
		die400('An error occurred, cUrl error code is ' . $error_no);
	header('Content-Type: image/jpeg');
	echo $data;*/
	echo 'We cannot fetch this image due to some unspeakable problems.';
	break;
default:
	die400("Invalid Request Param?");
}

?>
