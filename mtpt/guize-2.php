<?
$id = $_GET['id'];

$country = array("大陆", "港台", "欧美", "日韩", "其他地区");
$videoType = array("爱情", "恐怖", "喜剧", "动作", "伦理", "悬疑", "科幻", "青春", "其他类型");
$resolution = array("1080P", "720P", "480P","其他");
$quality = array( "DVDrip", "HDRip", "BDRip", "R5", "DVDScr", "BDMV","BDISO","DVDISO","其他品质");
$videoFileType = array("MKV", "RMVB", "MP4", "AVI", "MPEG", "ts","ISO", "其他文件类型");
$musicFileType = array("MP3", "MP4", "flac", "ape", "wav", "TTA", "DTS");
$platform = array("Windows", "Mac", "Linux", "Mobile", "Android", "其他平台");
$serial = array("剧场/OVA", "完结剧集", "连载剧集");
$language = array("国语", "粤语", "英语", "日语", "韩语", "法语", "德语", "西班牙语", "其他语言");
$softwareLanguage = array("简体中文", "繁体中文", "英语", "日语", "韩语", "其他语言");
$subtitle = array("简体中文", "繁体中文", "英文字幕", "中英字幕", "中日字幕", "中韩字幕", "无需字幕", "外挂字幕", "暂无字幕", "其他字幕");
$comictitle = array("简体GB","繁体BIG5","繁简外挂","简体外挂","繁体外挂","无字幕","其他");
$ennamemore = "--如果资源已有完整的英文命名，请不要擅自修改。如[HunanTV.Lu.Zhen.Chuan.Qi.Complete.HDTV.720p.x264-CHDTV]--";

$result = array();
$html = '<div id="subcat" style="display:none">';
$html_hide = '';
switch($id){
	case '401'://电影
		$hint = "[年份][影片中文名][英文全名][电影类型][字幕情况][格式][其他信息]";
		//$html .= '<label class="subcat">国别:&nbsp;&nbsp;&nbsp;&nbsp;</label>' . generateInput($country, "radio", "country") . '<br/>';
		$html .= '<label class="subcat">发行时间:</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">中文片名:</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">英文片名:</label><input type="text" name="english_name" style="width:50%"/>'.$ennamemore.'<br/>';
		$html_hide .= '<label class="subcat">类型:&nbsp;&nbsp;&nbsp;&nbsp;</label>' . generateInput($videoType, "checkbox", "movie_type") . '<br/>';
		$html_hide .= '<label class="subcat">语言:&nbsp;&nbsp;&nbsp;&nbsp;</label>' . generateInput($language, "checkbox", "language") . '<br/>';
		$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';
		$html_hide .= '<label class="subcat">分辨率:</label>' . generateInput($resolution, "radio", "resolution") . '<br/>';
		$html_hide .= '<label class="subcat">影片质量:</label>' . generateInput($quality, "radio", "quality") . '<br/>';
		$html_hide .= '<label class="subcat">字幕情况:</label>' . generateInput($subtitle, "radio", "subtitle") . '<br/>';

		break;
	case '402'://剧集
		$hint = "[年份][文件名/英文名][S季度E集数][语言字幕][格式][完结/连载][其他]";
		$html_hide .= '<label class="subcat">国别:&nbsp;&nbsp;&nbsp;&nbsp;</label>' . generateInput($country, "radio", "country") . '<br/>';
		$html .= '<label class="subcat">发行时间:</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">中文片名:</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">英文片名:</label><input type="text" name="english_name" style="width:50%"/><br/>'.$ennamemore.'<br/>';
		$html .= '<label class="subcat">S季度E集数:</label><input type="text" name="jidu"/><br/>';
		$html_hide .= '<label class="subcat">字幕情况:</label>' . generateInput($subtitle, "radio", "subtitle") . '<br/>';
		$html_hide .= '<label class="subcat">分辨率:</label>' . generateInput($resolution, "radio", "resolution") . '<br/>';
		$html_hide .= '<label class="subcat">影片质量:</label>' . generateInput($quality, "radio", "quality") . '<br/>';
		$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';
		$html .= '<label class="subcat">连载情况:</label>' . generateInput($serial, "radio", "serial") . '<br/>';

		break;
		
		case '403'://综艺
		$hint = "[具体时间(2011-01-01)][国别][中文/英文名称][格式][其他说明]";
		$html .= '<label class="subcat">国别：&nbsp;&nbsp;&nbsp;&nbsp;</label>' . generateInput($country, "radio", "country") . '<br/>';
		$html .= '<label class="subcat">具体时间:</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">中文片名:</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">英文片名:</label><input type="text" name="english_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">分辨率:</label>' . generateInput($resolution, "radio", "resolution") . '<br/>';
		$html_hide .= '<label class="subcat">影片质量:</label>' . generateInput($quality, "radio", "quality") . '<br/>';
		$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';

		break;
	case '404'://纪录片
		$hint = "[来源][年份][中文名][英文全名][字幕情况][格式][其他信息]";
		$html .= '<label class="subcat">来源:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="source"/><br/>';
		$html .= '<label class="subcat">年份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">中文名:&nbsp;&nbsp;</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">英文名:&nbsp;&nbsp;</label><input type="text" name="english_name" style="width:50%"/><br/>'.$ennamemore.'<br/>';
		$html .= '<label class="subcat">分辨率:</label>' . generateInput($resolution, "radio", "resolution") . '<br/>';
		$html .= '<label class="subcat">影片质量:</label>' . generateInput($quality, "radio", "quality") . '<br/>';
		$html .= '<label class="subcat">字幕情况:</label>' . generateInput($subtitle, "radio", "subtitle") . '<br/>';
		$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';

		break;
	case '405'://动漫
		switch ($_GET["source_sel"]) {

			case '46'://漫画
				$hint = "[Comic][中文名][英文名/罗马音名称][卷数][格式][连载/完结][其他]";
				$html .= '<label class="subcat">中文名:&nbsp;&nbsp;</label><input type="text" name="chinese_name" style="width:40%"/>*不同译名用/分割；无适当中文译名可省。<br/>';
				$html .= '<label class="subcat">英文名:&nbsp;&nbsp;</label><input type="text" name="english_name" style="width:40%"/>*片假名词汇应当转写为原文<br/>';
				$html .= '<label class="subcat">卷数:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="num"/>*例：Vol.1-Vol.24 Fin<br/>';
				$html .= '<label class="subcat">格式:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="sum"/>*例：ZIP/JPG；RAR/PNG；<br/>';
				$html .= "<label class=\"subcat\">是否完结</label>" . generateInput(array("完结","连载"), "radio", "wanjieornot") . '<br/>';
		
			break;
			
			case '76'://音乐
				$hint = "[专辑发售时间][EAC][专辑名][专辑艺人][文件格式][码率][其他（如有/无BK）]";
				$html .= '<label class="subcat">发布时间</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
				$html .= "<label class=\"subcat\">是否无损</label>" . generateInput(array("无损"), "checkbox", "wusunornot") . '<br/>';
				$html .= '<label class="subcat">专辑名:&nbsp;&nbsp;</label><input type="text" name="name" style="width:50%"/><br/>';
				$html .= '<label class="subcat">专辑艺人:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="author"/><br/>';
				$html .= '<label class="subcat">文件格式:&nbsp;&nbsp;&nbsp;</label><input type="text" name="codetype"/><br/>';
				$html .= '<label class="subcat">码率(无损不填):</label><input type="text" name="code"/><br/>';
		
			break;
			
			case '47'://其他
				$hint = "[资源类型][资源名称][资源格式][其他]";
				$html .= '<label class="subcat">资源类型</label><input type="text" name="codetype" style="width:50%"/><br/>';
				$html .= '<label class="subcat">资源名称</label><input type="text" name="name" style="width:50%"/><br/>';
				$html .= '<label class="subcat">资源格式</label><input type="text" name="code" style="width:50%"/><br/>';
				$html .= '<label class="subcat">其他:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="other"/><br/>';

			break;
		
			default://其他情况相同
				$hint = "[中文名][英文名][集数or卷数][字幕组or压制组][字幕信息][分辨率][品质信息][数据格式][连载/完结][其他]";
				$html .= '<label class="subcat">月份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="month" style="width:30%"/>*除新番连载以外不填！<br/>';
				$html .= '<label class="subcat">中文名:&nbsp;&nbsp;</label><input type="text" name="chinese_name" style="width:40%"/>*不同译名用/分割；无适当中文译名可省<br/>';
				$html .= '<label class="subcat">英文名:&nbsp;&nbsp;</label><input type="text" name="english_name" style="width:40%"/>*片假名词汇应当转写为原文<br/>';
				$html .= '<label class="subcat">集数:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="num"/>*例：TV 01-13 Fin；MOVIE；OVA 01；Vol.1-Vol.6；<br/>';
				$html .= '<label class="subcat">字幕or压制组:</label><input type="text" name="subtitle_group"/>*填写官方名称<br/>';
				$html .= '<label class="subcat">字幕信息:</label>' . generateInput($comictitle, "radio", "subtitle") . '<br/>';
				$html .= '<label class="subcat">分辨率:</label>' . generateInput($resolution, "radio", "resolution") . '<br/>';
				$html .= '<label class="subcat">质量:</label>' . generateInput($quality, "radio", "quality") . '<br/>';
				$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';
				$html .= "<label class=\"subcat\">是否完结</label>" . generateInput(array("完结","连载"), "radio", "wanjieornot") . '<br/>';
				break;
		}
		break;
		
	case '414'://音乐
				$hint = "[专辑发售时间][EAC][专辑名][专辑艺人][文件格式][码率][其他（如有/无BK）]";
				$html .= '<label class="subcat">发布时间</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
				$html .= "<label class=\"subcat\">是否无损</label>" . generateInput(array("无损"), "checkbox", "wusunornot") . '<br/>';
				$html .= '<label class="subcat">专辑名:&nbsp;&nbsp;</label><input type="text" name="name" style="width:50%"/><br/>';
				$html .= '<label class="subcat">专辑艺人:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="author"/><br/>';
				$html .= '<label class="subcat">文件格式:&nbsp;&nbsp;&nbsp;</label><input type="text" name="codetype"/><br/>';
				$html .= '<label class="subcat">码率(无损不填):</label><input type="text" name="code"/><br/>';
		
			break;
			
	case '406'://mv
		$hint = "[国别][具体发布时间][艺术家][资源名称][类型/风格][文件格式][分辨率][其它介绍]";
		$html .= '<label class="subcat">国别:&nbsp;&nbsp;&nbsp;&nbsp;</label>' . generateInput($country, "radio", "country") . '<br/>';
		$html .= '<label class="subcat">具体时间:</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">艺术家:&nbsp;&nbsp;</label><input type="text" name="artist"/><br/>';
		$html .= '<label class="subcat">文件名称:</label><input type="text" name="file_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">类型/风格:</label><input type="text" name="style"/><br/>';
		$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';

		break;
	case '407'://体育
		$hint = "[具体日期][发布内容(体育类型)][节目名称][字幕说明][格式][其他说明]";
		$html .= '<label class="subcat">具体时间:&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">体育类型:</label><input type="text" name="style"/><br/>';
		$html .= '<label class="subcat">节目名称:&nbsp;&nbsp;</label><input type="text" name="program_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">解说语言:&nbsp;&nbsp;</label>' . generateInput($language, "checkbox", "language") . '<br/>';
		$html .= '<label class="subcat">文件格式:&nbsp;&nbsp;</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';
		$html .= '<label class="subcat">录像分辨率:</label><input type="text" name="quality"/><br/>';
		$html .= '<label class="subcat">转载情况:&nbsp;&nbsp;</label><input type="text" name="zhuanzai"/><br/>';

		break;

	case '408'://软件
		$hint = "[应用平台][软件名称及版本][软件语言][软件格式][软件类型][其他说明]";
		$html .= '<label class="subcat">应用平台:</label>' . generateInput($platform, "radio", "platform") . '<br/>';
		$html .= '<label class="subcat">软件名称及版本:</label><input type="text" name="edition" style="width:50%"/><br/>';
		$html .= '<label class="subcat">软件语言:</label>' . generateInput($softwareLanguage, "radio", "softwareLanguage") . '<br/>';
		$html .= '<label class="subcat">软件类型:</label><input type="text" name="software_type"/><br/>';

		break;
	case '410'://游戏
		$hint = "[游戏中文名][游戏英文名][游戏类型][制作公司][数据格式][版本][其他]";
		$html .= '<label class="subcat">发行时间:</label><input type="text" name="release_time"/><br/>';
		$html .= '<label class="subcat">中文名:&nbsp;&nbsp;</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">英文名:&nbsp;&nbsp;</label><input type="text" name="english_name" style="width:50%"/><br/>';
		$html .= '<label class="subcat">类型:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="game_type"/><br/>';
		$html .= '<label class="subcat">制作公司:</label><input type="text" name="company"/><br/>';
		$html .= '<label class="subcat">数据格式:</label><input type="text" name="format"/><br/>';
		$html .= '<label class="subcat">版本:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="edition"/><br/>';

		break;
	case '411'://学习
		switch ($_GET["source_sel"]) {
			case '29'://专业学科
				$hint = "[学科类别][年份][(学校名/讲师名)中文名][(英文名)][集数][格式][字幕]";
				$html .= '<label class="subcat">学科类别:</label><input type="text" name="xueke"/><br/>';
				$html .= '<label class="subcat">年份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
				$html .= '<label class="subcat">学校名/讲师名:</label><input type="text" name="school"/><br/>';
				$html .= '<label class="subcat">中文名:&nbsp;&nbsp;</label><input type="text" name="chinese_name"/><br/>';
				$html .= '<label class="subcat">英文名:&nbsp;&nbsp;</label><input type="text" name="english_name"/><br/>';
				$html .= '<label class="subcat">集数:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="jishu"/><br/>';
				$html .= '<label class="subcat">字幕情况:</label>' . generateInput($subtitle, "radio", "subtitle") . '<br/>';
				$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';

				break;
			
			case '30'://讲座
				$hint = "[讲座演讲][日期][讲师名(地点)主题][(英文名)][(集数)][格式][字幕]";
				$html .= '<input type="hidden" name="lecture" value="讲座演讲"/>';
				$html .= '<label class="subcat">日期:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
				$html .= '<label class="subcat">讲师名:&nbsp;&nbsp;</label><input type="text" name="lecturer"/><br/>';
				$html .= '<label class="subcat">主题:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="chinese_name"/><br/>';
				$html .= '<label class="subcat">英文名:&nbsp;&nbsp;</label><input type="text" name="english_name"/><br/>';
				$html .= '<label class="subcat">集数:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="jishu"/><br/>';
				$html .= '<label class="subcat">字幕情况:</label>' . generateInput($subtitle, "radio", "subtitle") . '<br/>';
				$html .= '<label class="subcat">文件格式:</label>' . generateInput($videoFileType, "radio", "filetype") . '<br/>';

				break;

			case '31'://期刊
				$hint = "[期刊书籍][年份][中文名][英文名][期数]][格式]";
				$html .= '<input type="hidden" name="book" value="期刊书籍"/>';
				$html .= '<label class="subcat">年份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
				$html .= '<label class="subcat">中文片名:</label><input type="text" name="chinese_name" style="width:50%"/><br/>';
				$html .= '<label class="subcat">英文片名:</label><input type="text" name="english_name" style="width:50%"/><br/>';
				$html .= '<label class="subcat">期数:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="qishu"/><br/>';
				$html .= '<label class="subcat">格式:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="file_type"/><br/>';

				break;
			
			case '32'://外语学习
				$hint = "[语言类别][年份][(书籍名):内容][格式][属性]";
				$html .= '<label class="subcat">语言类别:</label><input type="text" name="language"/><br/>';
				$html .= '<label class="subcat">年份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
				$html .= '<label class="subcat">(书籍名):内容:</label><input type="text" name="chinese_name"/><br/>';
				$html .= '<label class="subcat">属性&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="property"/><br/>';

				break;

			case '58'://考研资料
				$hint = "[考研相关][年份][机构名：课程名][格式]";
				$html .= '<label class="subcat">考研相关:</label><input type="text" name="kaoyan"/><br/>';
				$html .= '<label class="subcat">年份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
				$html .= '<label class="subcat">机构名：课程名</label><input type="text" name="chinese_name"/><br/>';
				$html .= '<label class="subcat">格式&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="file_type"/><br/>';

				break;

			case '75'://其他
				$hint = "[其他资料][中文名][格式][简介]";
				$html .= '<label class="subcat">其他资料:</label><input type="text" name="other" value="其他资料"/><br/>';
				$html .= '<label class="subcat">年份:&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="release_time"/><br/>';
				$html .= '<label class="subcat">中文名&nbsp;&nbsp;</label><input type="text" name="chinese_name"/><br/>';
				$html .= '<label class="subcat">格式&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="file_type"/><br/>';
				$html .= '<label class="subcat">简介&nbsp;&nbsp;&nbsp;&nbsp;</label><input type="text" name="synopsis"/><br/>';

				break;
			
			default:
				# code...
				break;
		}

		break;
	case '423'://原创
		$hint = "此类型较特殊，请按情况用[]括号分隔，自行起格式填写";
		break;
	case '409'://其他
		$hint = "此类型较特殊，请按情况用[]括号分隔，自行起格式填写";
		break;
}
$html_hide = <<<hide
<div>
  <a href="#" title="点此展开更多选项" id="moreBtn">更多...</a>
  <script type="javascript/text">
	$(document).ready(function(){
  	$('#moreBtn').click(function(){
			$(".moreSetting").slideToggle();
			return false;
	});
  });
</script>
  <div class="moreSetting" style="display: none;">
  $html_hide  </div></div>
hide;
$html .= $html_hide;
$html .="<b style='color:red;font-size:22px'>标题预览</b><br/><b style='color:blue'>你也可以不使用上面的标题生成系统，直接修改下面的标题。但是如果你这么做，不符合标题格式的种子将被直接移入回收站</b><br/>你也可以先自动生成再编辑，但是不要先编辑再点上面的按钮。会覆盖已经编辑的标题。";
$result[] = $hint;
$result[] = $html . "</div>";
$result = json_encode($result);
echo $result;

/**
* 生成一组复选框或者单选框
*
* @param array $cat 需要生成的目录数组
* @param string $type input的type
* @param string $name input的name
* @return string 生成的html
*/
function generateInput($cat, $type, $name)
{
	$html = '';
	foreach ($cat as $value) {
		$html .= '<input type="' . $type . '" name="'. $name .'" value="'. $value .'"/><label>' . $value .'</label>';
	}

	return $html;
}
?>