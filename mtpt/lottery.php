<?php
require_once "include/bittorrent.php";
require_once "./memcache.php";
dbconn();
loggedinorreturn();
parked();
stdhead($CURUSER['username'] . $lang_mybonus['head_karma_page']);
?>
<script type="text/javascript" >
function latteryOnclicked()
{
	var num1=document.getElementById("num1").value;
	var num2=document.getElementById("num2").value;
	var num3=document.getElementById("num3").value;
	var num4=document.getElementById("num4").value;
	var num5=document.getElementById("num5").value;
	var multiple=document.getElementById("multiple").value;
	var selfkey=document.getElementById("selfkey").value;

	if(num1==""||num2==""||num3==""||num4==""||num5==""||multiple=="")
	{
		alert("输入不完整");
		return false;
	}
	if(num1<1||num1>15||num2<1||num2>15||num3<1||num3>15||num4<1||num4>15||num5<1||num5>15||multiple<=0)
	{
		alert("数字输入似乎有错误");
		return false;
	}
	if(confirm("你请确定你要购买的数字"+num1+","+num2+","+num3+","+num4+","+num5+"，倍注为"+multiple+"倍"))
	 {
			return true;
	 }
	 return false;
}
function givedchanged()
{
	var slt=document.getElementById("gived");	
	var ackey=document.getElementById("ackey");
	if(slt.value=='1')
	{
		ackey.value="在此输入好友用户名";
		ackey.disabled=false;
	}
	else if(slt.value=='2')
	{
		ackey.value="在此输入兑奖验证码";
		ackey.disabled=false;
	}
	else
	{
		ackey.value="此项无需填写";
		ackey.disable=true;
	}
}

	function randnum()//随机
{
	
	var num1=Math.floor(Math.random()*15+1);
	var num2=Math.floor(Math.random()*15+1);
	var num3=Math.floor(Math.random()*15+1);
	var num4=Math.floor(Math.random()*15+1);
	var num5=Math.floor(Math.random()*15+1);
	var slt1=document.getElementById("num1");
	var slt2=document.getElementById("num2");
	var slt3=document.getElementById("num3");
	var slt4=document.getElementById("num4");
	var slt5=document.getElementById("num5");
	slt1.value=num1;
	slt2.value=num2;
	slt3.value=num3;
	slt4.value=num4;
	slt5.value=num5;
	
}


</script>
<?php
$hapcharge=100;//预设单注价格,cleanup里面需要同时更改
	$cash=array();
	$cash[1]=5000000;
	$cash[2]=100000;
	$cash[3]=10000;
	$cash[4]=1000;
	$cash[5]=0;
	$cash[6]=0;
$action = htmlspecialchars($_GET['action']);
if(!$action)
{
$sql=("SELECT * FROM drawlottery order by id desc limit 1");
$res=sql_query($sql);
$row = mysql_fetch_array( $res );

print("<h2>彩票5/15（15选5）</h2><br/>上期开奖：".$row['num1']."，".$row['num2']." ，".$row['num3']." ，".$row['num4']." ，".$row['num5'] ."<h1><a href=\"lottery.php?action=drawlog\" target=\"_blank\" >往期开奖记录</a></h1>
<table>
<form action=\"?action=takelattery\" method=\"post\">
<tr>
	<td width=\"70%\">彩票玩过没？咱没有RMB，但咱有麦粒哈，用你的麦粒来买彩票吧。当然了，中奖了也只能奖励麦粒的（囧，没有RMB）。<br/><br/><strong>玩法简介</strong>:在15个数字(1-15)中选择5个数字,若数字无错误即可购买成功，无任何限制购买（每期可购买多次），价格$hapcharge 每次/每注，也可倍注，倍注价格为原价x倍注。<br/><br/><strong>如何中奖</strong>：系统<b style=\"color:red\">每周二周五的21:00左右</b>会随机生成5个数字，若你购买时输入的数字跟系统随机产生的数字相同（位置和数字都相同），即认为彩中1个，两个位置的数字相同，即认为彩中2个，依此类推，最多彩中5个</td><td width=\"30%\"><strong>奖项设置</strong>：<br/>");
	for ($i=5;$i>=2;$i--)
	echo ((6-$i)."等奖(彩中$i 个)：奖励".$cash[6-$i]."麦粒 x 倍注<br/>");
	echo ("</td></tr>
<tr>
	<td width=\"80%\">依次输入5个数字
	");
	for ($j=1;$j<=5;$j++)
	{
	echo ("<select name=\"num$j\" id=\"num$j\" >");
		for ($i=1;$i<=15;$i++)
		{echo "<option value=\"$i\">";
			if ($i<10) echo "0".$i;
			else echo $i;
		echo "</option>";}
	echo "</select>";
}

echo ("<input type=\"button\" value='随机选择' onclick=\" return randnum() \" />&nbsp;倍注<input type=\"text\" name=\"multiple\" value=1 id=\"multiple\" style='width: 20px' />倍</td><td width=\"200px\">$hapcharge  麦粒/倍注</td></tr>
<tr>
	<td width=\"80%\">彩票兑奖人<select name=\"gived\" id=\"gived\" onchange=\"givedchanged()\"><option value=\"0\">自己</option><option value=\"1\">好友</option><<option value=\"2\">匿名</option></select><input type=\"text\" name=\"ackey\" id=\"ackey\" value=\"此项无需填写\" disabled=\"true\"  style='width: 180px' /><br/>提示：匿名彩票不记名，不挂失，可以随意转让。兑奖时输入彩票id和验证码即可领奖。如果购买时验证码留空则默认是购买者的注册邮箱地址。非匿名彩票开奖后自动发奖，匿名彩票需要手动兑奖。（匿名彩票有什么用？可以随便送，可以证明程序员脑子比较水灵）</td><td width=200px\"><input type=\"submit\"  value='购买' onclick=\"return latteryOnclicked();\"/></td></tr>
</form>
</table>
<br/><br/><hr/>");
print("<h2>我的中奖纪录</h2>这里只显示拥有的非匿名并且中奖的彩票。如果还没有的话赶紧加油吧~<br/><table>
	<tr>
<td class='colhead'>期数</td><td class='colhead'>彩票ID</td><td class='colhead'>NUM1</td><td class='colhead'>NUM2</td><td class='colhead'>NUM3</td><td class='colhead'>NUM4</td><td class='colhead'>NUM5</td><td class='colhead'>倍数</td><td class='colhead'>中奖等级</td><td class='colhead'>中奖奖励</td>
</tr>");
	
	
	$sql="select * from lottery where ownerid=".$CURUSER['id']." and isencase!= 0";
	$res=sql_query($sql);
	while($row = mysql_fetch_array( $res ))
	{
		print("<tr><td>".$row['drawid']."</td><td>".$row['id']."</td><td>".$row['num1']."</td><td>".$row['num2']."</td><td>".$row['num3']."</td><td>".$row['num4']."</td><td>".$row['num5']."</td><td>".$row['multiple']."</td><td>".$row['isencase']."</td><td>".($cash[$row['isencase']]*$row['multiple'])."麦粒</td></tr>");
	}
	
	print("</table><h3><a href=\"lottery.php?action=showmylottery\" target=\"_blank\" >点击查看我购买过的所有彩票</a></h3><hr/><br/><br/><h2>手动兑奖（匿名彩票）</h2>
<table>
<form action=\"?action=encash\" method=\"post\">
	<tr>
		<td width=\"80%\">&nbsp;&nbsp;&nbsp;&nbsp;领奖请在此输入彩票id<input type=\"text\" name=\"lotteryid\" id=\"lotteryid\" style='width: 60px' />，如果你不记得彩票id，你购买彩票时，已经将id发到你的信箱，或许你可以去那看看。<br/>在此输入该彩票的验证码<input type=\"password\" name=\"selfkey\" id=\"selfkey\" style='width: 60px' />，如果你购买彩票时没有输入验证码，那默认为你的邮箱地址&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td width=\"200px\"><input type=\"submit\"  value='兑奖'/></td></tr></tr>
</from>
</table>");
}
	$userid=$CURUSER['id'];		
	if($action == "takelattery")
	{
		$userbouns=(int)$CURUSER['seedbonus'];
		
		$num1=(int)htmlspecialchars($_POST['num1']);
		$num2=(int)htmlspecialchars($_POST['num2']);
		$num3=(int)htmlspecialchars($_POST['num3']);
		$num4=(int)htmlspecialchars($_POST['num4']);
		$num5=(int)htmlspecialchars($_POST['num5']);
		$multiple=(int)htmlspecialchars($_POST['multiple']);//倍注
		$gived=(int)htmlspecialchars($_POST['gived']);
		$selfkey=htmlspecialchars($_POST['ackey']);		

		if($num1<0||$num1>15||$num2<0||$num2>15||$num3<0||$num3>15||$num4<0||$num4>15||$num5<0||$num5>15||$multiple<=0)
		{
			stdmsg("错误", "你个2B，数字输错了");
			die();
		}
		$charge=$multiple * $hapcharge;
		if($charge > $userbouns)
		{
			stdmsg("错误", "你的麦粒不足，攒点麦粒再来吧^_^");
			die();
		}
		
		$ownid=0;		
		if($gived==0)
		{
			$ownid=$userid;
			if($selfkey=="" or $selfkey ==htmlspecialchars("在此输入兑奖验证码"))
			{
				$selfkey=$CURUSER['email'];	
			}
		}
		if($gived==1)
		{
			$own=htmlspecialchars($_POST['ackey']);
			$sql="SELECT id,email FROM users where username='".$own."'";
			$res=sql_query($sql);
			$row=mysql_fetch_array($res);
			if(!$row)
			{
				stdmsg("错误","请检查你输入的好友用户名");
				die();
			}
			else {
				$ownid=$row['id'];
				$selfkey=$row['email'];
			}
		}
		if($gived==2)
		{
			if($selfkey=="" or $selfkey ==htmlspecialchars("在此输入兑奖验证码"))
			{
				$selfkey=$CURUSER['email'];	
			}
		}
		
		
			$sql="SELECT MAX(id) from drawlottery";
			$res=sql_query($sql);
			$row = mysql_fetch_array( $res );
			$drawid=(int)$row['0'];
			if ($drawid <0) $drawid =1;
			$memcache->set('drawid',$drawid);
		
		$date=date('Y-m-d',time());
		$drawid=(int)$memcache->get('drawid')+1;
		$sql="INSERT INTO lottery(ownerid,selfkey,drawid, num1, num2, num3, num4, num5, shoptime,multiple) VALUES ('$ownid','$selfkey','$drawid', '$num1', '$num2', '$num3', '$num4', '$num5', '$date', '$multiple')";
		if(@sql_query($sql) or sqlerr(__FILE__, __LINE__))
		{
			$lotteryid=mysql_insert_id() ;
			$sql="UPDATE users SET seedbonus =seedbonus - ".$charge." WHERE id = ".$userid;
			@sql_query($sql) or sqlerr(__FILE__, __LINE__);
			writeBonusComment($userid,"花费 $charge 个麦粒购买了彩票");
		
			stdmsg( "购买成功","购买成功,id为".$lotteryid."，彩票数字为$num1,$num2,$num3,$num4,$num5 倍注为 $multiple  。稍后会将详细信息发到你信箱，请不要删除，领奖时可能需要输入彩票id才可领奖");
			if ($ownid==$userid)
			{
				sendMessage(0, $ownid, "彩票购买成功","你获得了第$drawid  期的彩票，id为$lotteryid ，彩票数字为$num1,$num2,$num3,$num4,$num5 倍注为 $multiple 购买时间为$date ，总共花费 $charge 个麦粒。若中奖，麦粒将自动发送到你账户");
			}else if($ownid==0)
			{
				sendMessage(0, $userid, "彩票购买成功"," 你成功购买了一张不记名彩票，你获得了第$drawid  期的彩票，id为$lotteryid ，彩票数字为$num1,$num2,$num3,$num4,$num5 倍注为 $multiple 购买时间为$date ，总共花费 $charge 个麦粒。彩票验证码为$selfkey 由于该彩票匿名购买，领奖时请直接输入彩票id和验证码领奖");
			}
			else if ($ownid!=$userid)
			{
				sendMessage($userid, $ownid, "你的好友送你一张彩票","我买了一张彩票送给你，你获得了第$drawid  期的彩票，id为$lotteryid ，彩票数字为$num1,$num2,$num3,$num4,$num5 倍注为 $multiple 购买时间为$date 。若中奖，麦粒将自动发送到你账户\n 此信息由系统代替用户发送");
				sendMessage(0, $userid, "成功赠送好友一张彩票","彩票数字为$num1,$num2,$num3,$num4,$num5 倍注为 $multiple 购买时间为$date 总共花费麦粒 $charge 。若中奖，麦粒将自动发送到你的好友账户");
			}
		}
	}
	if($action == "encash")
	{
		$lotteryid=htmlspecialchars($_POST['lotteryid']);//倍注
		$selfkey=htmlspecialchars($_POST['selfkey']);
		$sql="SELECT * from lottery where id=".$lotteryid;
		$res=sql_query($sql);
		$row = mysql_fetch_array( $res );
		
		if(!$row)
		{
			stdmsg("错误", "ID输入有误");
			die();
		}
		if($selfkey!=$row['selfkey'])
		{
			stdmsg("错误", "彩票验证码错误，请核对后再试");
			die();
		}
				
		$drawid=$row['drawid'];
		$lnum1=(int)$row['num1'];
		$lnum2=(int)$row['num2'];
		$lnum3=(int)$row['num3'];
		$lnum4=(int)$row['num4'];
		$lnum5=(int)$row['num5'];		
		$multiple=(int)$row['multiple'];
		
		if(((int)$row['isencase'])!=0)
		{
			stdmsg("错误", "该彩票似乎已经兑过奖了");
			die();
		}
		if(((int)$row['ownerid'])!=0)
		{
			stdmsg("错误", "该彩票似乎不是匿名彩票，开奖的时候自动兑过了。");
			die();
		}
		$sql="SELECT * from drawlottery where id=".$drawid;
		$res=sql_query($sql);
		$row = mysql_fetch_array( $res );
		if(!$row)
		{
			stdmsg("错误", "该期彩票未开奖，只能兑已开奖彩票，请核对后再试");
			die();
		}
	//	print_r($row);
		
		$dnum1=(int)$row['num1'];
		$dnum2=(int)$row['num2'];
		$dnum3=(int)$row['num3'];
		$dnum4=(int)$row['num4'];
		$dnum5=(int)$row['num5'];
		$level=6;
		if($lnum1==$dnum1)
		{
			$level=$level-1;
		}
		if($lnum2==$dnum2)
		{
			$level=$level-1;
		}
		if($lnum3==$dnum3)
		{
			$level=$level-1;
		}
		if($lnum4==$dnum4)
		{
			$level=$level-1;
		}
		if($lnum5==$dnum5)
		{
			$level=$level-1;
		}
		$bonus=$cash[$level]*$multiple;
		if ($level<5){
		$sql="UPDATE users SET seedbonus =seedbonus +".$bonus." WHERE id = ".$userid;
		echo $sql;
		if(sql_query($sql) or sqlerr(__FILE__, __LINE__))
		{
			sql_query("UPDATE lottery SET isencase ='$level'  WHERE id = ".$drawid) or sqlerr(__FILE__, __LINE__);
			$date=$date=date('Y-m-d',time());
			sql_query("UPDATE lottery SET encasetime ='".$date."'  WHERE id = ".$drawid) or sqlerr(__FILE__, __LINE__);
			writeBonusComment($userid,"把第$drawid 期中了奖的不记名id为$lotteryid 的彩票兑了奖啦，得到了$bonus 个麦粒");
			sendshoutbox("幸运儿[@$CURUSER[username]] 把第$drawid 期中了奖的不记名id为$lotteryid 的彩票兑了奖啦，得到了$bonus 个麦粒~~喂你站住，交个朋友怎么样");
		}
		
		stdmsg("恭喜" ,"恭喜你在该期开奖中获得$level 等奖，倍注为$multiple 倍，获得麦粒$bonus"."<br/>");
		}
	}
	if($action == "drawlog"){
	print("<h2>近50期开奖纪录</h2><table>
	<tr>
<td class='colhead'>期数</td><td class='colhead'>NUM1</td><td class='colhead'>NUM2</td><td class='colhead'>NUM3</td><td class='colhead'>NUM4</td><td class='colhead'>NUM5</td><td class='colhead'>开奖日期</td>");
if (get_user_class()>14)
echo ("<td class='colhead'>中奖人数（右边数据站长以上可见，请保密</td><td class='colhead'>出</td><td class='colhead'>入</td><td class='colhead'>入-出</td>");
echo ("</tr>");	
	
	$sql="select * from drawlottery order by id desc limit 0,50";
	$res=sql_query($sql);
	while($row = mysql_fetch_array( $res ))
	{
		print("<tr><td>".$row['id']."</td><td>".$row['num1']."</td><td>".$row['num2']."</td><td>".$row['num3']."</td><td>".$row['num4']."</td><td>".$row['num5']."</td><td>".$row['drawtime']."</td>");
	if (get_user_class()>14){
	$sql = "select sum(multiple) from lottery where drawid=".$row['id']." and isencase=";
		$level1 = mysql_fetch_array(sql_query($sql."1"));
		$level2 = mysql_fetch_array(sql_query($sql."2"));
		$level3 = mysql_fetch_array(sql_query($sql."3"));
		$level4 = mysql_fetch_array(sql_query($sql."4"));
		$allnum = '一等奖'.$level1[0].'注，二等奖'.$level2[0].'注，三等奖'.$level3[0].'注，四等奖'.$level4[0].'注';
		$out = $level1[0]*$cash[1]+$level2[0]*$cash[2]+$level3[0]*$cash[3]+$level4[0]*$cash[4];
		$in = mysql_fetch_array(sql_query("select sum(multiple*100) from lottery where drawid=".$row['id']));
echo ("<td>$allnum</td><td>$out</td><td>$in[0]</td><td>".($in[0]-$out)."</td>");
}
echo ("</tr>");	
	}
	print("</table>");
	}
	if($action == "showmylottery")
	{
		if ($_GET['id']) {
			$id = (int)$_GET['id'];
			echo "<h2>别人的彩票</h2>";}
		else 
			{$id = $CURUSER['id'];echo "<h2>我的彩票</h2>这里显示我拥有的彩票，不管是否中奖，都将显示在这里";}
			print("<table><tr><td class='colhead'>期数</td><td class='colhead'>彩票ID</td><td class='colhead'>NUM1</td><td class='colhead'>NUM2</td><td class='colhead'>NUM3</td><td class='colhead'>NUM4</td><td class='colhead'>NUM5</td><td class='colhead'>倍数</td></tr>");
		
		$sql="select * from lottery where ownerid= ".$id;
		$res=sql_query($sql) or sqlerr(__FILE__,__LINE__);
		
		while($row = mysql_fetch_array( $res ))
		{
			print("<tr><td>".$row['drawid']."</td><td>".$row['id'].	"</td><td>".$row['num1']."</td><td>".$row['num2']."</td><td>".$row['num3']."</td><td>".$row['num4']."</td><td>".$row['num5']."</td><td>".$row['multiple']."</td></tr>");
		}
		echo "</table>";
		//if (!mysql_fetch_array( $res )) echo "<h1>购买记录为空</h1>";
	}
	
	stdfoot();

?>