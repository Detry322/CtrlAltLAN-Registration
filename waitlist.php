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


$result = DB::query("SELECT * FROM registrations WHERE state < 0 ORDER BY state DESC");
$count = DB::count();

$steamids = "";

foreach ($result as $row)
{
	$steamids .= ",".$row['steamid'];
}

function get_player($a, $b)
{
	foreach ($a as $c)
	{
		if ($c->steamid == $b)
			return $c;
	}
	return null;
}

$steamids = substr($steamids,1);

$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$API."&steamids=".$steamids;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);
$data = json_decode($data);
$array = $data->response->players;
include 'header.php';

?>

<div class="generic box">
				<span class="title">The Waitlist</span><br />
				<span class="subtext" style="font-size:0.7em;"><?php echo ($count == 1) ? ($count." person is") : ($count." are"); ?> on the waitlist.</span><br />

<?php

for($i = 0; $i < count($result); $i++)
{
	$player = get_player($array, $result[$i]['steamid']);
	$steam_color = ($player->personastate > 0) ? "rgb(142, 202, 254)" : "rgb(137, 137, 137)";
	?>
	<div class="checklist-box" style="padding-left: 30px; padding-top: 20px; font-size: 0.8em; padding-right: 45px; color: <?php echo $steam_color; ?>; font-family: Trebuchet MS,Helvetica,Arial,sans-serif; text-align: right;">
	<a href="<?php echo $player->profileurl; ?>"><img src="<?php echo $player->avatarmedium; ?>" style="width: 60px; height: 60px; border: solid 4px <?php echo $steam_color; ?>; border-radius: 5px; margin-left: 10px; background-image: url('<?php echo $player->avatar; ?>'); background-size: cover;" /></a>
	<?php echo $player->personaname; ?><span class="font-size:2em; font-family: TF2 Build;">&nbsp;&nbsp;<?php echo ($i+1); ?></span>
	</div>
	<?php
}

?>
<br />
<a class="button" href="/">Return</a>
</div>

<?php

include 'footer.php';