<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
$API = "98B0770824D696E9DCA0CA3DCF8AC83D";
session_start();
require 'openid.php';
require_once 'meekrodb.php';
DB::$user = 'registrations';
DB::$password = 'pztKBbrFyZf6zvRr';
DB::$dbName = 'registrations';

function test_app_id($appid, $array)
{
	foreach ($array as $game)
	{
		if ($game->appid == $appid)
		{
			return "<span class=\"good\">&#x2713;&nbsp;</span>";
		}
	}
	return "<span class=\"bad\">&#x2716;&nbsp;</span>";
}

try {
    # Change 'localhost' to your domain name.
   $openid = new LightOpenID('register.ctrlaltlan.org');
    if(!$openid->mode || ($openid->mode == 'cancel')) {
        if(isset($_GET['login'])) {
            $openid->identity = 'http://steamcommunity.com/openid';
            header('Location: ' . $openid->authUrl());
        }
    } else {
        if($openid->validate()) {
                $id = $openid->identity;
                // identity is something like: http://steamcommunity.com/openid/id/76561197994761333
                // we only care about the unique account ID at the end of the URL.
                $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                preg_match($ptn, $id, $matches);
                $_SESSION['id'] = $matches[1];
                $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$API."&steamids=".$_SESSION['id'];
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($data);
                $_SESSION['userdata'] = $data->response->players[0]; 
                header("Location: /");
        }
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}

include 'header.php';

if (isset($_GET['register_success']))
{
	?>
		
		<div class="success box">You have successfully registered for Jack Serrino's LAN Extravaganza v7</div> 

	<?php
}
if (isset($_GET['change_success']))
{
	?>
		
		<div class="success box">You have successfully changed your computer type</div> 

	<?php
}
if (!isset($_SESSION['id']))
{
	?>
		<div id="loginbox" class="box">
			<span class="title">Please log in</span><br />
			<span class="subtext" style="font-size:0.7em;">In order to register for the LAN party, you must log in with Steam</span><br /><br />
			<form action="/?login" method="post"> <input type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_large_border.png"></form>
		</div>
	<?php
} else {
	$user = DB::queryFirstRow("SELECT * FROM registrations WHERE steamid=%s", $_SESSION['id']);
	DB::query("SELECT * FROM registrations WHERE state >= 0");
	$count = DB::count();
	if ($user == null || $user['state'] < 0)
	{
		?>
			<div class="generic box">
				<span class="title">Jack Serrino's LAN Extravaganza v7</span><br />
				<span class="subtext" style="font-size:0.7em;">December 22nd and 23rd, 5:00 PM to 11:59 AM<br />130 Beach Rd, Glencoe, IL<br /><br />You haven't registered yet for Jack Serrino's LAN Extravaganza v7. To register, please click the button below.</span><br /><br />
				
				<?php 
				if ($count < 33)
				{
				?>
				<a class="button" href="/register.php">Register</a><br /><br />
				<?php
				} else {
					DB::query("SELECT * FROM registrations WHERE state < 0");
					$c2 = DB::count();
				?><span class="subtext" style="font-size:0.7em; color:red;">Sadly, tickets have run out :(</span><br /><br />
				<?php
					if ($user != null && $user['state'] < 0)
					{
						?>
							<span class="subtext" style="font-size:0.7em;">Thank you for signing up for the waitlist.<br />You are number <?php echo -1*$user['state']; ?> in line, and will be notified if there is an opening.</span><br /><br />
							<span class="subtext" style="font-size:0.7em;">If you wish, you may print your ticket before you are added to the attendee list. Please keep in mind that your ticket will not work if you are still on the waitlist when coming to the party.</span><br /><br /><a class="button" href="/print.php">Print</a><br /><br />
						<?php
					}
					else
					{
						?>
							<span class="subtext" style="font-size:0.7em; color:red;">Click below to sign up for the waitlist.</span><br /><br />
							<a class="button" href="/register.php">Join waitlist</a><br /><br />
						<?php
					}
				}
				?>
				<span class="subtext" style="font-size:0.7em;"><?php echo ($count == 1) ? ($count." person has") : ($count." people have"); ?> registered.</span><br /><br />
				<span class="subtext" style="font-size:0.7em;"><?php echo ((33-$count) == 1) ? ((33-$count)." ticket remains") : ((33-$count)." tickets remain"); ?></span>
				<br /><br />
				<a class="button" href="/attendees.php">See Who's Going</a><br /><br />
				<span class="title">OR</span><br /><br />
				<a class="button" href="/waitlist.php">View the Waitlist</a>
				
			</div>
		<?php
	} else {
				$url = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=".$API."&steamid=".$_SESSION['id'];
			    $ch = curl_init($url);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			    $data = curl_exec($ch);
			    curl_close($ch);
  	  			$data = json_decode($data);
  	  			$response = $data->response->games;

		?>
			<div class="generic box">
				<span class="title">Print your ticket: </span><a class="button" href="/print.php">Print</a><br /><br />
				<span class="subtext" style="font-size:0.7em;">Please select your computer type if you haven't done so already.</span>
			</div>
			<div class="generic box">
				<span class="title">Jack Serrino's LAN Extravaganza v7</span><br />
				<span class="subtext" style="font-size:0.7em;">December 22nd and 23rd, 5:00 PM to 11:59 AM<br />130 Beach Rd, Glencoe, IL<br /><br /><?php echo ($count == 1) ? ($count." person has") : ($count." people have"); ?> registered.</span><br />
				<span class="subtext" style="font-size:0.7em;"><?php echo ((33-$count) == 1) ? ((33-$count)." ticket remains") : ((33-$count)." tickets remain"); ?></span>
				<br /><br />
				<a class="button" href="/attendees.php">See Who's Going</a>
			</div>
			<div class="generic box">
				<span class="title">Computer Type</span><br />
				<span class="subtext" style="font-size:0.7em;">Please select the type of computer you plan to bring.</span><br />
				<?php 
				$types = array("Windows Desktop", "Windows Laptop", "Mac Desktop", "Mac Laptop", "Linux Desktop", "Linux Laptop", "Dual-boot Desktop", "Dual-boot Laptop", "Nintendo Console", "Microsoft Console", "Sony Console");
				if ($user['computer'] == 0)
				{
				?>
				<form action="/change_computer.php" method="POST">
					<select name="computer">
						<option value="1">Windows Desktop</option>
						<option value="3">Mac Desktop</option>
						<option value="5">Linux Desktop</option>
						<option value="7">Dual-boot Desktop</option>
						<option value="2">Windows Laptop</option>
						<option value="4">Mac Laptop</option>
						<option value="6">Linux Laptop</option>
						<option value="8">Dual-boot Laptop</option>
						<option value="9">Nintendo Console</option>
						<option value="10">Microsoft Console</option>
						<option value="11">Sony Console</option>
					</select>
					<input type="submit" class="button" value="Submit" />
				</form>
				<?php
				} else {
				?>
				<br /><span class="subtext">You have selected: <?php echo $types[$user['computer']-1]; ?></span><br /><br />
				<a class="button" href="/change_computer.php">Change</a>
				<?php
				}
				?>
			</div>
			<div class="generic box">
				<span class="title">Game Checklist</span>
				<br />
				<span class="subtext" style="font-size:0.7em;">Click on the game to go to the Steam store page.</span><br />
				<span class="subtext" style="font-size:0.7em;"><span class="good">&#x2713;</span> = owned<br /><span class="bad">&#x2716;</span> = not owned<br /><span class="meh" style="font-family: serif">~</span> = could not be determined<br /></span>
				<div class="checklist-box"><!-- Unreal Tournament 3 -->
					<a href="http://steamcommunity.com/app/13210"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/13210/ba0a5c14642ab3337bf09d1d3df5e076a771ee32.jpg" style="width:184px;height:69px; border: solid 4px rgb(137,137,137);" /></a> <?php echo test_app_id(13210,$response); ?>
				</div>
				<div class="checklist-box"><!-- Call of Duty 4 -->
					<a href="http://steamcommunity.com/app/7940"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/7940/a4bd2ef1a993631ca1290a79bd0dd090349ff3e2.jpg" style="width:184px;height:69px; border: solid 4px rgb(137,137,137);" /></a> <?php echo test_app_id(7940,$response); ?>
				</div>
				<div class="checklist-box"><!-- Left 4 Dead 2 -->
					<a href="http://steamcommunity.com/app/550"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/550/205863cc21e751a576d6fff851984b3170684142.jpg" style="width:184px;height:69px; border: solid 4px rgb(137,137,137);" /></a> <?php echo test_app_id(550,$response); ?>
				</div>
				<div class="checklist-box"><!-- Counter-Strike: Source -->
					<a href="http://steamcommunity.com/app/240"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/240/ee97d0dbf3e5d5d59e69dc20b98ed9dc8cad5283.jpg" style="width:184px;height:69px; border: solid 4px rgb(137,137,137);" /></a> <?php echo test_app_id(240,$response); ?>
				</div>
				<div class="checklist-box"><!-- Team Fortress 2 -->
					<a href="http://steamcommunity.com/app/440"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/440/07385eb55b5ba974aebbe74d3c99626bda7920b8.jpg" style="width:184px;height:69px; border: solid 4px rgb(137,137,137);" /></a> <span class="meh">~&nbsp;</span>
				</div>
				<div class="checklist-box"><!-- Warcraft 3 -->
					<a href="http://fast2play.com/warcraft-3-reign-of-chaos-the-frozne-throne-1.html"><img src="/oblog3.jpg" style="width:184px;height:69px; border: solid 4px rgb(137,137,137);" /></a> <span class="meh">~&nbsp;</span>
				</div>
			</div>
		<?php
	}
}

include 'footer.php';

?>