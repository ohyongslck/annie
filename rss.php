<?

header('Content-Type: application/xml; charset=utf-8');

$host = $_SERVER['HTTP_HOST'];

setlocale(LC_TIME, "kr_KR.utf8");

date_default_timezone_set('Asia/Seoul');

$startdir = '.';

$showthumbnails = false; 

$showdirs = true;

$forcedownloads = false;

$hide = array('list.php');

$displayindex = false;

$allowuploads = false;

$overwrite = false;

error_reporting(0);

if(!function_exists('imagecreatetruecolor')) $showthumbnails = false;

$leadon = $startdir;

if($leadon=='.') $leadon = '';

if((substr($leadon, -1, 1)!='/') && $leadon!='') $leadon = $leadon . '/';

$startdir = $leadon;



if($_GET['dir']) {

	// check this is okay.

	

	if(substr($_GET['dir'], -1, 1)!='/') {

		$_GET['dir'] = $_GET['dir'] . '/';

	}

	

	$dirok = true;

	$dirnames = split('/', $_GET['dir']);

	for($di=0; $di<sizeof($dirnames); $di++) {

		

		if($di<(sizeof($dirnames)-2)) {

			$dotdotdir = $dotdotdir . $dirnames[$di] . '/';

		}

		

		if($dirnames[$di] == '..') {

			$dirok = false;

		}

	}

	

	if(substr($_GET['dir'], 0, 1)=='/') {

		$dirok = false;

	}

	

	if($dirok) {

		 $leadon = $leadon . $_GET['dir'];

	}

}







$opendir = $leadon;

if(!$leadon) $opendir = 'torrent';

if(!file_exists($opendir)) {

	$opendir = 'torrent';

	$leadon = $startdir;

}



clearstatcache();

if ($handle = opendir($opendir)) {

	while (false !== ($file = readdir($handle))) { 

		// first see if this file is required in the listing

		if ($file == "." || $file == "..")  continue;

		$discard = false;

		for($hi=0;$hi<sizeof($hide);$hi++) {

			if(strpos($file, $hide[$hi])!==false) {

				$discard = true;

			}

		}

		

		if($discard) continue;

		if (@filetype($leadon.$file) == "dir") {

			if(!$showdirs) continue;

		

			$n++;

			$key = @filemtime($leadon.$file) . ".$n";

			$dirs[$key] = $file . "/";

		}

		else {

			$n++;

			$key = @filemtime($leadon.$file) . ".$n";

			$files[$key] = $file;

			

			if($displayindex) {

				if(in_array(strtolower($file), $indexfiles)) {

					header("Location: $file");

					die();

				}

			}

		}

	}

	closedir($handle); 

}



// sort our files

@krsort($dirs, SORT_NUMERIC);

@krsort($files, SORT_NUMERIC);



// order correctly

if($_GET['order']=="desc" && $_GET['sort']!="size") {$dirs = @array_reverse($dirs);}

if($_GET['order']=="desc") {$files = @array_reverse($files);}

$dirs = @array_values($dirs); $files = @array_values($files);



?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>Ohys-Raws</title>
		<link>https://torrents.ohys.net/</link>
		<?
			$arsize = sizeof($files);

			if(strlen($_GET[q])>0)
				$q = explode(" ", $_GET[q]);
			
			for($i=0, $count=0; $i<$arsize && $count<20; $i++) {
				$filename = iconv("EUC-KR", "UTF-8", $files[$i]);

				// 파일명에서 검색 관련 부분만 남김
				$testname = str_replace("[Ohys-Raws] ", "", $filename);
				if(strpos($testname, "(")>0)
					$testname = substr($testname, 0, strpos($testname, "("));
				else if(strpos($filename, "[")>0)
					$testname = substr($testname, 0, strpos($testname, "["));

				// 검색어 대조
				$failed = false;
				for($j=0; $q[$j]; $j++){
					if(!eregi($q[$j], $testname)){
						$failed = true;
						break;
					}
				}
				if($failed)
					continue;

				// 검색 통과한 출력물 개수 카운트
				$count++;

				if(strlen($filename)>203)
					$filename = substr($files[$i], 0, 200) . '...';

				$fileurl = $leadon.rawurlencode(iconv("EUC-KR", "UTF-8", $files[$i]));
				$pubdate = date("D, d M Y H:i:s T", @filemtime($leadon.$files[$i]));
		?><item><title><![CDATA[<?=$filename?>]]></title><link><![CDATA[https://torrents.ohys.net/<?=$fileurl?>]]></link><pubDate><?=$pubdate?></pubDate>
		</item>
		<?
			}
		?>

	</channel>
</rss>
