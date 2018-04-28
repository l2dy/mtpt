<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
//loggedinorreturn();
stdhead($lang_rules['head_rules']);
$Cache->new_page('rules', 900, true);
if (!$Cache->get_page())
{
$Cache->add_whole_row();
//make_folder("cache/" , get_langfolder_cookie());
//cache_check ('rules');
begin_main_frame();
if ($CURUSER)
$lang_id = $CURUSER['lang'];
else
$lang_id = get_guest_lang_id();
$is_rulelang = get_single_value("language","rule_lang","WHERE id = ".sqlesc($lang_id));
if (!$is_rulelang){
	$lang_id = 6; //English
}
begin_frame('目录',false);
$index = "<ul>
	<li><a href=#1 class='faqlink'>总则</a></li>
	<li><a href=#2 class='faqlink'>论坛总则</a></li>
	<li><a href=#3 class='faqlink'>评论总则</a></li>
	<li><a href=#4 class='faqlink'>群聊区总则</a></li>
	<li><a href=#5 class='faqlink'>违规处罚规则</a></li>
	<li><a href=#6 class='faqlink'>管理组退休待遇</a></li>
	<li><a href=#49 class='faqlink'>字幕区规则</a></li>
	<li><a href=#56 class='faqlink'>促销及置顶规则</a></li>
	<li><a href=#57 class='faqlink'>标题命名规则</a></li>
	<li><a href=#122 class='faqlink'>上传规则</a></li>
	</ul>";
print($index);
end_frame();

$res = sql_query("SELECT * FROM rules WHERE lang_id = ".sqlesc($lang_id)." ORDER BY id");
while ($arr=mysql_fetch_assoc($res)){
	begin_frame('<span id='.$arr['id'].'>'.$arr['title'].'</span>', false);
	print(format_comment($arr["text"]));
	end_frame();
}
end_main_frame();
}
//cache_save ('rules');
stdfoot();
?>
