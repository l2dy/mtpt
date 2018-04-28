<?php
require "include/bittorrent.php";
require "./memcache.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path());
parked();
$maxsalarynum = $CURUSER['maxsalarynum'];
function bonusarray($option){
	global $onegbdownload_bonus,$fivegbdownload_bonus,$tengbdownload_bonus,$onegbupload_bonus,$fivegbupload_bonus,$tengbupload_bonus,$oneinvite_bonus,$customtitle_bonus,$vipstatus_bonus, $basictax_bonus, $taxpercentage_bonus, $bonusnoadpoint_advertisement, $bonusnoadtime_advertisement;
	global $lang_mybonus;
	$bonus = array();
	switch ($option)
	{
		case 1: {//碰运气
			$bonus['points'] = 25;
			$bonus['art'] = 'luck';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_luck'];
			$bonus['description'] = $lang_mybonus['text_luck_note'];
			break;
			}
		default: break;
	}
	return $bonus;
}

$action = htmlspecialchars($_GET['action']);

stdhead($CURUSER['username'] . $lang_mybonus['head_karma_page']);

	$bonus = number_format((int)$CURUSER['seedbonus'], 0);
if (!$_POST['action']) {
	print("<table align=\"center\" width=\"940\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n");
	print("<tr><td class=\"colhead\" colspan=\"4\" align=\"center\"><font class=\"big\">".$SITENAME.$lang_mybonus['text_karma_system']."</font></td></tr>\n");
?>
<tr><td class="text" align="center" colspan="4"><?php echo $lang_mybonus['text_exchange_your_karma']?><?php echo $bonus?><?php echo $lang_mybonus['text_for_goodies'] ?>
<br /><b><?php echo $lang_mybonus['text_no_buttons_note'] ?></b></td></tr>
<script type="text/javascript" >
//js语句
function prmOnClicked()
{
	var torrentid=document.getElementById("torrentid").value;
	var type=document.getElementById("prmtype").value;
	var time=document.getElementById("prmtime").value;
	if(type=='7')
	{
		var obj=document.getElementById("timetype");
		obj.value="小时";
	}
	if(torrentid==""||type==""||time=="")
	{
		alert("输入错误");
		return false;
	}
	if(confirm("你确定要为ID为 "+torrentid+" 的种子购买"+time+"天特权么？"))
	 {
		var postForm = document.createElement("form");//表单对象   
		postForm.method="post" ; 
		postForm.action = 'bonusapp.php' ;    
        var actioninput=document.createElement("input") ;
	    actioninput.setAttribute("name", "action") ;   
		actioninput.setAttribute("value", "prm"); 
		postForm.appendChild(actioninput) ; 
		var idinput = document.createElement("input") ;
		idinput.setAttribute("name", "torrentid") ;   
		idinput.setAttribute("value", torrentid);   
		postForm.appendChild(idinput) ; 
		var typeinput = document.createElement("input") ;
		typeinput.setAttribute("name", "type") ;   
		typeinput.setAttribute("value", type);   
		postForm.appendChild(typeinput) ; 
		var timeinput = document.createElement("input") ;
		timeinput.setAttribute("name", "time") ;   
		timeinput.setAttribute("value", time);   
		postForm.appendChild(timeinput) ; 
       
		document.body.appendChild(postForm) ;   
		postForm.submit() ;   
		document.body.removeChild(postForm) 
	 }
	 return false;
}
function selectchanged()
{
var type=document.getElementById("prmtype").value;
obj=document.getElementById("timetype");
if(type=='7')
	{
		obj.value="小时";
	}
	else
	{
		obj.value="天";
	}
}
function onekeytaken()//一键购买
{
	//var num1=document.getElementById("num1").value;
	//var num2=document.getElementById("num2").value;
	//var num3=document.getElementById("num3").value;
	//var num4=document.getElementById("num4").value;
	//var num5=document.getElementById("num5").value;
	//var multiple=document.getElementById("multiple").value;
	//var selfkey=document.getElementById("selfkey").value;
	//if(num1==""||num2==""||num3==""||num4==""||num5==""||multiple=="")
	//{
	//	alert("输入不完整");
	//	return false;
	//}
	//alert("dsafd");
	var num1=Math.floor(Math.random()*12+1);
	var num2=Math.floor(Math.random()*12+1);
	var num3=Math.floor(Math.random()*12+1);
	var num4=Math.floor(Math.random()*12+1);
	var num5=Math.floor(Math.random()*12+1);
	var multiple=1;
	
	if(confirm("以下数字为系统随机为你生成的数字，确认购买么？"+num1+","+num2+","+num3+","+num4+","+num5+"，倍注"+multiple+"倍"))
	 {
		var postForm = document.createElement("form");//表单对象   
		postForm.method="post" ; 
		postForm.action = "./lottery.php?action=takelattery" ;
        //var actioninput=document.createElement("input") ;
	    //actioninput.setAttribute("name", "action") ;   
		//actioninput.setAttribute("value", "lattery"); 
		//postForm.appendChild(actioninput) ; 
		
		var inum1 = document.createElement("input") ;
		inum1.setAttribute("name", "num1") ;   
		inum1.setAttribute("value", num1);   
		postForm.appendChild(inum1) ; 
		
		var inum2 = document.createElement("input") ;
		inum2.setAttribute("name", "num2") ;   
		inum2.setAttribute("value", num2);   
		postForm.appendChild(inum2) ; 
		
		var inum3 = document.createElement("input") ;
		inum3.setAttribute("name", "num3") ;   
		inum3.setAttribute("value", num3);   
		postForm.appendChild(inum3) ; 
		
		var inum4 = document.createElement("input") ;
		inum4.setAttribute("name", "num4") ;   
		inum4.setAttribute("value", num4);   
		postForm.appendChild(inum4) ; 
		
		var inum5 = document.createElement("input") ;
		inum5.setAttribute("name", "num5") ;   
		inum5.setAttribute("value", num5);   
		postForm.appendChild(inum5) ; 
		
		var imultiple = document.createElement("input") ;
		imultiple.setAttribute("name", "multiple") ;   
		imultiple.setAttribute("value", multiple);   
		postForm.appendChild(imultiple) ; 
		
		//var iselfkey = document.createElement("input") ;
		//num1.setAttribute("name", "selfkey") ;   
		//num1.setAttribute("value", selfkey);   
		//postForm.appendChild(iselfkey) ; 
       
		document.body.appendChild(postForm) ;   
		postForm.submit() ;   
		document.body.removeChild(postForm) ; 	
	}
}
</script>
<?php

print("<tr><td class=\"colhead\" align=\"center\">".$lang_mybonus['col_option']."</td>".
"<td class=\"colhead\" align=\"left\">".$lang_mybonus['col_description']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_points']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_trade']."</td>".
"</tr>");
for ($i=1; $i <=6; $i++)
{
	$bonusarray = bonusarray($i);
	print("<tr>");
	print("<form action=\"\" method=\"post\">");
	print("<td class=\"rowhead_center\"><input type=\"hidden\" name=\"option\" value=\"".$i."\" /><b>".$i."</b></td>");
	if ($i==1){  //碰运气
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b>".$lang_mybonus['text_to_be_play']."</b><input type=\"text\" name=\"luckbonus\" id=\"luckbonus\" style='width: 80px' />".$lang_mybonus['text_karma_points']."</td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."$otheroption</td><input type=\"hidden\" name=\"action\" value=\"exchange\" /><td class=\"rowfollow nowrap\" align='center'>".$lang_mybonus['text_min']."25<br />".$lang_mybonus['text_max']."1,000</td>");
	}
	if ($i==2){  //21点
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b><a href=play21.php class='faqlink'>".$lang_mybonus['text_play21']."</a></b></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$otheroption."</h1>（敢不敢再测人品！)</td><td>点左边标题啦</td>");
	}
	
	if ($i==3){  //偷麦粒
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b><a  class='faqlink' href=steal.php>嘿，小偷儿</a></b>~<br/></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$otheroption."</h1>类似碰运气，不过是从别人手里偷过来哦(测试中)</td><td>点左边标题啦</td>");
	}
	if ($i==4){  //prm促销
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b>我顶</b></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$otheroption."</h1>麦粒多的没地方用么？点这儿给你的种子买个特权吧<br>提示：输入种子id， 如果你不知道你的种子的id，你可以点开你的种子页面，看见地址栏后面有个id=XXXX了么？，等号后面的几个数字就是了（其余请忽略）<br/>提示：天数输入大于39可以购买永久，价格为原价的39倍（你是在抢劫么？你咋知道的 ^_^）。另外置顶不允许购买永久，最好等当前促销已结束再购买新的促销，否则新购买的促销将替换旧的<br>每天每位用户只能使用一次此功能<br>ID为<input type=\"text\" value=\"".$_GET['id']."\" name=\"torrentid\" id=\"torrentid\" style='width: 80px' />的种子<select name=\"prmtype\" id=\"prmtype\" onchange=\"selectchanged()\" style='width: 130px' > <option value=\"2\">免费</option> <option value=\"3\">2X上传</option><option value=\"4\">2X上传&免费</option><option value=\"5\">50%下载</option><option value=\"6\">50%下载&2X上传</option><option value=\"7\">二级置顶</option>
</select><input type=\"text\" name=\"prmtime\" id=\"prmtime\" style='width: 40px' /><input type=\"text\"  id=\"timetype\" size=\"2\" style=\"background:transparent;border:0;color:red\" value=\"天\" disabled =\"false\"/></td><td>免费2000/天<br/>2X上传2000/天<br/>2X上传&免费5000/天<br/>50%下载1200/天<br/>50%下载&2X上传3500/天<br/>置顶4000/小时<br/></td>");
	}
	if ($i==5){  //c彩票
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b><a href=lottery.php class='faqlink'>彩票5/12（12选5）</a></b><br/></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$otheroption."</h1>点这里才能进入彩票站！才能看到开奖记录，才能一次买好多注，才能送彩票给别人，才能知道每隔三天22:00开奖，还能手动兑奖！这些都不是重点，重点是进去才知道一等奖500w麦粒！！二等奖50w麦粒！！而每一注才100麦粒~没有闲钱买彩票的话用麦粒试试运气吧</td><td>点<a href=lottery.php class='faqlink'>左边</a>进入彩票站，点右边一键随机购买一注彩票</td>");
	}
	if ($i==6){	//buqianka
	
		$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b>补签卡</b><br/></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$otheroption."</h1>超过三天未登录将清零连续登陆天数。你现在的连续登陆天数是<b> ".$CURUSER['salarynum']." </b>。购买补签卡可以将连续登陆天数更改为断签前最高连续登陆天数<b > ".$maxsalarynum." </b> 天。价格为(天数*1024)个麦粒</td><td class=\"rowfollow\" align='left'><input type=\"hidden\" name=\"action\" value=\"buqianka\"/>".$maxsalarynum." x 1024 = ". 1024*$maxsalarynum ."</td>");
	}

	if($CURUSER['seedbonus'] >= $bonusarray['points'])
	{
		if ($i==1){
			if($memcache->get('app_luck_'.$CURUSER['id'])!='')
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" disabled=\"true\" value=\"".$lang_mybonus['submit_karma_luck']."\" /><br />上次时间：<br />".$memcache->get('app_luck_'.$CURUSER['id'])."</td>");
			else
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_karma_luck']."\" /></td>");
		}
		elseif ($i==2){
			print("<td class=\"rowfollow\" align=\"center\"><a href='play21.php' class='faqlink'>我不</a></td>");
		}
		elseif ($i == 3)
		{
			print("<td class=\"rowfollow\" align=\"center\"><a href='steal.php' class='faqlink'>就不</a></td>");
		}
		elseif ($i==4){
			print("<td class=\"rowfollow\" align=\"center\"><input type=button onclick=\"return prmOnClicked()\" value='购买'/></td>");
		}
		elseif ($i==5){
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" value='一键购买' onclick=\" return onekeytaken() \" /></td>");
		}
		elseif ($i==6){
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"购买\" /></td>");
		}
		
	}
	else
	{
		print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['text_more_points_needed']."\" disabled=\"disabled\" /></td>");
	}
	print("</form>");
	print("</tr>");
	
}
print("</table><br />");
}

if($action == "viewluck"){
	$lucklog = array();
	$lucklog = json_decode($memcache->get('app_luck_log'));
	$lucklog = array_reverse($lucklog);
		print("<table width=100%>");
		print("<tr><td height=30>");
		print("显示近1000条玩家游戏记录");
		print("</tr></td>");
	foreach ($lucklog as $log){
		print("<tr><td height=30>");
		print($log."<br />");
		print("</tr></td>");
	}
		print("</table>");
}

// Bonus exchange
if ($_POST['action'] == "buqianka") {
if($CURUSER['seedbonus'] > $maxsalarynum *1024){
addBonus($CURUSER['id'],-$maxsalarynum *1024);
writeBonusComment($CURUSER['id'],"用".$maxsalarynum ."*1024个麦粒购买了补签卡");
sql_query("UPDATE users SET salarynum = maxsalarynum where id = ".$CURUSER['id']);
	stdmsg($lang_mybonus['text_success'], '成功购买了补签卡，你现在的连续登陆天数是'.$CURUSER['salarynum']);
	}
else
{
	stdmsg($lang_mybonus['text_success'], '你的麦粒不够');
	}
	stdfoot();
	die();
}
if ($_POST['action'] == "exchange") {
	if ($_POST["userid"] || $_POST["points"] || $_POST["bonus"] || $_POST["art"]){
		write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is trying to cheat at bonus system",'mod');
		die($lang_mybonus['text_cheat_alert']);
	}
	$option = (int)$_POST["option"];
	$bonusarray = bonusarray($option);

	$points = $bonusarray['points'];
	$userid = $CURUSER['id'];
	$art = $bonusarray['art'];

	$bonuscomment = $CURUSER['bonuscomment'];
	$seedbonus=$CURUSER['seedbonus']-$points;

	if($CURUSER['seedbonus'] >= $points) {
		//=== trade for upload
		if($art == "luck") {
		if($memcache->get('app_luck_'.$CURUSER['id'])!=''){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['text_cheat_alert']);
				stdfoot();
				die();
		}
			$luckbonus=0+$_POST['luckbonus'];
			if ($luckbonus < 25 || $luckbonus > 1000) {
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_amount_not_allowed']);
				die();
			}
			else if($CURUSER['seedbonus'] < $luckbonus){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['text_not_enough_karma']);
				die();
			}
			$retluckbonus = mt_rand(1,$luckbonus*2);
			$sqlluckbonus = $retluckbonus - $luckbonus;
			sql_query("UPDATE users SET seedbonus = seedbonus + $sqlluckbonus WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
		if($sqlluckbonus > 0)
			$retinfo = "恭喜".$CURUSER['username']."<b><font color=red>获得了".$sqlluckbonus."个</font></b>麦粒";
		elseif($sqlluckbonus == 0)
			$retinfo = $CURUSER['username']."即没有得到也没有失去麦粒";
		else
			$retinfo = "很遗憾，".$CURUSER['username']."<b><font color=green>失去了".abs($sqlluckbonus)."个</font></b>麦粒";
			$message = $CURUSER['username']."使用了：".$luckbonus."个麦粒，获得了：".$retluckbonus.$lang_mybonus['text_point']."，".$retinfo;
			stdmsg($lang_mybonus['text_success'], $message);
			$date = date("H:i:s");
			$memcache->set('app_luck_'.$CURUSER['id'],$date,false,600) or die ("");
			//碰运气记录开始
				$lucklog = json_decode($memcache->get('app_luck_log'));
				$lucklog[] = $date." ".$message;
if(count($lucklog))
	$lucklog = array_slice($lucklog,-1000);
				$memcache->set('app_luck_log',json_encode($lucklog),false,3600);
			//碰运气记录结束
			stdfoot();
			die();
		}
	}
}
stdfoot();
?>
