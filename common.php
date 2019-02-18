<?php
iconv_set_encoding("internal_encoding", "UTF-8");

function errorMessage ($msg, $url) {
	session_start();
	$_SESSION['flash'][] = array(type=>"alert-error", message=>$msg);
	session_write_close();
	if (isset($url)) {
		header("Location: $url");
		die();
	}
}
function errorMessage2 ($msg) {
	session_start();
	$_SESSION['flash'][] = array(type=>"alert-error", message=>$msg);
	session_write_close();
}
function successMessage ($msg, $url) {
	session_start();
	$_SESSION['flash'][] = array(type=>"alert-success", message=>$msg);
	session_write_close();
	if (isset($url)) {
		header("Location: $url");
		die();
	}
}

function testMessage ($msg) {
	session_start();
	$_SESSION['flash'][] = array(type=>"alert-success", message=>$msg);
	session_write_close();
}

function infoMessage ($msg, $url) {
	session_start();
	$_SESSION['flash'][] = array(type=>"alert-info", message=>$msg);
	session_write_close();
	if (isset($url)) {
		header("Location: $url");
		die();
	}
}
function normalMessage ($msg, $url) {
	session_start();
	$_SESSION['flash'][] = array(message=>$msg);
	session_write_close();
	if (isset($url)) {
		header("Location: $url");
		die();
	}
}
function niceTime($timeStr) {
	//$t = strtotime($timeStr);

	$dt = new DateTime("@$timeStr");  // convert UNIX timestamp to PHP DateTime
return $dt->format('d-m-Y'); 
}

function fullTime ($timeStr) {
	return date("j F Y, H:i:s",strtotime($timeStr));
}
function performMerge ($current, $new, &$toInsert, &$toDelete) {
	foreach ($new as $k => $v) {
		if (!in_array(intval($v), $current)) $toInsert[] = intval($v);
		$new[$k] = intval($new[$k]);
	}
	foreach ($current as $k => $v) {
		if (!in_array(intval($v), $new)) $toDelete[] = intval($v);
	}
	return;
}

function valid_date($str) 
    {
        $str = str_replace('/', '-', $str);
        if ($arr = strtotime($str))
        {
            $arr = explode("-", date("d-m-Y", strtotime($str)));
            $yyyy = $arr[2];
            $mm = $arr[1];
            $dd = $arr[0];
            if (is_numeric($yyyy) && is_numeric($mm) && is_numeric($dd))
            {
                return checkdate($mm, $dd, $yyyy);
            }
        }
        return false;
    }


/*$UPLOAD_RELDIR = "../drives/";
$UPLOAD_ABSDIR = "/var/www/vhosts/riicc.sg/sys/drives/";

$ZIP_RELDIR = "../drives/zip/";
$ZIP_ABSDIR = "/var/www/vhosts/riicc.sg/sys/drives/zip/";*/
?>