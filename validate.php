<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
require_once 'meekrodb.php';
require_once 'http_response_code.php';
DB::$user = 'registrations';
DB::$password = 'pztKBbrFyZf6zvRr';
DB::$dbName = 'registrations';

$API = "98B0770824D696E9DCA0CA3DCF8AC83D";

$success = false;
$error = "";
$steaminfo = array();

if (!isset($_GET['info']))
{
	$error = "Bad Request";
}
else
{
	$string = $_GET['info'];
	$stuff = explode(":",$string);
	if (count($stuff) != 2)
	{
		$error = "Bad Request";
	}
	else
	{
		$steamid = $stuff[0];
		$secret = $stuff[1];
		$user = DB::queryFirstRow("SELECT * FROM registrations WHERE steamid=%s",$steamid);

		if ($user == null)
		{
			$error = "User SteamID Not Found";
		}
		else
		{
			if ($user['secret'] != $secret)
			{
				$error = "Invalid Ticket";
			}
			else
			{
				$success = true;
				DB::update('registrations', array(
						'state' => 1
					),'steamid=%s',$steamid);

				$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$API."&steamids=".$steamid;
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				curl_close($ch);
				$data = json_decode($data);
				$data = $data->response->players[0];

				$types = array("Undecided", "Windows Desktop", "Windows Laptop", "Mac Desktop", "Mac Laptop", "Linux Desktop", "Linux Laptop", "Dual-boot Desktop", "Dual-boot Laptop", "Nintendo Console", "Microsoft Console", "Sony Console");

				$steaminfo['avatar'] = $data->avatar;
				$steaminfo['name'] = $data->personaname;
				$steaminfo['computer'] = $types[$user['computer']];
		    }
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo ($success ? "Success!" : "Error"); ?></title>
	</head>
	<body style="width: auto; font-size: 2em; background-color: <?php echo ($success ? "#00FF00" : "#FF0000"); ?>">
		<?php
		if ($success)
		{
			?>

				<img src="<?php echo $steaminfo['avatar']; ?>" /><br /><br />
			<?php
			echo $steaminfo['name']."<br /><br />";
			echo $steaminfo['computer'];
		} else {
			echo $error."<br /><br />";
			echo $_SERVER['QUERY_STRING'];
		}
		?>
	</body>
</html>