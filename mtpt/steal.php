<?php
//author：扬扬。于2013-05-22.php新手练习作
require "include/bittorrent.php";
require "./memcache.php";
dbconn();
loggedinorreturn();
parked();
if ($CURUSER['enabled'] == 'no') stderr("禁止","你的账号被禁止登陆",0);
//每次计算随机数基数cost，每次偷麦粒手续费tax，20几率输掉，randlose，10几率双倍，其他rand。getcaughtrand被抓几率，被抓扣caughtcost，需要needprotect个保镖，每隔limittime秒可以偷一次。
$cost = 500;
$tax = $cost * 0.06;
$rand = mt_rand(90,180)*0.01;
$randlose = mt_rand(50,100)*0.01;
$protectcost = 700;
$getcaughtrand = mt_rand(1,10000);
$caughtcost = 5 * $cost;
$needprotect = 30;
$caught = -($needprotect + 5);
$limittime = 300;	
function changestealstatus($userid,$num,$reset)
{	
	$userid = sqlesc($userid);
	if ($reset == 'no')sql_query("UPDATE users SET stealstatus = stealstatus + $num WHERE id=$userid") or sqlerr(__FILE__,__LINE__);
	else sql_query("UPDATE users SET stealstatus = $reset WHERE id=$userid") or sqlerr(__FILE__,__LINE__);

}



$return = "<br/><a href='steal.php' class='faqlink'>点击返回</a>";


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
if ($_POST['action'] == "steal")
{
//steal prepare
$date = date("H:i:s");
addBonus($CURUSER['id'],-$tax);
$username = sqlesc($_POST['username']);
$opusername = sqlesc($CURUSER['username']);
$res = sql_query("SELECT id,username,seedbonus,stealstatus FROM users WHERE username=$username") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_assoc($res);



/*数据库验证方法，放弃
//$now = time();
//$usertime = strtotime(date("h:m:s",$CURUSER['stealtime']));
//echo $now."---now,time,<br/>".$usertime;
//if ($now - $usertime < $limittime) stderr("休息一会吧","你在$limittime 秒里面偷过别人了，休息一会吧",0);
$writetime = sqlesc(date("h:m:s",time()));
sql_query("UPDATE users SET stealtime = $writetime WHERE id={$CURUSER['id']}") or sqlerr(__FILE__,__LINE__);
*/
//error message
if ($CURUSER['seedbonus'] <= $cost)  {
stderr("出错了","你的麦粒不足$cost 了，偷窃经费不够(-$tax)".$return,0);
die;
}
if ($CURUSER['stealstatus'] == -5)  {
addBonus($CURUSER['id'],-$cost);
stderr("出错了","你退出了这个游戏，不能偷别人，但是麦粒一样要扣(-$cost)，不做记录".$return,0);
die;
}
if ($CURUSER['stealstatus'] < -5)  {
addBonus($CURUSER['id'],-$cost);
stderr("出错了","你正在蹲监狱，不能偷别人，但是麦粒一样要扣(-$cost)，不做记录".$return,0);
die;
}
if ($username == $opusername)  {
stderr("出错了","花偷窃经费偷自己，偷窃经费倒是没丢，系统拿走部分浪费了(-$tax)。将不记录到麦粒记录，免得丢人~".$return,0);
die;
}
if (!$arr['id']) {
stderr("出错了","没有这个用户，花了偷窃经费，偷了个空(-$tax)。将不记录到麦粒记录，免得丢人~".$return,0);
die;
}

if ($arr['stealstatus'] == -5) {
stderr("出错了","他不玩这个游戏。偷窃经费白花了，(-$tax)将不记录到麦粒记录。".$return,0);
die;
} 
if ($arr['stealstatus'] < -5) {
addBonus($CURUSER['id'],-$cost);
stderr("出错了","他正在坐牢呢，你也要坐牢吗~？(-$cost)将不记录到麦粒记录。".$return,0);
die;
}

if ($arr['seedbonus'] <= 5*$cost) {
stderr("出错了","他的麦粒不足5*$cost 了，换个有钱的偷吧，偷窃经费还给你(-$tax)".$return,0);
die;
} 


//steal start
//change &&check stealtime memcache方法
if($memcache->get('app_steal_'.$CURUSER['id'])!='') {
$lasttime = $memcache->get('app_steal_'.$CURUSER['id']);
stderr("错误","你在$limittime 秒内($lasttime)偷过麦粒，休息一会吧".$return,0);}
$memcache->set('app_steal_'.$CURUSER['id'],$date,false,$limittime) or die ("请向管理员报告此错误");

//被抓
if ($getcaughtrand == 1)
{
	addBonus($CURUSER['id'],-$caughtcost);
	writeBonusComment($CURUSER['id'],"偷麦粒被抓了，麦粒被扣了$caughtcost ！");
	changestealstatus($CURUSER['id'],0,$caught);
	stderr("很不幸","你偷麦粒被抓了。。你的麦粒被扣除了$caughtcost 。你现在被抓进了监狱，不能偷别人的麦粒，也不会被偷。你需要找$needprotect 个保镖把你救出来才能继续游戏。出狱之后记得重新加入游戏".$return,0);
	die;
}

//偷到保镖
if ($arr['stealstatus'] > 0){
$becaught = mt_rand(1,100);
if($becaught > 30){
addBonus($CURUSER['id'],-$cost);
writeBonusComment($CURUSER['id'],"偷{$arr['username']}的麦粒被保镖抓了，损失$cost 个麦粒干掉他一个保镖");
stderr("出错了","他花钱雇保镖，被暂时保护了。偷窃经费花掉了，但是你也干掉了他一个保镖~（-$cost ）".$return,0,1,1,0);
changestealstatus($arr['id'],-1,'no');
addBonus($arr['id'],$cost/2);
writeBonusComment($arr['id'],"{$CURUSER['username']}来偷你的麦粒！但是因为你有保镖，他失败了。你获得了偷窃者的$cost /2个麦粒，另外一半被保镖带走了");
if ($arr['stealstatus' != -1])sendMessage(0, $arr['id'], "你的麦粒被偷了！", "[b]{$CURUSER['username']}[/b]来偷你的麦粒！但是因为你有保镖，他失败了。你获得了偷窃者的$cost /2个麦粒，另外一半被保镖带走了。\n如果你想偷回来，或者申请保护，或者不想参与这个游戏，[url=steal.php]都可以在这里完成[/url]\n[url=sendmessage.php?receiver={$CURUSER['id']}]给他发站内信[/url]");
die;}
else $return .= "（你的保镖睡着了）";
}  


//get random 
$random = mt_rand(1,100);
if ($random <= 20)
	{$opget = $randlose * $cost;
	//$userget = $cost;
	}
elseif ($random >= 90)
	{$opget = 2*$cost;
	//$userget = 0;
	}
else{
	$opget = $rand*$cost;
	//$userget = $cost - $opget;
}

//do abs
$abs = abs($cost-$opget);

if ($cost == $opget ){
$message = "但是，他没有从你这里捞到任何好处~";
}
elseif ($cost < $opget)
{
$message = "你被他偷走了$abs 个麦粒。";
addBonus($arr['id'],-$abs);
writeBonusComment($arr['id'],"被{$CURUSER['username']}偷走 $abs 个麦粒");
addBonus($CURUSER['id'],$abs);
writeBonusComment($CURUSER['id'],"从{$arr['username']}偷到 $abs 个麦粒");
stderr("结果" ,"从{$arr['username']}偷到 $abs 个麦粒".$return,0,1,1,0);
}
else 
{
$message = "但是，他不仅没有成功，反而给你留下了$abs 个麦粒~";
addBonus($arr['id'],$abs);
writeBonusComment($arr['id'],"{$CURUSER['username']}来偷麦粒，反而留下 $abs 个");
addBonus($CURUSER['id'],-$abs);
writeBonusComment($CURUSER['id'],"偷{$arr['username']}的麦粒反而搭进去 $abs 个");
stderr("结果", "偷{$arr['username']}的麦粒反而搭进去 $abs 个".$return,0,1,1,0);
}
//send message to user

if ($arr['stealstatus'] != -1)sendMessage(0, $arr['id'], "你的麦粒被偷了！", "你的麦粒被[b]{$CURUSER['username']}[/b]偷了!\n".$message."\n如果你想偷回来，或者申请保护，或者不想参与这个游戏，[url=steal.php]都可以在这里完成[/url]\n[url=sendmessage.php?receiver={$CURUSER['id']}]给他发站内信[/url]");
die;
//end steal
}
//改变steal值到-5或0以退出或加入游戏以及屏蔽站内信
if ($_POST['action'] == 'option')
{
	if ($CURUSER['stealstatus'] < -5){
	$abssteal = abs($CURUSER['stealstatus']) - 5;
	stderr("你被抓了","你正在蹲监狱，已经退出了游戏。你还需要购买$abssteal 个保镖把你从监狱救出来才能继续游戏。出狱之后记得重新加入游戏".$return,0);die;	
	}
	if ($_POST['quite'] == 'on' && $CURUSER['stealstatus'] < -1)  {
	changestealstatus($CURUSER['id'],0,'0');
	addBonus($CURUSER['id'],-2000);
	writeBonusComment($CURUSER['id'],"-2000；退出游戏之后重新加入，花费了2000个麦粒");
	stderr("操作成功","你成功重新加入了游戏".$return,0);die;}
	if ($_POST['quite'] == 'off' && $CURUSER['stealstatus'] > 0)
	{stderr("提示信息","你现在还有保镖，不要抛弃他们啊。".$return,0);die;}
	if ($_POST['quite'] == 'off')  {
	changestealstatus($CURUSER['id'],0,-5);
	stderr("操作成功","你成功退出了这个游戏，你将不能偷别人，别人也不再能偷走你的麦粒。".$return,0);die;}
	if ($_POST['message'] == false && $CURUSER['stealstatus'] == 0)  {
	changestealstatus($CURUSER['id'],0,-1);
	stderr("操作成功","你成功拦截了偷麦粒应用的站内信，你家被偷光都不会告诉你了~".$return,0);die;}
	if ($_POST['message'] == true && $CURUSER['stealstatus'] == -1)  {
	changestealstatus($CURUSER['id'],0,'0');
	stderr("操作成功","你将继续接收站内信提示".$return,0);die;}
	else stderr("操作失败","估计是你还有保镖的缘故，你的操作失败了。".$return,0);

}

//购买保镖
if ($_POST['action'] == 'changesteal')
{
	
	$changenum = (int)$_POST['stealnum'];
	$costforchange = $changenum * $protectcost;
	if ($changenum <= 0) stderr("错误","你输入的数字不是正数".$return,0);
	if ($CURUSER['seedbonus'] < $costforchange)
	{
		stderr("麦粒不够","你的麦粒不够买这么多的保镖".$return,0);die;}
	$abssteal = abs($CURUSER['stealstatus']) - 5 -$changenum ;
	$pronow = $CURUSER['stealstatus']>0?"你现在有{$CURUSER['stealstatus']}个保镖":"你还需要$abssteal 个保镖才能把你救出来";
	addBonus($CURUSER['id'],-$costforchange);
	writeBonusComment($CURUSER['id'],"花费 $costforchange 个麦粒购买了 $changenum 个保镖");
	changestealstatus($CURUSER['id'],$changenum,'no');
	stderr("操作成功","你花费 $costforchange 个麦粒购买了 $changenum 个保镖.".$pronow.$return,0);die;
	
}

//show 
if ($_POST['action'] == 'show')
{
	if ($CURUSER['seedbonus'] <= $cost)  {
	stderr("出错了","你的麦粒不足$cost 了，贿赂经费不够".$return,0);
	die;
	}
	addBonus($CURUSER['id'],-($cost*2));
	writeBonusComment($CURUSER['id'],"在偷麦粒应用中花费 $cost *2 个麦粒购贿赂系统");
	$show = "每次花费$cost 个麦粒去别人家，系统额外收取$tax 。每次查看参数花费2*$cost <br/><b>你有10%几率输掉（获得0.50~1.00倍花费的麦粒），有10%几率偷双倍。其他情况下，随机获得0.90~1.80倍花费的麦粒。有万分之一的几率被抓。</b><br/>被抓之后需要购买$needprotect 个保镖把你救出来。每个保镖需要$protectcost 个麦粒。出狱之后需要重新加入游戏。<br/>如果对游戏规则有修改建议，请在论坛灌水区发帖讨论，不要透露太多信息哦<br/>";
	stderr("不要告诉别人哦","$show ",0);
}
//挑衅
if ($_POST['writename'] == 'yes')
{
	if($CURUSER['stealstatus'] <= -5)stderr("出错","你被抓或者退出了游戏，挑衅谁呀".$return,0);
	if ($CURUSER['stealstatus'] > 0) {
	changestealstatus($CURUSER['id'],0,'0');
	echo "<script type=\"text/javascript\">alert('你家保镖听说你不要他们了，就全都跑掉了');window.location.href ='steal.php'</script>";
	
	}
	if ($memcache->get('app_steal_userlist_'.$CURUSER['id']) == ""){
	$memcache->set('app_steal_userlist_'.$CURUSER['id'],$CURUSER['username'],false,10);
	$newlist = $memcache->get('app_steal_userlist')."  ,  ". $memcache->get('app_steal_userlist_'.$CURUSER['id']);
	$memcache->set('app_steal_userlist',$newlist,false,350000);}
	else $memcache->set('app_steal_userlist_'.$CURUSER['id'],$CURUSER['username'],false,10);
}

}
stdhead("偷麦粒应用");
?>

<b style="color:blue;font-size:54px;">嘿，小偷儿</b>
<form action="" method="post" >
<input type="hidden" name="action" value="steal"/>
请输入对方的用户名<input type="text"   name="username" />
<input type="submit" name="submit" value="动手！"/>
</form>
<a href="topten.php?type=6&lim=100&subtype=bo" class="faqlink" target="_blank">查看麦粒排行榜的土豪</a><br/>土豪家里可能有保镖，偷麦粒需谨慎
<br/>
<form action="" method="post" >
<input type="hidden" name="action" value="show"/>
贿赂系统了解计算规则(每次查看规则将花费2*<?php echo $cost;?>个麦粒)
<input type="submit" name="submit" value="知己知彼"/>
</form>
<br/><br/><br/>
<div style="background-color:#000000;width:600px">
	<p style="color:red;FONT-size:24; background-color:#454545"><b> 小偷儿公告板</b></p>调整保镖状态。拥有保镖被偷时，有30%几率抓不到小偷，此时不消耗保镖数量，保镖睡着，正常被偷。<br/>新增拦截站内信功能。必须保镖数目为零时才能开启，购买保镖将使此功能无效。<br/>由于挑衅功能问题很多，暂时不上线
<!--
	<p style="color:#FFFFFF"><b style="FONT-size:24">以下用户公然挑衅小偷界，偷光他们！</b></p>
	<p style="color:#FFFFFF"><?php echo $memcache->get('app_steal_userlist');?></p>挑衅小偷会使家里保镖全都跑掉，挑衅名单将挂在黑板上150000秒即大概两天，之后自动消失。重复挑衅会刷新时间。
	<form action="" method="post"><input type="hidden" name="writename" value="yes"/><input type="submit" value="小偷们来偷我吧，我家保镖都撵走了~"/></form>
-->
	</div>
<b style="color:red;font-size:33px;">游戏规则</b><br/>
<span class="rules">
<?php
echo "
首先<b>每次</b>偷窃行为会被系统收$tax 个麦粒。每$limittime 秒能偷一次<br/>
然后你可以去别人家里偷麦粒了~不过会有几率赔哦。（跟碰运气一样，但是赢的概率更大）<br/>
<br/>
有极小的几率被警察抓到，抓到的话将进监狱，损失$caughtcost 麦粒 ，不能继续偷别人的麦粒（退出游戏）。<br/>

<br/>
你可以购买保镖保护自己的麦粒，拥有保镖的时候别人不能偷你，但是你可以偷别人。<br/>
偷到被保镖保护的用户，将损失$cost 个麦粒（其中的一半给被偷的用户），另外一半保镖会拿着跑掉。<br/>
<br/>
如果你不想玩这个游戏，不希望被偷麦粒的站内信困扰，你可以在下面的设置中退出这个游戏，其他人将不能偷你<br/>

每个保镖需要花费$protectcost 个麦粒<br/>"
?>
</span>
<b style="color:red;font-size:33px;">游戏设置</b><br/>

<form action="" method="post" ><b style="color:blue;font-size:22px;">买保镖</b><br/><?php
 $needpro = abs($CURUSER['stealstatus']) - 5;
if ($CURUSER['stealstatus'] != -5)
	echo ($CURUSER['stealstatus']<-5?"你现在还需要购买". $needpro."个保镖才能把你从监狱救出来":"你现在有".$CURUSER['stealstatus']."个保镖");
else	
	echo "你退出了游戏，购买保镖将使你损失麦粒并且有可能重新加入游戏";
	?><br/>
请输入你要购买的保镖数量
<input type="hidden" name="action" value="changesteal"/>
<input type="text"   name="stealnum"  />
<input type="submit" value="购买保镖"/>
</form>
<form action="" method="post">
<b style="color:blue;font-size:22px;">是否接收站内信？</b><br/>选择不接收站内信将不退出游戏，别人可以来偷。你在有保镖的状态下不能停止接收站内信。被偷的状况请到<a href='myhistory.php?type=bonus'class='faqlink'>麦粒记录</a>查看。<br/>
<input type="hidden" name="action" value="option"/>
<?php
echo "接收<input type=\"checkbox\" name=\"message\" ".($CURUSER['stealstatus']!=-1? "  checked=\"checked\" ":"")." /><br/>";
?>
<input type="submit" name="submit" value="执行"/>
</form>
<form action="" method="post">
<b style="color:blue;font-size:22px;">是否继续参与游戏</b><br/>为了防止用户频繁退出加入游戏，每次退出之后<b style="color:red">重新加入</b>游戏将收取2000个麦粒。<br/>
<input type="hidden" name="action" value="option"/>
<?php
echo "参与游戏<input type=\"radio\" name=\"quite\" value=\"on\" ".($CURUSER['stealstatus']>=-1? "  checked=\"checked\" ":"")." /><br/>";
echo "退出游戏<input type=\"radio\" name=\"quite\" value=\"off\" ".($CURUSER['stealstatus']<=-5? "  checked=\"checked\" ":"")." /><br/>";
?>
<input type="submit" name="submit" value="执行"/>
</form>





<?php
stdfoot();
?>