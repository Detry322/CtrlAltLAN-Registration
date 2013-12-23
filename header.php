<?php 

function output_profile()
{
	if (!isset($_SESSION['id']))
	{
		echo "<form action=\"/?login\" method=\"post\"> <input type=\"image\" src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_border.png\"></form>";
		return;
	}
	$steam_color = ($_SESSION['userdata']->personastate > 0) ? "rgb(142, 202, 254)" : "rgb(137, 137, 137)";

	?>
		<img src="<?php echo $_SESSION['userdata']->avatarmedium; ?>" style="width: 52px; height: 52px; border: solid 4px <?php echo $steam_color; ?>; border-radius: 5px; float: right; margin-left: 10px; background-image: url('<?php echo $_SESSION['userdata']->avatar; ?>'); background-size: cover;" />
		<span style="color: <?php echo $steam_color; ?>; font-family: Trebuchet MS,Helvetica,Arial,sans-serif;"><?php echo $_SESSION['userdata']->personaname; ?><br /><br /><a href="/logout.php">Log Out</a></span>
	<?php
};

?>
<!DOCTYPE html>
<html>
<head>
	<head>
	<title>Ctrl Alt LAN</title>
		<link href="/main.css" rel="stylesheet" type="text/css" />
		<!--[if !IE 7]>
			<style type="text/css">
				#wrap {display:table;height:100%}
			</style>
		<![endif]-->
		<script type='text/javascript'>var _gaq=_gaq||[];_gaq.push(["_setAccount","UA-46160012-1"]);_gaq.push(["_setDomainName","ctrlaltlan.org"]);_gaq.push(["_trackPageview"]);(function(){var e=document.createElement("script");e.type="text/javascript";e.async=true;e.src=("https:"==document.location.protocol?"https://ssl":"http://www")+".google-analytics.com/ga.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})()</script>
	</head>
	<body>
	<div id="wrap">
	<div id="header">
		<div class="confiner">
			<h1 class="title">Ctrl Alt LAN</h1>
			<h2 class="subtext">Jack Serrino's LAN Extravaganza v7</h2>
			<div id="profile">
				<?php output_profile(); ?>
			</div>
		</div>
	</div>
	<div id="main" class="confiner">
		<br/><br/>
