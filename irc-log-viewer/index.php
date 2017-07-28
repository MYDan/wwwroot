<?php

 
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


function message ($arrayvalue) {
	$arrayvalue[0] = "";
	$arrayvalue[1] = "";
	$arrayvalue[2] = "";
	$arrayvalue[3] = "";
	$temp = implode (" ", $arrayvalue);
	return $temp;
}

function colorhash ($nick, $colornicks) {
	$temp = "";
	$test = "";
	// is the nick in the list of predefined colors?
	if (array_key_exists($nick, $colornicks)) {
		return $colornicks[$nick];
	} else {
		// if the nick is not in the list take first 3 letters of nick and apply some magic to generate a color
		for($i = 0; $i <= 2; $i++) {
			$temp .= ord(substr(strtoupper(md5($nick)), $i, 1))-48;
			// 0 = 48
			// Z = 90
			$test .= ord(substr(strtoupper(md5($nick)), $i, 1))-48;
		}
		//return round((($temp / 126) * 255));
		return $temp;
	}
}
function filenamedate($input) {
	// format: logs/2013-06/LOG_2013-06-23.txt
	preg_match_all("'LOG_([0-9][0-9][0-9][0-9])-([0-9][0-9])-([0-9][0-9]).txt'", $input, $logdate);
	$logdate1['day'] = $logdate[3][0];
	$logdate1['month'] = $logdate[2][0];
	$logdate1['year'] = $logdate[1][0];
	return $logdate1;
}

//Load colors for nicknames
require ("nicks.php");

$showoverview = false;
if ((!isset($_GET['day'])) || (!isset($_GET['month'])) || (!isset($_GET['year']))) {
	$showoverview = true;
}
$day = null;
if (isset($_GET['day']))
	$day = $_GET['day'];
if (!is_numeric($day)) { 
	$day = date('d');
	$month = date('m');
	$year = date('Y');
}

$month = null;
if (isset($_GET['month']))
	$month = $_GET['month'];
if (!is_numeric($month)) { 
	$day = date('d');
	$month = date('m');
	$year = date('Y');
}

$year = null;
if (isset($_GET['year']))
	$year = $_GET['year'];
if (!is_numeric($year)) { 
	$day = date('d');
	$month = date('m');
	$year = date('Y');
}
?>
<head>
<title>MYDan IRC logs</title>
<style>
	@font-face {
		font-family: 'Droid Sans Mono';
		font-style: normal;
		font-weight: 400;
		src: local('Droid Sans Mono'), url(droidsansmono.woff) format('woff');
	}
	body {
		line-height:120%;
		background-color:#f5f5f5;
		background: url("/assets/img/grain-eee.png") repeat scroll 0 0 rgba(0, 0, 0, 0);
		font-family: 'Droid Sans Mono', courier;
		font-size:0.9em;
	}
	a.loglink {	
		color:#222a44;
	}
	.nick { 
		font-weight:bold;
		text-align:right;
		padding-right:5px;
	}
	.content {
		padding-left:7px;
		border-left:1px solid #999;
	}
	.quit {
		color:#888 !important;
	}
	.join {
		color:#888 !important;
	}
	.nickchange {
		color:#888 !important;
	}
	.line-index {
		color:#AAA;
		font-size:0.7em;
	}
	.line-index a{
		color:#AAA;
	}
	hr {
		border-bottom: 1px solid #DDDDDD;
		border-top:none;
		border-left:none;
		border-right:none;
		margin-bottom: 1em;
		clear:both;
	}
	td.irclog  {
		padding-left:15px;
		margin-top:1px;
		margin-bottom:1px;
	}
	table.irclog {
		font-size:0.9em;
	}
	.even {
		background-color: rgba(0, 0, 0, 0.06);
	}
	.odd {
		background-color: rgba(0, 0, 0, 0.02);
	}
	form {
		display:inline;
		padding:0;
		margin:0;
		clear:none;
	}
	.monthoverview {
		vertical-align: top;
		display:inline-block;
		padding-right:50px;
		padding-bottom:100px;
	}
</style>
<meta http-equiv="refresh" content="300">
</head>
<body>

<?php
$logdir = "LOG/".$year."-".$month."/";
$date = $year."-".$month."-".$day;
$date_month = $year."-".$month."-1";
$file = $logdir."LOG_".$year."-".sprintf("%02s", $month)."-".sprintf("%02s", $day).".txt";

// set timezone:
//echo phpversion();
date_default_timezone_set("Asia/Shanghai");

// Display all days that have logs available
echo "Current Server Time: ".date("H:i")." (Asia/Shanghai)";

if ($showoverview) {
	echo "<h1>MYDan IRC Logs</h1>";
	$month_index = 0;
	
	while(1) {
		$read_month = date('m', strtotime($date_month." -".$month_index." month"));
		$read_year = date('Y', strtotime($date_month." -".$month_index." month"));
		$logdir = "LOG/".$read_year."-".$read_month."/";
		
		if (is_dir($logdir)) {
			echo "<div class=\"monthoverview\"><h3>".date("F", strtotime($date_month." -".$month_index." month")).", ".date("Y", strtotime($date_month." -".$month_index." month"))."</h3>";
			if ($dh = opendir($logdir)) {
				$files = scandir($logdir);
				foreach($files as $file) {
					if ($file != "." && $file != "..") {
						$file_date = filenamedate($file);
						$file_size = round(filesize($logdir.$file)/1024,2);
						$filename = $file_date['day']."<sup>".date("S", mktime(0, 0, 0, $file_date['month'], $file_date['day'], $file_date['year']))."</sup> (".
						date("l", mktime(0, 0, 0, $file_date['month'], $file_date['day'], $file_date['year'])).") - ".$file_size." KB";
						echo "<a class=\"loglink\" href=\"index.php?day=".$file_date['day']."&month=".$file_date['month']."&year=".$file_date['year']."\">".$filename."</a><br />";
					}
				}
				closedir($dh);
			}
			echo "</div>";
		}
		$month_index++;
		if ($month_index > 80) {
			exit();
		}
	}
} else {
?>
<h1>MYDan IRC Logs</h1>
<h2><?php echo $year."/".$month."/".$day; ?></h2>
<h3>Timezone: Asia/Shanghai</h3>

<?php 
$day_now = date('d');
$month_now = date('m');
$year_now = date('Y');
$daym1 = date('d', strtotime($date .' -1 day'));
$monthm1 = date('m', strtotime($date .' -1 day'));
$yearm1 = date('Y', strtotime($date .' -1 day'));
$dayp1 = date('d', strtotime($date .' +1 day'));
$monthp1 = date('m', strtotime($date .' +1 day'));
$yearp1 = date('Y', strtotime($date .' +1 day'));
echo "<form style=\"float:left;\" action=\"index.php\" method=\"get\">
	<input type=\"hidden\" name=\"day\" value=\"".$daym1."\" />
	<input type=\"hidden\" name=\"month\" value=\"".$monthm1."\" />
	<input type=\"hidden\" name=\"year\" value=\"".$yearm1."\" />
	<input type=\"submit\" value=\"&larr; Previous Day\" />
</form>";

echo "<form style=\"float:right;\" action=\"index.php\" method=\"get\">
	<input type=\"hidden\" name=\"day\" value=\"".$dayp1."\" />
	<input type=\"hidden\" name=\"month\" value=\"".$monthp1."\" />
	<input type=\"hidden\" name=\"year\" value=\"".$yearp1."\" />
	<input type=\"submit\" value=\"Next Day &rarr;\" />
</form>";
?>
<div style="margin:auto; width:200px; text-align:center;">
	<form action="index.php" ><input type="submit" value="Day Selection"></form>
	<form action="index.php" >
		<?php
			echo "<input type=\"hidden\" name=\"day\" value=\"".$day_now."\" />
			<input type=\"hidden\" name=\"month\" value=\"".$month_now."\" />
			<input type=\"hidden\" name=\"year\" value=\"".$year_now."\" />";
		?>
		<input type="submit" value="Today">
	</form>
</div>
<hr />
<table class="irclog" cellpadding="0" cellspacing="0">
<?
$line_index = 1;
if (!file_exists($file)) {
	exit ("file not found");
}
$handle = fopen($file, "r");
if ($handle) {
	while (($line = fgets($handle)) !== false) {	
		$tags = explode(" ", $line);
		$timestamp = $tags[0];
		$tag = $tags[1];
		$nick = $tags[3];
		$message = $tags[4];
		
		switch ($tag) {
			case "M": echo "<tr class=\"message";
			break;
			
			case "P": echo "<tr class=\"quit";
			break;
			
			case "Q": echo "<tr class=\"quit";
			break;
			
			case "N": echo "<tr class=\"nickchange";
			break;
			
			case "J": echo "<tr class=\"join";
			break;
			
			case "T": echo "<tr class=\"topic";
			break;
		}
		
		// Even/Odd Rows
		if (($tag == "M") || ($tag == "P") || ($tag == "Q") || ($tag == "N") || ($tag == "J") || ($tag == "T")) {
			if ($line_index %2) {
				echo " even\">";
			} else {
				echo " odd\">";
			}
		}
		
		// Line Index
		echo "<td class=\"irclog\"><div class=\"line-index\"><a href=\"#".$line_index."\" name=\"".$line_index."\">".$line_index++."</a></div></td>";

		// Timestamp
		switch ($tag) {
			case "A":
			case "M": 
				echo "<td class=\"irclog\"><div style=\"color: hsl(".colorhash($nick, $colornicks).", 100%, 30%);\" class=\"date\">".date("H:i", $timestamp - date("Z"))."</div></td>";
				break;
			
			default: echo "<td class=\"irclog\"><div class=\"date\">".date("H:i", $timestamp - date("Z"))."</div></td>";
			break;
		}
		
		// Nick
		$nicksettopic = false;
		if ($nick == "*") {
			$nick = "Topic";
		} else {
			$nicksettopic = true;
		}
		switch ($tag) {
			case "M": echo "<td class=\"irclog nick\"><div style=\"color: hsl(".colorhash($nick, $colornicks).", 100%, 30%);\" class=\"nick\">".htmlentities($nick, ENT_QUOTES)."</div></td>";
			break;
			
			case "A": echo "<td class=\"irclog nick\"><div style=\"color: hsl(".colorhash($nick, $colornicks).", 100%, 30%);\" class=\"nick\">".htmlentities($nick, ENT_QUOTES)."</div></td>";
			break;
			
			default: echo "<td class=\"irclog nick\"><div class=\"nick\">".htmlentities($nick, ENT_QUOTES)."</div></td>";
			break;
		}
		
		//Message
		switch ($tag) {
			case "M": echo "<td><div class=\"content\" style=\"color: hsl(".colorhash($nick, $colornicks).", 100%, 30%);\">".htmlentities(message($tags), ENT_QUOTES);
			break;
			
			case "P": echo "<td><div class=\"content\"> left the channel";
			break;
			
			case "Q": echo "<td><div class=\"content\"> left the channel";
			break;
			
			case "T": 
				if ($nicksettopic) {
					echo "<td><div class=\"content\"> has set the topic";
				} else {
					echo "<td><div class=\"content\"> ".htmlentities(message($tags), ENT_QUOTES);
				}
				break;
			
			case "N": echo "<td><div class=\"content\"> changed nick to: ".htmlentities(message($tags), ENT_QUOTES);
			break;
			
			case "A": echo "<td><div class=\"content\" style=\"color: hsl(".colorhash($nick, $colornicks).", 100%, 30%);\"><i>".htmlentities(message($tags), ENT_QUOTES)."</i>";
			break;
			
			case "J": echo "<td><div class=\"content\"> joined the channel";
			break;
		}
		// debug
		//echo "<br /><br />".$line;
		
		echo "</div></td></div></div></tr>";
		
		
		 
	}
} else {
	// error opening the file.
}
echo "</table><br />";

echo "<form style=\"float:left;\" action=\"index.php\" method=\"get\">
	<input type=\"hidden\" name=\"day\" value=\"".$daym1."\" />
	<input type=\"hidden\" name=\"month\" value=\"".$monthm1."\" />
	<input type=\"hidden\" name=\"year\" value=\"".$yearm1."\" />
	<input type=\"submit\" value=\"&larr; Previous Day\" />
</form>";

echo "<form style=\"float:right;\" action=\"index.php\" method=\"get\">
	<input type=\"hidden\" name=\"day\" value=\"".$dayp1."\" />
	<input type=\"hidden\" name=\"month\" value=\"".$monthp1."\" />
	<input type=\"hidden\" name=\"year\" value=\"".$yearp1."\" />
	<input type=\"submit\" value=\"Next Day &rarr;\" />
</form>";
?>
<div style="margin:auto; width:200px; text-align:center;">
	<form action="index.php" ><input type="submit" value="Day Selection"></form>
	<form action="index.php" >
		<?php
			echo "<input type=\"hidden\" name=\"day\" value=\"".$day_now."\" />
			<input type=\"hidden\" name=\"month\" value=\"".$month_now."\" />
			<input type=\"hidden\" name=\"year\" value=\"".$year_now."\" />";
		?>
		<input type="submit" value="Today">
	</form>
</div>
<br />
<?php
}
?>
</body>

