<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();
require_once 'meekrodb.php';
DB::$user = 'registrations';
DB::$password = 'pztKBbrFyZf6zvRr';
DB::$dbName = 'registrations';
$API = "98B0770824D696E9DCA0CA3DCF8AC83D";


$result = DB::query("SELECT * FROM registrations WHERE state >= 0");
$count = DB::count();

$steamids = "";

foreach ($result as $row)
{
	$steamids .= ",".$row['steamid'];
}

function find_computer($a, $b)
{
	foreach ($a as $c)
	{
		if ($c['steamid'] == $b)
			return $c['computer'];
	}
	return 0;
}

$steamids = substr($steamids,1);

$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$API."&steamids=".$steamids;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);
$data = json_decode($data);
$array = $data->response->players;
$types = array("Undecided", "Windows Desktop", "Windows Laptop", "Mac Desktop", "Mac Laptop", "Linux Desktop", "Linux Laptop", "Dual-boot Desktop", "Dual-boot Laptop", "Nintendo Console", "Microsoft Console", "Sony Console");

include 'header.php';

?>

<div class="generic box">
				<span class="title">Attendees</span><br />
				<span class="subtext" style="font-size:0.7em;"><?php echo ($count == 1) ? ($count." person has") : ($count." people have"); ?> registered.</span><br />
				<span class="subtext" style="font-size:0.7em;"><?php echo ((33-$count) == 1) ? ((33-$count)." ticket remains") : ((33-$count)." tickets remain"); ?></span>

<?php

foreach ($array as $player)
{
	$steam_color = ($player->personastate > 0) ? "rgb(142, 202, 254)" : "rgb(137, 137, 137)";
	?>
	<div class="checklist-box" style="padding-left: 30px; padding-top: 20px; font-size: 0.8em; padding-right: 45px; color: <?php echo $steam_color; ?>; font-family: Trebuchet MS,Helvetica,Arial,sans-serif; text-align: right;">
	<a href="<?php echo $player->profileurl; ?>"><img src="<?php echo $player->avatarmedium; ?>" style="width: 60px; height: 60px; border: solid 4px <?php echo $steam_color; ?>; border-radius: 5px; margin-left: 10px; background-image: url('<?php echo $player->avatar; ?>'); background-size: cover; float: left;" /></a>
	<?php echo $player->personaname; ?><br /><br />
	<span style="font-family: TF2 Secondary; color: #c9bca3; font-size: 1em;"><?php echo $types[find_computer($result, $player->steamid)]; ?></span>
	<?php
	if ($_SESSION['id'] == '76561198012175982')
	{
		?>
		<br /><br /><a href="/delete.php?steamid=<?php echo $player->steamid; ?>" class="button">Remove</a>
		<?php
	}
	?>
	</div>
	<?php
}

?>
<br />
<a class="button" href="/">Return</a>
</div>

<?php

include 'footer.php';