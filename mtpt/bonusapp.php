<?php
require "include/bittorrent.php";
require "./memcache.php";
dbconn();
loggedinorreturn();
parked();
//print_r("<script type=\"text/javascript\" >if(confirm(\"本次改名将花费，请确认！\")){}else{window.history.back(-1);}</script>");
stdhead($CURUSER['username'] . $lang_mybonus['head_karma_page']);
$action = htmlspecialchars($_POST['action']);


$changenamecharge = 10000;//默认改名价格
$changenamechargemax = 100000;//max
$ycharge = array();//预设价格
$discount = 1;
	$ycharge['setallfree'] = 2000*$discount;//免费
	$ycharge['setall2up'] = 2000*$discount;//2x上传
	$ycharge['setall2up_free'] = 5000*$discount;//免费&2x上传
	$ycharge['setallhalf_down'] = 1200*$discount;//50%下载
	$ycharge['setall2up_half_down'] =3500*$discount;//50%下载&2X上传
	$ycharge['top'] =4000*$discount;//置顶
	
if (!$action) {
	print_r("<script type=\"text/javascript\" >alert(\"参数错误, 返回前一个页面\");window.history.back(-1);</script>");
	echo "参数错误";
}
$userid=$CURUSER['id'];
$userbouns=(int)$CURUSER['seedbonus'];
// changename
if ($action == "changename") {
	$charge=-1;
	$newname=htmlspecialchars($_POST['newname']);
	$oldname=$CURUSER['username'];
	if(utf8_strlen($newname)>14||utf8_strlen($newname)<4|| !validusername($newname))
	{
		echo "名字不符合要求";
		die();
	}
	if($newname == $oldname) {echo "新旧用户名一样，无需更改";die();}

	if($res=sql_query("SELECT namecharge from bonusapp where userid ='".$userid."'")or sqlerr(__FILE__, __LINE__))
	{
		$row = mysql_fetch_array( $res );
		$charge=$row['namecharge'];
	}
	if($charge<=0)
	{
		$charge=$changenamecharge;
		if(preg_match("/^[\d][\d]*[\d]$/",$oldname))
		{
			$charge=$charge/2;
		}
		//echo $userbonus;
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(@sql_query("UPDATE users SET username ='".$newname."' WHERE id = ".$userid))
		{
			sql_query("UPDATE users SET seedbonus =seedbonus -".$charge." WHERE id = ".$userid) or sqlerr(__FILE__, __LINE__);
			echo "改名成功，本次消费麦粒".$charge."，欢迎再次光临^_^";
			$charge=$charge*2;
			sql_query("INSERT into bonusapp(userid, namecharge) values(".$userid.",". $charge.")") or sqlerr(__FILE__, __LINE__);
		}else
		{
			echo "修改失败，该用户名可能已被占用";
			die();
		}
	}
	else
	{	
		if($charge>$changenamechargemax)
		{
			$charge=$changenamechargemax;
		}
		if(preg_match("/^[\d][\d]*[\d]$/",$oldname))
		{
			$charge=$charge/2;
		}
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(@sql_query("UPDATE users SET username ='".$newname."' WHERE id = ".$userid))
		{
			sql_query("UPDATE users SET seedbonus =seedbonus -".$charge." WHERE id = ".$userid) or sqlerr(__FILE__, __LINE__);
			echo "改名成功，本次消费麦粒".$charge."，欢迎再次光临^_^。";
			$charge=$charge*2;
			sql_query("UPDATE bonusapp set namecharge=".$charge." where userid=".$userid) or sqlerr(__FILE__, __LINE__);
		}else
		{
			echo "修改失败，该用户名可能已被占用";
			die();
		}
	}	
	$charge=$charge/2;
	sendshoutbox("薄情寡义的[@$CURUSER[username]] 不喜欢自己的名字啦，花了$charge 个麦粒给自己换了个新名字叫[@$newname]快来围观土豪吧¬_¬");
	writeBonusComment($CURUSER[id],"用户名由$CURUSER[username]更换为$newname ，花费麦粒$charge");
	writeModComment($CURUSER[id],"用户名由$CURUSER[username]更换为$newname ，花费麦粒$charge");
	record_op_log(0,$CURUSER[id],htmlspecialchars($CURUSER['username']),"change",$CURUSER['username']."--花费麦粒自助更名为--". $newname);
	
	print("<br>建议重新登录，<a href=\"logout.php\" align=\"center\">点我去登录</a>");
}
function checktorrent($torrentid)
{	
	$res=@sql_query("SELECT * from torrents where id =".$torrentid)or sqlerr(__FILE__, __LINE__);
	$row = mysql_fetch_array( $res );
	if ($row['pos_state'] != 'normal') die('抱歉，不允许对置顶的种子购买促销');
	return $row;
}

if($action=="prm"){//促销
	
	
	$id=htmlspecialchars($_POST['torrentid']);
	$prmtype=htmlspecialchars($_POST['type']);
	$time=htmlspecialchars($_POST['time']);
	
	if(!preg_match("/^[\d]{1,8}$/",$id)||!preg_match("/^[\d]{1,8}$/",$time)||$prmtype<'2'||$prmtype>'7')
	{
		echo "再玩 ban了你";
		die();
	}
	$torrentid=(int)$id;
	$prmtime=(int)$time;
	$record=checktorrent($torrentid);
	if(!$record)
	{
		echo "不要玩了，没有该ID的种子";
		die();
	}
	if((((int)$record['sp_state'])==2||((int)$record['sp_state'])==4)&&((int)$record['endfree'])==0)
		{
		if ($prmtype!="7"){
			echo "你在玩我么，该种子已经永久免费了";
			die();}
		}

	$charge=0;
	if($prmtype=="2"){
		$charge=((int)$prmtime)*$ycharge['setallfree'];
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}

		if(@sql_query("UPDATE torrents SET sp_state = 2 where id=".$torrentid))
		{
			echo $info = $CURUSER['username']."花费了".$charge."个麦粒，种子 ".$record['name']." 种子设置为免费".$prmtime."天";
		}
	}
	else if($prmtype=="3"){
		$charge=((int)$prmtime)*$ycharge['setall2up'];
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(@sql_query("UPDATE torrents SET sp_state = 3 where id=".$torrentid))
		{	
			echo $info = $CURUSER['username']."花费了".$charge."个麦粒，种子 ".$record['name']." 种子设置为2倍上传".$prmtime."天";
		}
	}
	else if($prmtype=="4"){
		$charge=((int)$prmtime)*$ycharge['setall2up_free'];
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(@sql_query("UPDATE torrents SET sp_state = 4 where id=".$torrentid))
		{			
			echo $info = $CURUSER['username']."花费了".$charge."个麦粒，种子 ".$record['name']." 种子设置为2倍上传并且免费".$prmtime."天";
		}
	}
	else if($prmtype=="5"){
		$charge=((int)$prmtime)*$ycharge['setallhalf_down'];
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(@sql_query("UPDATE torrents SET sp_state = 5 where id=".$torrentid))
		{
			echo $info = $CURUSER['username']."花费了".$charge."个麦粒，种子 ".$record['name']." 种子设置为50&下载".$prmtime."天";
		}
	}
	else if($prmtype=="6"){
		$charge=((int)$prmtime)*$ycharge['setall2up_half_down'];
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(@sql_query("UPDATE torrents SET sp_state = 6 where id=".$torrentid))
		{
			echo $info = $CURUSER['username']."花费了".$charge."个麦粒，种子 ".$record['name']." 种子设置为50%下载并且免费".$prmtime."天";
		}
	}
	else if($prmtype=="7"){//置顶
	checktorrent($torrentid);
		if($prmtime>24)
		{
			echo "不要贪心，置顶最多允许购买24 小时，已为你改变为购买24 小时，我聪明吧^_^";
			$prmtime=24;
		}
		$charge=((int)$prmtime)*$ycharge['top'];
		$deadline=date('Y-m-d H:i:s',time()+(3600*$prmtime));
		if($charge>$userbouns)
		{
			echo "麦粒不足，攒点麦粒再来吧^_^";
			die();
		}
		if(sql_query("UPDATE torrents SET pos_state = 'sticky' where id=".$torrentid) or sqlerr(__FILE__, __LINE__))
		{
			sql_query("UPDATE torrents SET Endsticky = '".$deadline."' where id=".$torrentid) or sqlerr(__FILE__, __LINE__);
			echo $info = $CURUSER['username']."花费了".$charge."个麦粒，种子 ".$record['name']." 种子设置二级置顶".$prmtime."小时";
		}
	}
		if($prmtype>='2'&&$prmtype<='6')
	{
		$oritime=$record['endfree'];
		$deadline=date('Y-m-d H:i:s',time()+(86400*$prmtime));

		if($prmtime>=39)
		{
			if ($discount <0.8)
				{sql_query("UPDATE torrents SET endfree = '".date('Y-m-d H:i:s',time()+(86400*7))."'where id=".$torrentid) 		or sqlerr(__FILE__, __LINE__);
				echo ("but活动期间不允许购买永久，自动变更为7天。多的麦粒没收了~");
				}
			else  {
				echo "你购买了永久促销";
				sql_query("UPDATE torrents SET endfree = '0000-00-00 00:00:00'where id=".$torrentid) or sqlerr(__FILE__, __LINE__);
					}
		}
		else{
			sql_query("UPDATE torrents SET endfree = '".$deadline."'where id=".$torrentid) or sqlerr(__FILE__, __LINE__);
		}		
	}
	
	sql_query("UPDATE users SET seedbonus =seedbonus -".$charge." WHERE id = ".$userid) or sqlerr(__FILE__, __LINE__);//扣麦粒
	sendshoutbox("土豪君[@$CURUSER[username]] 花了$charge 个麦粒给种子 [url=details.php?id=$torrentid]$record[name][/url] 购买了促销~来瞅瞅是什么种子吧");
	writeBonusComment($CURUSER[id],"购买促销 ，花费麦粒$charge");
	write_log("自助购买促销：".$info);
	if ($CURUSER['id'] != $record['owner'])
	{
	sendMessage($CURUSER['id'], $record['owner'], "我替你的种子购买了促销", "$CURUSER[username] 花了$charge 个麦粒给种子 [url=details.php?id=$torrentid]$record[name][/url] 购买了促销\n $info \n 此站内信由系统代替用户发送");}

	sql_query("Insert comments(user,torrent,added,text) values(11,$torrentid,now(),".sqlesc("你好我是替系统来留言的[em12]\n".$info).")")or sqlerr(__FILE__, __LINE__);
}

stdfoot();
?>
