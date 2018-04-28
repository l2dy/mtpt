<?php
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");

require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

//if ($CURUSER["uploadpos"] == 'no')
	//stderr($lang_upload['std_sorry'], $lang_upload['std_unauthorized_to_upload'],false);

if ($enableoffer == 'yes')
	$has_allowed_offer = get_row_count("offers","WHERE allowed='allowed' AND userid = ". sqlesc($CURUSER["id"]));
else $has_allowed_offer = 0;
$uploadfreely = user_can_upload("torrents");
$allowtorrents = ($has_allowed_offer || $uploadfreely)||true;
$allowspecial = user_can_upload("music");

//if (!$allowtorrents && !$allowspecial)
//	stderr($lang_upload['std_sorry'],$lang_upload['std_please_offer'],false);
$allowtwosec = ($allowtorrents && $allowspecial);

$brsectiontype = $browsecatmode;
$spsectiontype = $specialcatmode;
$showsource = (($allowtorrents && get_searchbox_value($brsectiontype, 'showsource')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showsource'))); //whether show sources or not
$showmedium = (($allowtorrents && get_searchbox_value($brsectiontype, 'showmedium')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showmedium'))); //whether show media or not
$showcodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showcodec')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showcodec'))); //whether show codecs or not
$showstandard = (($allowtorrents && get_searchbox_value($brsectiontype, 'showstandard')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showstandard'))); //whether show standards or not
$showprocessing = (($allowtorrents && get_searchbox_value($brsectiontype, 'showprocessing')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showprocessing'))); //whether show processings or not
$showteam = (($allowtorrents && get_searchbox_value($brsectiontype, 'showteam')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showteam'))); //whether show teams or not
$showaudiocodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showaudiocodec')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showaudiocodec'))); //whether show languages or not

stdhead($lang_upload['head_upload']);
//加入做种版权类问题公告信息

if (get_user_class() < UC_DOWNLOADER){
	if(!ipv6ip($CURUSER['ip'])) //保种等级以下限制代理地址访问种子区
	{
		stdmsg("你访问了假麦田", "<h1>您使用IPV4地址访问了假麦田,使用假麦田会对正常麦田用户造成偷跑锐捷流量等各种不好的影响,所以该地址将在近期被禁用,不要试图用假麦田做种！<br/><b>请认准麦田PT官方地址：(pt.nwsuaf6.edu.cn)复制括号中的内容到浏览器地址栏粘贴访问。</b><br/>麦田PT只支持IPV6访问，西农用户请使用锐捷有线认证在宿舍及部分办公区获取IPV6地址。<br/>新手QQ群：145543216 麦田PT新人指导</h1>");
		stdfoot();
		exit;
	}
}
else{
	if(!ipv6ip($CURUSER['ip'])) //保种等级以上访问种子区温和的弹窗提示
	{
		$mod_message = "你访问了假麦田！使用假麦田存在账号泄露风险，请尽量少使用！为方便管理，在站长私人的安全的v4tov6环境搭建完毕前，该页面仍然对管理组成员开放,进入特权模式！";
		echo "<script type='text/javascript'>alert('$mod_message');</script>";
	}
}
/*?>
<table align="center">
<tr><td><?=$lang_upload['text_zhuyi']?></td></tr>
<tr><td></td></tr>
</table>*/
print("<div align=\"center\"><form method=\"get\" action=\"torrents.php?\" target=\"_blank\">".$lang_upload['text_search_offer_note']."&nbsp;&nbsp;<input type=\"text\" name=\"search\">&nbsp;&nbsp;<input type=\"hidden\" name=\"incldead\" value=0>");
print("<input type=\"submit\" class=\"btn\" value=\"".$lang_upload['submit_search']."\" /></form></div>");

?>
	<form id="compose" enctype="multipart/form-data" action="takeupload.php" method="post" name="upload">
			<table border="1" cellspacing="0" cellpadding="5" width="940">
				<tr>
					<td class='colhead' colspan='2' align='center'>
						<?php echo $lang_upload['text_tracker_url'] ?>: &nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo  get_protocol_prefix() . $announce_urls[0]?></b>
						<?php
						if(!is_writable($torrent_dir))
						print("<br /><br /><b>ATTENTION</b>: Torrent directory isn't writable. Please contact the administrator about this problem!");
						if(!$max_torrent_size)
						print("<br /><br /><b>ATTENTION</b>: Max. Torrent Size not set. Please contact the administrator about this problem!");
						?>
					</td>
				</tr>
				<tr>
					<td class='colhead' colspan='2' align='center'>
						<?php print($lang_upload['text_notice']);?>
					</td>
				</tr>
<script type="text/javascript">
function uplist(name,list) {
	var childRet = document.getElementById(name);
	for (var i = childRet.childNodes.length-1; i >= 0; i--) { 
		childRet.removeChild(childRet.childNodes.item(i)); 
	} 
	for (var j=0; j<list.length; j++) {
		var ret = document.createDocumentFragment();
		var newop = document.createElement("option");
		newop.id = list[j][0];
		newop.value = list[j][0]; 
		newop.appendChild(document.createTextNode(list[j][1])); 
		ret.appendChild(newop); 
		document.getElementById(name).appendChild(ret); 
	}
}

function secondtype(value) {
<?
	$cats = genrelist($browsecatmode);
        foreach ($cats as $row){
	$catsid = $row['id'];
	$secondtype = searchbox_item_list("sources",$catsid);
	$secondsize = count($secondtype,0);
	$cachearray = $cachearray."var lid".$catsid." = new Array(['0','请选择子类型']";
	for($i=0; $i<$secondsize; $i++){
		$cachearray = $cachearray.",['".$secondtype[$i]['id']."','".$secondtype[$i]['name']."']";
	}
	$cachearray = $cachearray.");\n";
	}
        $cats = genrelist($browsecatmode);
	$cachearray = $cachearray."switch(value){\n";
        foreach ($cats as $row){
        $catsid = $row['id'];
	$cachearray = $cachearray."\tcase \"".$catsid."\": ";
	$cachearray = $cachearray."uplist(\"source_sel\",lid".$catsid.");";
	$cachearray = $cachearray."break;\n";
	}
	$cachearray = $cachearray."}\n";
	print($cachearray);
?>
}

function parseUrl(url){
	var a = document.createElement('a');
	a.href = url;
	return {
		source: url,
		protocol: a.protocol.replace(':', ''),
		host: a.hostname,
		port: a.port,
		query: a.search,
		params: (function(){
			var ret = {}, seg = a.search.replace(/^\?/, '').split('&');
			for (var i = 0; i < seg.length; i++) {
				if (!seg[i]) continue;
				s = seg[i].split('=');
				ret[s[0]] = s[1];
			}
			return ret;
		})(),
		file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
		hash: a.hash.replace('#', ''),
		path: a.pathname.replace(/^([^\/])/, '/$1'),
		relative: a.href.match(/tps?:\/\/[^\/]+(.+)/ || [,''])[1],
		segments: a.pathname.replace(/^\//, '').split('/')
	};
}

function uploadFile (file, altsize, success_callback, failure_callback) {
	var formData = new FormData();
	formData.append('file', file);
	formData.append('altsize', altsize);
	var req = new XMLHttpRequest();
	req.open('POST', '/attachment.php');
	req.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			var resp = this.responseText;
			var i = resp.search(/[0-9A-Fa-f]{32}/);
			var attach_hash = resp.substr(i, 32);
			success_callback(attach_hash);
		} else {
			failure_callback(req);
		}
	}
	req.send(formData);
}

function importBangumi(url){
	var hint_dom = $('#import-hint');
	try {
		var wrapper_url = '/bangumi_wrapper.php?';
		var wrapper_info_url = wrapper_url + 'type=info&url=' + encodeURIComponent(url);
		$(hint_dom).text('开始抓取数据，期间页面可能无法响应按键操作');
		var info_ajax_obj = $.ajax({
			url: wrapper_info_url,
			method: 'GET',
			async: false
		});
		var info_obj = JSON.parse(info_ajax_obj.responseText);
		$(hint_dom).text('已抓取到资源信息');
		var air_date = info_obj.air_date;
		var year = air_date.split('-')[0];
		var title_chs = info_obj.name_cn;
		var title = info_obj.name;
		var summary = info_obj.summary;
		var score = info_obj.rating.score;
		var rating_users = info_obj.rating.total;
		var staff = [];
		var roles = [];
		$.each(info_obj.staff, function(i, v){
			var item = v.name_cn + (v.name_cn != v.name ? '（' + v.name + '）' : '') + '，' + v.jobs.join('，');
			staff.push(item);
		});
		$.each(info_obj.crt, function(i, v){
			var this_actors = [];
			if (v.actors != null)
				$.each(v.actors, function(i2, v2){
					this_actors.push(v2.name);
				});
			var item = v.name_cn;
		    if (this_actors.length > 0) item += '（' + this_actors.join('，') + ' 饰)';
			roles.push(item);
		});
		$('#name').val('[' + year + '][' + title_chs + ']' + (title_chs != title ? '[' + title + ']' : ''));
		$('#descr').val('片名：' + title_chs + (title_chs != title ? '（' + title + '）' : '') + '\n年代：' + year + '\n地区：\n类别：\n语言：\n上映日期/开播日期：' + air_date + '\nBangumi 评分：' + score + '/10 (rated by ' + rating_users + ' users)\nBangumi 链接：[url]' + url + '[/url]\n工作人员：' + staff.join('；') + '\n角色：' + roles.join('；') + '\n\n简介：\n' + summary + '\n');
		$(hint_dom).text('信息填充完毕。请完善相关信息并上传种子。');
		var cover_url = info_obj.images.large;
		var wrapper_cover_url = wrapper_url + 'type=image&url=' + encodeURIComponent(cover_url);
		var xhr_obj = new XMLHttpRequest();
		xhr_obj.open('GET', wrapper_cover_url);
		xhr_obj.responseType = 'blob';
		xhr_obj.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200) {
				var cover_obj = this.response;
				var cover_file = new File([cover_obj], 'cover.jpg');
				var success_callback = function (attach_hash) {
					$('#descr').val('[attach]' + attach_hash + '[/attach]\n\n' + $('#descr').val());
					$(hint_dom).text('信息填充完毕。请完善相关信息并上传种子。');
				};
				var failure_callback = function (xhr) { ; };
				uploadFile(cover_file, 'no', success_callback, failure_callback);
			}
		}
		xhr_obj.send();
	} catch (err) {
		console.log(err);
		$(hint_dom).text('发生异常，无法继续。请检查 URL 是否正确。如果确认正确，请稍等一下后再试。IE、Edge 等浏览器无法发自动传图。');
	}
	setTimeout(function(){
		$(hint_dom).text('');
	}, 6000);
}

function importDouban(url){
	var hint_dom = $('#import-hint');
	try {
		var wrapper_url = '/douban_wrapper.php?';
		var wrapper_info_url = wrapper_url + 'type=info&url=' + encodeURIComponent(url);
		$(hint_dom).text('开始抓取数据，期间页面可能无法响应按键操作');
		var info_ajax_obj = $.ajax({
			url: wrapper_info_url,
			method: 'GET',
			async: false
		});
		var info_obj = JSON.parse(info_ajax_obj.responseText);
		$(hint_dom).text('已抓取到资源信息');
		var title_chs = info_obj.title, title = info_obj.original_title;
		var summary = info_obj.summary;
		var year = info_obj.year;
		var score = info_obj.rating.average;
		var area = info_obj.countries.join('，');
		var cats = info_obj.genres.join('/');
		var directors = [];
		var actors = [];
		$.each(info_obj.directors, function(i, v){
			directors.push(v.name);
		});
		$.each(info_obj.casts, function(i, v){
			actors.push(v.name);
		});
		$('#name').val('[' + year + '][' + title_chs + ']' + (title_chs != title ? '[' + title + ']' : ''));
		$('#descr').val('片名：' + title_chs + (title_chs != title ? '（' + title + '）' : '') + '\n年代：' + year + '\n地区：' + area + '\n类别：' + cats + '\n语言：\n豆瓣评分：' + score + '/10\n豆瓣链接：[url]' + url + '[/url]\n导演：' + directors.join('，') + '\n主演：' + actors.join('，') + '\n\n简介：\n' + summary + '\n');
		$('input[name="dburl"]').val(url);
		$(hint_dom).text('信息填充完毕。请完善相关信息并上传种子。');
		var cover_url = info_obj.images.large;
		var wrapper_cover_url = wrapper_url + 'type=image&url=' + encodeURIComponent(cover_url);
		var xhr_obj = new XMLHttpRequest();
		xhr_obj.open('GET', wrapper_cover_url);
		xhr_obj.responseType = 'blob';
		xhr_obj.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200) {
				var cover_obj = this.response;
				var cover_file = new File([cover_obj], 'cover.jpg');
				var success_callback = function (attach_hash) {
					$('#descr').val('[attach]' + attach_hash + '[/attach]\n\n' + $('#descr').val());
					$(hint_dom).text('信息填充完毕。请完善相关信息并上传种子。');
				};
				var failure_callback = function (xhr) { ; };
				uploadFile(cover_file, 'no', success_callback, failure_callback);
			}
		}
		xhr_obj.send();
	} catch (err) {
		console.log(err);
		$(hint_dom).text('发生异常，无法继续。请检查 URL 是否正确。如果确认正确，请稍等一下后再试。IE、Edge 等浏览器无法发自动传图。');
	}
	setTimeout(function(){
		$(hint_dom).text('');
	}, 6000);
}

function importImdb(url){
	var hint_dom = $('#import-hint');
	try {
		var wrapper_url = '/imdb_wrapper.php?';
		var wrapper_info_url = wrapper_url + 'type=info&url=' + encodeURIComponent(url);
		$(hint_dom).text('开始抓取数据，期间页面可能无法响应按键操作');
		var info_ajax_obj = $.ajax({
			url: wrapper_info_url,
			method: 'GET',
			async: false
		});
		var info_obj = JSON.parse(info_ajax_obj.responseText);
		$(hint_dom).text('已抓取到资源信息');
		var title = info_obj.title;
		var summary = info_obj.storyline;
		var content_rating = info_obj.content_rating;
		var year = info_obj.year;
		var length = info_obj.length;
		var director = info_obj.director;
		var rating = info_obj.rating;
		var rating_users = info_obj.rating_count;
		var cats = info_obj.genre.join('/');
		var stars = info_obj.stars.join('/');
		var countries = info_obj.metadata.countries.join('/');
		$('#name').val('[' + year + ']' + '[' + title + ']');
		$('#descr').val('片名：' + title + '\n年代：' + year + '\n地区：' + countries + '\n类别：' + cats + '\n语言：\n时长：' + length + ' 分钟\nIMDb 评分：' + rating + '/10 of ' + rating_users + ' users\nIMDb 链接：[url]' + url + '[/url]\n内容分级：' + content_rating + '\n导演：' + director + '\n主演：' + stars + '\n\n简介：\n' + summary + '\n');
		$('input[name="imdburl"]').val(url);
		$(hint_dom).text('信息填充完毕。请完善相关信息并上传种子。');
	} catch (err) {
		console.log(err);
		$(hint_dom).text('发生异常，无法继续。请检查 URL 是否正确。如果确认正确，请稍等一下后再试。');
	}
	setTimeout(function(){
		$(hint_dom).text('');
	}, 6000);
}

$(document).ready(function(){
	uplist("source_sel",new Array(['0','请先选择一级类型']));
	$("#browsecat").change(function(){
		if($("#browsecat").val() == "0") return;
		secondtype($("#browsecat").val());
		removeSubcat();
	});

	function catChange()
	{
		secondtype($("#browsecat").val());
		removeSubcat();
	}

	$("#source_sel").change(function(){
		//start modified by SamuraiMe,2013.05.17 
		//用于自动生成种子标题
		removeSubcat();

		$.getJSON("guize.php?id="+$("#browsecat").val()+"&source_sel="+$(this).val()+"&t="+new Date(), function(result){
			$("#gstishi").html(result[0]);
			$("td td:has('#name')").prepend(result[1]);
			$("#subcat").slideDown();
		});
	});

	$("#subcat input:checkbox, #subcat input:radio").live("click", function() {
		var name = $(this).attr("name");
		var index = $(this).index("#subcat input[name="+name+"]");
		var length = $("#subcat input[name="+name+"]").length;
		if ($(this).next().next().is("[id=temp_input]")) {
			$(this).next().toggle().next().toggle();
		} else {
			if (index == length-1) {
				var input = $(this).next().text();
				$(this).next().hide().after('<input type="text" id="temp_input" value="'+input+'"/>').focus();
				$(this).val($(this).next().next().val());
				$(this).next().text($(this).next().next().val());
			} else {
				removeTempInput();
			}
		}
	});

	$("#temp_input").live("blur", function() {
		$(this).prev().text($(this).val()).show();;
		$(this).prev().prev().val($(this).val());
		$(this).remove();
		generateName();
	});


	$("#subcat input").live("change", function() {
		generateName();
	});

	function removeSubcat()
	{
		if ($("#subcat").length>0) {
			$("#subcat").slideUp(function() {
				$(this).remove();
			})
		};
	}

	function removeTempInput() {
		$("#temp_input").prev().text($("#temp_input").val())
			.prev().val($("#temp_input").val());
		$("#temp_input").prev().show().end().remove();
		//$("#temp_input").remove();
	}

	function generateName () {
		var names = new Array();
		var tempName = '';
		var name = '';
		$("#subcat input:checked, #subcat input[value!=''][id!=temp_input]:text, #subcat input[type=hidden]").each(function() {
			if ($(this).attr("name") == tempName) {
				names[names.length-1] += "/" + $(this).val();
			} else {
				names[names.length] = $(this).val();
			}
			tempName = $(this).attr("name");
		});

		for (var i = 0; i < names.length; i++) {
			name += "[" + names[i] + "]";
		};
		$("#name").val(name);
	}
	//end modified by SamuraiMe,2013.05.17

	//modified by SamuraiMe,2013.05.19 获取豆瓣与IMDB的URL
	//获取豆瓣url
	$("#browsecat").change(function() {
		var cat = $(this).val();
		if (401!=cat && 402!=cat && 405!=cat && 414 !=cat && 404!=cat) {
			$("#select_douban").remove();
			$("#reselect_douban").remove();
		}
	});
	//中文名改变时刷新显示可能的豆瓣链接
	$("[name=chinese_name]").live("blur", function (){
		$("#reselect_douban").remove();
		displayDoubanItem();
	});
	$("#reselect_douban").live("click", function() {
		$(this).remove();
		displayDoubanItem();
	});
	//豆瓣链接被选中时
	$(".douban_item").live("click", function () {
		$a = $(this).find("a");
		var url = $a.first().attr("href");
		$("[name=dburl]").val(url);
		$("#select_douban").remove();
		$("[name=dburl]").after("<input type=\"button\" id=\"reselect_douban\"value=\"重新选择\"/>");
	});

	function displayDoubanItem() 
	{
		var q = $("[name=chinese_name]").val();
		var cat = $("#browsecat").val();
		if (q && (401==cat || 402==cat || 405==cat || 414 ==cat || 404==cat) ) {
			var requestUrl = "imdb/imdb_url.php?res=douban&title=" + q + "&type=" + $("#browsecat").val();
			$.get(requestUrl, function (data) {
				if ($("#select_douban").length>0) {
					$("#select_douban").remove();
				}
				if (data.length>0) {
					$("[name=dburl]").after(data);
				};
			});
		}
	}


	//英文名改变时刷新显示可能的imdb链接
	$("[name=english_name]").live("blur", function (){
		$("#reselect_imdb").remove();
		displayImdbItem();
	});
	$("#reselect_imdb").live("click", function() {
		$(this).remove();
		displayImdbItem();
	});
	//imdb链接被选中时
	$(".imdb_item").live("click", function () {
		$a = $(this).find("a");
		var url = $a.first().attr("href");
		$("[name=imdburl]").val(url);
		$("#select_imdb").remove();
		$("[name=imdburl]").after("<input type=\"button\" id=\"reselect_imdb\"value=\"重新选择\"/>");
	});

	function displayImdbItem() 
	{
		var q = $("[name=english_name]").val();
		var cat = $("#browsecat").val();
		if (q && (401==cat || 402==cat || 405==cat || 414 ==cat || 404==cat) ) {
			var requestUrl = "imdb/imdb_url.php?res=imdb&title=" + q + "&type=" + $("#browsecat").val();
			$.get(requestUrl, function (data) {
				if ($("#select_imdb").length>0) {
					$("#select_imdb").remove();
				}
				if (data.length>0) {
					$("[name=imdburl]").after(data);
				};
			});
		}
	}
	//获取豆瓣与IMDB的URL结束

	//引用发布功能开始
	$("#cite_torrent_btn").click(function(){
		citeTorrent();
	});

	function citeTorrent()
	{
		var id = $("#cite_torrent").val();
		//需要判断id是否合法
		if (id != '') {
			if ($("#cite_hint").length > 0) {
				$("#cite_hint").remove();
			};
			$.getJSON("./citetorrent.php?torrent_id="+id, function(data){
				if (data["exist"] == "yes") {
					$("#browsecat").val(data["category"]);
					catChange();
					$("#source_sel").val(data["source"]);
					$("#name").val(data["name"]);
					$("input[name=small_descr]").val(data["small_descr"]);
					$("input[name=url]").val(data["url"]);
					$("input[name=dburl]").val(data["dburl"]);
					$("#descr").text(data["descr"]);
				} else {
					$("#cite_torrent_btn").after("<span id=\"cite_hint\">所引用的种子不存在..<span>");
				}
			});
		};
	}
	if ($("#cite_torrent").val() != "") {
		citeTorrent();
	};
	//引用发布功能结束

	$("#qr").click(function(){
		var err = "";
		if($("#browsecat").val() == 0)  err += "请选择[类型]\n\n";
		if($("#source_sel").val() == 0)	err += "请选择[子类型]\n\n";
		if($("#torrent").val() == "") err += "请选择[种子文件]\n\n";
		if($("#name").val().length < 10) err += "[标题]内容不得少于10个字符\n\n";
		if($("#descr").val().length < 50) err += "[简介]内容不得少于50个字符\n\n";
		if($("#descr").val().search(/attach|img/i) == -1) err += "[简介]内容必须包含图片\n\n";
		if(err == "") return true;
		jAlert(err);
		return false;
	});

	// 快速填写信息功能 Start
	$('#import-button').click(function(){
		var url = $('#import-source-url').val();
		var url_parsed = parseUrl(url);
		switch (url_parsed.host) {
		case 'movie.douban.com':
			importDouban(url);
			break;
		case 'www.imdb.com':
			importImdb(url);
			break;
		case 'bangumi.tv':
		case 'bgm.tv':
			importBangumi(url);
			break;
		default:
			$('#import-hint').text('输入的 URL 不是 豆瓣 / IMDb / Bangumi 的链接。');
			break;
		}
	});
	// 快速填写信息功能 End

	$("#browsecat").change();
});
</script>
<?php
$torrent_id = isset($_GET["cite_torrent_id"]) ? 0+$_GET["cite_torrent_id"] : "";
tr("引用发布", "<input type=\"text\" id=\"cite_torrent\" name=\"cite_torrent\" value=\"$torrent_id\" /><input type=\"button\" id=\"cite_torrent_btn\" value=\"引用\"/>", 1);
tr("快速填写信息", '<input type="text" id="import-source-url" placeholder="相应网站上资源信息页的 URL" size="80"> <input type="button" id="import-button" value="导入"> <br> 此功能可以从 豆瓣 / IMDb / Bangumi 上抓取信息，并生成标题、简介。目前仍然需要手动选择种子类型。IMDb 无法实现自动传图。<br><span id="import-hint"></span>', 1);
tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" />\n", 1);
if (get_user_class() >= 11){ //退休及等级以上（暂时把等级硬编码）可发布时选择是否MTTV 2017.09.10 纳兰斯坦
tr("是否MTTV组", "<input type=\"radio\" name=\"ismttv\" value=\"yes\"/>yes<input type=\"radio\" name=\"ismttv\" value=\"no\" checked/>no <b>非MTTV组成员请勿修改此项！</b>\n", 1);
tr("转载限制", '<select name="prohibit_reshipment"><option value="no_restrain">无限制</option><option value="cernet_only">仅限教育网转载</option><option value="prohibited">禁止转载</option></select> <b>非MTTV组成员请勿修改此项！</b>', 1);
}else{//低等级默认隐藏提交no，如有用户修改此项则后台验证不通过
print("<input type=\"hidden\" name=\"ismttv\" value=\"no\"/>");
print("<input type=\"hidden\" name=\"prohibit_reshipment\" value=\"no_restrain\"/>");
}
if ($allowtorrents){
		$disablespecial = " onchange=\"disableother('browsecat','specialcat')\"";
		$s = "<select name=\"type\" id=\"browsecat\" ".($allowtwosec ? $disablespecial : "").">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
		$cats = genrelist($browsecatmode);
                                        foreach ($cats as $row)
                                                $s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
                                        $s .= "</select>\n";
                                }
                                else $s = "";
                                if ($allowspecial){
                                        $disablebrowse = " onchange=\"disableother('specialcat','browsecat')\"";
                                        $s2 = "<select name=\"type\" id=\"specialcat\" ".$disablebrowse.">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
                                        $cats2 = genrelist($specialcatmode);
                                        foreach ($cats2 as $row)
                                                $s2 .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
                                        $s2 .= "</select>\n";
                                }
                                else $s2 = "";
                                tr($lang_upload['row_type']."<font color=\"red\">*</font>", ($allowtwosec ? $lang_upload['text_to_browse_section'] : "").$s.($allowtwosec ? $lang_upload['text_to_special_section'] : "").$s2.($allowtwosec ? $lang_upload['text_type_note'] : ""),1);

				if ($showsource || $showmedium || $showcodec || $showaudiocodec || $showstandard || $showprocessing){
                                        if ($showsource){
                                               $source_select = torrent_selection($lang_upload['text_source'],"source_sel","sources");
                                        }
                                        else $source_select = "";

                                        if ($showmedium){
                                                $medium_select = torrent_selection($lang_upload['text_medium'],"medium_sel","media");
                                        }
                                        else $medium_select = "";

                                        if ($showcodec){
                                                $codec_select = torrent_selection($lang_upload['text_codec'],"codec_sel","codecs");
                                        }
                                        else $codec_select = "";

                                        if ($showaudiocodec){
                                                $audiocodec_select = torrent_selection($lang_upload['text_audio_codec'],"audiocodec_sel","audiocodecs");
                                        }
                                        else $audiocodec_select = "";

                                        if ($showstandard){
                                                $standard_select = torrent_selection($lang_upload['text_standard'],"standard_sel","standards");
                                        }
                                        else $standard_select = "";

                                        if ($showprocessing){
                                                $processing_select = torrent_selection($lang_upload['text_processing'],"processing_sel","processings");
                                        }
                                        else $processing_select = "";

                                        //tr($lang_upload['row_quality']."<font color=red>*</font>", $source_select . $medium_select. $codec_select . $audiocodec_select. $standard_select . $processing_select, 1 );
                                        tr($lang_upload['row_quality']."<font color=red>*</font>", "<select id='source_sel' name='source_sel'></select>", 1 );
                                }

				//tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" onchange=\"getname()\" />\n", 1);
				//tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" />\n", 1);
				if ($altname_main == 'yes'){
					tr($lang_upload['row_torrent_name'], "<b>".$lang_upload['text_english_title']."</b>&nbsp;<input type=\"text\" style=\"width: 250px;\" name=\"name\" />&nbsp;&nbsp;&nbsp;
<b>".$lang_upload['text_chinese_title']."</b>&nbsp;<input type=\"text\" style=\"width: 250px\" name=\"cnname\"><br /><font class=\"medium\">".$lang_upload['text_titles_note']."</font>", 1);
				}
				else
					tr($lang_upload['row_torrent_name'], "<input type=\"text\" style=\"width: 650px;\" id=\"name\" name=\"name\" /><br /><font class=\"medium\">".$lang_upload['text_torrent_name_note']."</font>", 1);
				if ($smalldescription_main == 'yes')
				tr($lang_upload['row_small_description'], "<input type=\"text\" style=\"width: 650px;\" name=\"small_descr\" /><br /><font class=\"medium\">".$lang_upload['text_small_description_note']."</font>", 1);
				tr($lang_upload['row_description_note'],"<br /><font size=+1 color=brown>".$lang_upload['text_description_note']."</font>", 1);
				
				get_external_tr();
				get_dbexternal_tr();
				if ($enablenfo_main=='yes')
					tr($lang_upload['row_nfo_file'], "<input type=\"file\" class=\"file\" name=\"nfo\" /><br /><font class=\"center\">".$lang_upload['text_only_viewed_by'].get_user_class_name($viewnfo_class,false,true,true).$lang_upload['text_or_above']."</font>", 1);
				print("<tr><td class=\"rowhead\" style='padding: 3px' valign=\"top\">".$lang_upload['row_description']."<font color=\"red\">*</font></td><td class=\"rowfollow\">");
				textbbcode("upload","descr","",false);
				print("</td></tr>\n");


				if ($showteam){
					if ($showteam){
						$team_select = torrent_selection($lang_upload['text_team'],"team_sel","teams");
					}
					else $showteam = "";

					tr($lang_upload['row_content'],$team_select,1);
				}

				//==== offer dropdown for offer mod  from code by S4NE
				$offerres = sql_query("SELECT id, name FROM offers WHERE userid = ".sqlesc($CURUSER[id])." AND allowed = 'allowed' ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
				if (mysql_num_rows($offerres) > 0)
				{
					$offer = "<select name=\"offer\"><option value=\"0\">".$lang_upload['select_choose_one']."</option>";
					while($offerrow = mysql_fetch_array($offerres))
						$offer .= "<option value=\"" . $offerrow["id"] . "\">" . htmlspecialchars($offerrow["name"]) . "</option>";
					$offer .= "</select>";
					tr($lang_upload['row_your_offer']. (!$uploadfreely && !$allowspecial ? "<font color=red>*</font>" : ""), $offer.$lang_upload['text_please_select_offer'] , 1);
				}
				//===end

				if(get_user_class()>=$beanonymous_class)
				{
					tr($lang_upload['row_show_uploader'], "<input type=\"checkbox\" name=\"uplver\" value=\"yes\" />".$lang_upload['checkbox_hide_uploader_note'], 1);
				}
				?>
				<tr><td class="toolbox" align="center" colspan="2"><input id="qr" type="submit" class="btn" value="<?php echo $lang_upload['submit_upload']?>" />
				<input id="preDIv" type="button" class="btn" onClick="javascript:preview_torrent();return false;" value="预览" />
					<input id="EditDIv" type="button" style="display:none;" onClick="javascript:edit_torrent();return false;" class="btn" value="继续编辑" />
					</td>

				</td></tr>	</table>
	</form>
<?php
stdfoot();
