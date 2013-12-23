<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once 'meekrodb.php';
require_once 'phpqrcode.php';

DB::$user = 'registrations';
DB::$password = 'pztKBbrFyZf6zvRr';
DB::$dbName = 'registrations';
session_start();

if (!isset($_SESSION['id']))
{
	header("Location: /");
	die();
}

$player = DB::queryFirstRow("SELECT * FROM registrations WHERE steamid=%s",$_SESSION['id']);

if ($player == null)
{
	header("Location: /");
	die();
}

$avatar = $_SESSION['userdata']->avatarfull;
$ch = curl_init($avatar);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$ret = curl_exec($ch);
$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($retcode == 404)
{
	$avatar = $_SESSION['userdata']->avatarmedium;
	$ch = curl_init($avatar);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($retcode == 404)
	{
		$avatar = $_SESSION['userdata']->avatar;
	}
}

QRcode::png($player['steamid'].":".$player['secret'], "/var/www/qrcodes/".$player['steamid'].".png");

?>
<!DOCTYPE html>
<html>
<head>
	<head>
		<link href="/print.css" rel="stylesheet" type="text/css" />
		<!--[if !IE 7]>
			<style type="text/css">
				#wrap {display:table;height:100%}
			</style>
		<![endif]-->
	</head>
	<body onload="setTimeout(window.print,1000);">
		<h1>Ctrl Alt LAN</h1>
		<h2>Printable Ticket and Waiver</h2>
		<hr />
		<img id="profilepic" src="<?php echo $avatar; ?>" />
		<img id="qrcode" src="<?php echo "/qrcodes/".$player['steamid'].".png"; ?>" />
		<p><?php echo $_SESSION['userdata']->personaname; ?></p>
		<hr style="clear: both;" />
		<ol>
			<li>I agree not to spend more than 50% of my time at the LAN playing any one game.</li>
			<li>I agree not to rage, or be otherwise unsportsmanlike.</li>
			<li>I agree not to sell food.</li>
			<li>I agree to clean up my mess in its entirety when I leave.</li>
			<li>I agree to use at a maximum one (1) ethernet port and two (2) power ports at any given time.</li>
			<li>I agree not to bring, use, or otherwise associate with any illegal substance while at the party.</li>
			<li>I agree not to leave the premises between the hours of 1 AM and 6 AM</li>
			<li>I agree not to use speakers.</li>
			<li>I agree not to daisy chain power strips.</li>
			<li>I agree to bring my own ethernet cord and power strip, or rent one for $2.</li>
			<li>I agree not to torrent anything, legal or illegal, while at the LAN.</li>
			<li>I agree not to cheat by using any sort of external assistance or program</li>
		</ol><hr />
		<h1>Legal</h1>
		<span id="legal"><b>LIMITATION OF LIABILITY:</b> Sponsors, Administrators and/or Other Participants are not responsible for any inaccurate information, whether caused by the Official Web Site or by the Web Site users or by any of the equipment or programming associated with or utilized in the event or the Tournament or by any technical or human error which may occur in the processing of Tournament results. Administrators/Sponsors assume NO liability for any injury, loss, or damage of any kind arising from or in connection with any person's participation in the Event or Tournament, including without limitation, participation in any real life activity, or injury, loss or damage sustained from use of any prize won. If for any reason the Event or the Tournament is not capable of running as planned, including infection by computer virus', bugs, tampering, unauthorized intervention, fraud, technical failures, or any other causes beyond the control of the Sponsors, Administrators and/or Other Participants which corrupt or affect the administration, security, fairness, integrity, or proper conduct of the Event or the Tournament, Administrator's/Sponsors reserve the right at their sole discretion to cancel, terminate, modify or suspend the Event and the Tournament. By participating in the Tournament, each Participant agrees to be bound by the Official Rules. ADMINISTRATORS/SPONSORS/OTHER PARTICIPANTS SHALL NOT BE LIABLE FOR PUNITIVE, INCIDENTAL, CONSEQUENTIAL OR SPECIAL DAMAGES WHETHER OR NOT SUCH DAMAGES COULD HAVE BEEN FORESEEN AND WHETHER OR NOT ANY SPONSOR OR ADMINISTRATOR RECEIVED NOTICE THEREOF.<br /><br />

<b>RELEASE:</b> The individual signing below ("Registrant") does hereby RELEASE AND WAIVE any and all causes of action, claims, losses and damages, whether such causes of action, claims, losses and damages are known or unknown, whether such causes of action, claims, losses, and damages existing in law or in equity, and whether such causes of action, claims, losses, and damages exist under contract, tort, strict liability or other theory, owned by Registrant or which may be owned by Registrant as against Sponsors or any Sponsor or any Sponsor's directors, officers, employees, agents and representatives, arising out of, relating to or in connection with the Event and/or the Tournament (collectively, the "CLAIMS"). Registrant FOREVER DISCHARGES Administrators/Sponsors/Other Participants and their respective directors, officers, employees, agents and representatives from all Claims. Further more, by signing, you and/or your Parent or Guardian understand that a variety of files maybe shared on the network, i.e., music, movies, pornography, or other types of files, and that Sponsors, Administrators, and Other Participants are not responsible for what you view or save on your computer.<br /><br />

<b>INDEMNITY:</b> Registrant does hereby agree to indemnify, defend and hold harmless each Administrator/Sponsor/Other Participant and each Sponsor's directors, officers, employees, agents and representatives from and against all losses, lawsuits, causes of action, claims and damages which arise from the acts or omissions of Registrant in connection with the Event or the Tournament.</span><br /><br /><br />
Name:____________________________ Sign:_____________________________ Date:______________
	</body>
</html>