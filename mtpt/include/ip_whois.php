<?php

$university_translations = array(
	'Tsinghua University' => '清华大学'
);

function distinguish_cernet($res, $original_ip) {
	global $university_translations;
	if (!isset($res['mnt-by']) || strpos($res['mnt-by'], 'CERNET') === false)
		return 'Non-CERNET: ' . $res['netname'];
	$netname = explode('-', $res['netname']);
	if (count($netname) == 1)
		$netname = $netname[0];
	else
		$netname = implode('-', array_slice($netname, 0, 2));
	$curl_options = array(
		CURLOPT_DNS_CACHE_TIMEOUT => 1,
		CURLOPT_FORBID_REUSE => true,
		CURLOPT_FRESH_CONNECT => true,
		CURLOPT_TIMEOUT => 5,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:58.0) Gecko/20100101 Firefox/58.0',
		CURLOPT_RETURNTRANSFER => true
	);
	$query_url = 'http://www.nic.edu.cn/member-cgi/i6obj?query=' . $original_ip;
	$curl_obj = curl_init($query_url);
	curl_setopt_array($curl_obj, $curl_options);
	$data = curl_exec($curl_obj);
	$error_no = curl_errno($curl_obj);
	curl_close($curl_obj);
	if ($error_no !== 0)
		return 'CERNET: Query Failed (' . $res['netname'] . ')';
	$data = mb_convert_encoding($data, 'utf-8', 'gb2312');
	$data = str_replace("\r", "", $data);
	$data = explode("\n", $data);
	$result = array();
	foreach ($data as $item) {
		if ($item[0] == ' '){
			$result[] = trim($item);
		}
	}
	if (count($result) < 3)
		return 'CERNET: Query Failed (' . $res['netname'] . ')';
	else {
		if (array_key_exists($result[2], $university_translations))
			$result[2] = $university_translations[$result[2]];
		return 'CERNET: ' . $result[2] . ' (' . $result[1] . ')';
	}
}

function ip_whois_query($query, $server='whois.iana.org') {
	$server = @dns_get_record($server, DNS_A);
	$server = $server[0]['ip'];
	$fp = fsockopen($server, 43, $errno, $errstr, 5);
	if ($fp === false)
		return array();
	stream_set_timeout($fp, 2);
	fwrite($fp, $query . "\r\n");
	$buffer = '';
	do {
		$buffer .= fgets($fp, 1024);
	} while (!feof($fp));
	fclose($fp);
	$buffer = rtrim($buffer);
	$buffer = explode("\n", $buffer);
	$res = array();
	foreach ($buffer as $item){
		$item = trim($item);
		if (strlen($item) == 0 || $item[0] == '%') continue;
		list($name, $value) = explode(":", $item);
		list($name, $value) = array(trim($name), trim($value));
		if (isset($res[$name]))
			$res[$name] .= "\n" . $value;
		else
			$res[$name] = $value;
	}
	if (isset($res['whois']))
		return ip_whois_query($query, $res['whois']);
	else return distinguish_cernet($res, $query);
}

