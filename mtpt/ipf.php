<?php
function ip_filter($ip,$ipv6 = true,$ipv4 = true)
{
$banIpv6 = array(
'0:0:0:0:0:0:0:0-2001:0:ffff:ffff:ffff:ffff:ffff:ffff',
'2001:260:0:0:0:0:0:0 - 2001:480:ffff:ffff:ffff:ffff:ffff:ffff',
'2001:41d0:0:0:0:0:0:0-2001:41d0:ffff:ffff:ffff:ffff:ffff:ffff',
'2001:4858:0:0:0:0:0:0 - 2001:4858:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:5C0:0:0:0:0:0:0 - 2001:5C0:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:B000:0:0:0:0:0:0 - 2001:B7FF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:BC8:0:0:0:0:0:0 - 2001:BC8:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:C08:0:0:0:0:0:0 - 2001:C08:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:CCA:0:0:0:0:0:0 - 2001:CCF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:E00:0:0:0:0:0:0 - 2001:E01:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2002:0:0:0:0:0:0:0 - 2002:FFFF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2401:EC01:0:0:0:0:0:0 - 2401:ECFF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2402:F001:0:0:0:0:0:0 - 2402:F0FF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'240C:0:0:0:0:0:0:0 - 240C:F:FFFF:FFFF:ffff:ffff:ffff:ffff',
'240E:0:0:0:0:0:0:0 - 240E:FFF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2605:F700:0:0:0:0:0:0 - 2605:F700:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2607:F8B0:0:0:0:0:0:0 - 2607:F8B0:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2A01:E00:0:0:0:0:0:0- 2A01:E3F:FFFF:FFFF:ffff:ffff:ffff:ffff'

);
$banIpv4 = array(
	// '0.0.0.0-255.255.255.255'
	);
	if($ipv6 && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6|FILTER_FLAG_NO_RES_RANGE ))
	{
		//v6地址
		if(isset($banIpv6[0]))
		{
			foreach($banIpv6 as $v)
			{
				list($a,$b) = explode('-',$v);
				if(ipv6_compare($ip,$a) >= 0 && ipv6_compare($b,$ip) >= 0)
					return false;//在指定banned范围内
			}
		}
		return true;
	}
	elseif($ipv4 && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4|FILTER_FLAG_NO_RES_RANGE))
	{
		//v4地址
		if(isset($banIpv4[0]))
		{
			$ipnum = _ip2long($ip);
			foreach($banIpv4 as $v)
			{
				list($a,$b) = explode('-',$v);
				if(_ip2long($a) <= $ipnum && _ip2long($b)>= $ipnum)
					return false;
			}		
		}
		return true;
	}
	else
		return false;
}
/* 溢出fixed */
function _ip2long($a)
{
	return sprintf("%u",ip2long($a));
}
/* 要求格式完整 */
function ipv6_compare($a,$b)
{
	$a_arr = explode(':',$a);
	$b_arr = explode(':',$b);
	foreach($a_arr as $k => $v)
	{
		$c = hexdec($a_arr[$k]) - hexdec($b_arr[$k]) ;
		// $c = strcasecmp($a_arr[$k],$b_arr[$k]);
		if($c === 0)
			continue;
		else
			return $c;
	}
	return 0;
}
$bannedV6 = array(
'0:0:0:0:0:0:0:0-2001:0:ffff:ffff:ffff:ffff:ffff:ffff',
'2001:260:0:0:0:0:0:0 - 2001:480:ffff:ffff:ffff:ffff:ffff:ffff',
'2001:41d0:0:0:0:0:0:0-2001:41d0:ffff:ffff:ffff:ffff:ffff:ffff',
'2001:4858:0:0:0:0:0:0 - 2001:4858:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:5C0:0:0:0:0:0:0 - 2001:5C0:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:B000:0:0:0:0:0:0 - 2001:B7FF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:BC8:0:0:0:0:0:0 - 2001:BC8:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:C08:0:0:0:0:0:0 - 2001:C08:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:CCA:0:0:0:0:0:0 - 2001:CCF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2001:E00:0:0:0:0:0:0 - 2001:E01:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2002:0:0:0:0:0:0:0 - 2002:FFFF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2401:EC01:0:0:0:0:0:0 - 2401:ECFF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2402:F001:0:0:0:0:0:0 - 2402:F0FF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'240C:0:0:0:0:0:0:0 - 240C:F:FFFF:FFFF:ffff:ffff:ffff:ffff',
'240E:0:0:0:0:0:0:0 - 240E:FFF:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2605:F700:0:0:0:0:0:0 - 2605:F700:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2607:F8B0:0:0:0:0:0:0 - 2607:F8B0:FFFF:FFFF:ffff:ffff:ffff:ffff',
'2A01:E00:0:0:0:0:0:0- 2A01:E3F:FFFF:FFFF:ffff:ffff:ffff:ffff'

);
$bannedV4 = array(
	// '0.0.0.0-255.255.255.255'
	);
	
	
header('Content-type:text/html;charset=utf-8');
echo '测试开始';
echo '<hr/><br />当前规则：允许所有ip访问麦田网站。但是由于ipv4和v6隧道会计费，所以不允许以下ip段连接ut做种。会提示invalid ip。<br />如果同时使用外站pt请将下列ip添加至ipfilter<br />';
//echo 'ipv6:On '.' <br />';
//echo 'ipv4:On '.' <br /><br /><br />';
echo '<br />banned Ipv4:<br />';
foreach($bannedV4 as $v4){echo $v4."<br />";};
echo ' <br />banned Ipv6: <br />';
foreach($bannedV6 as $v6){echo $v6."<br />";};
// $test = array('222.178.10.244');
// $d = array(

$test = array('2001:250:1002:240c:9c0:f1d8:147:8f3e',
'2002:259:111::',
'2002:dac5:e517:c:5def:8e7d:bf31:18b8',
'240c:f:1:6000::2821',
'240c:f:1:6000::2a6e',
'113.140.84.102');

echo '测试ip数量：'.count($test).'<br/>';

foreach($test as $v)
{
	echo '测试地址：<a href="http://ip.zxinc.org/ipquery/?ip='.$v.'"'."target='_blank'".' >'.$v.'</a>---->'.(ip_filter($v,true,true)?'<font color="green">允许</font>':'<font color="red">禁止</font>').'<br/>';
}
?>
