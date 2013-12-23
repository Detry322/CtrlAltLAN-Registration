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

if (!isset($_SESSION['id']))
{
	header('Location: /');
	die();
}

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

function test_app_id($appid, $array)
{
	foreach ($array as $game)
	{
		if ($game->appid == $appid)
		{
			return true;
		}
	}
	return false;
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

$UT3totals = 0;
$totals = array(0,0,0,0,0,0,0,0,0,0,0,0);

foreach ($array as $player)
{
	$totals[find_computer($result,$player->steamid)] += 1;
}

$worstcase = $count*2-$totals[2]-$totals[2]-$totals[4]-$totals[6]-$totals[8];
$bestcase = $worstcase-$totals[0];
$laptops = $totals[2]+$totals[4]+$totals[6]+$totals[8]; 
$desktops = $totals[1]+$totals[3]+$totals[5]+$totals[7];
$consoles = $totals[9]+$totals[10]+$totals[11];
$unknown = $totals[0];

$bestcaseamps = ($unknown+$laptops)*2.5+($desktops+$consoles)*1+$desktops*3.5+$consoles*1.5;
$worstcaseamps = ($laptops)*2.5+($desktops+$consoles+$unknown)*1+($desktops+$unknown)*3.5+$consoles*1.5;

?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="http://www.flotcharts.org/flot/jquery.flot.js"></script>
<script src="http://www.flotcharts.org/flot/jquery.flot.pie.js"></script>
<div class="generic box">
				<span class="title">Attendee Data</span><br />
				<span class="subtext" style="font-size:0.7em;"><?php echo ($count == 1) ? ($count." person has") : ($count." people have"); ?> registered.</span><br /><br />
				<span class="subtext" style="font-size:0.7em;">Worst case, we need <span class="title"><?php echo $worstcase; ?></span> total plugs.</span><br />
				<span class="subtext" style="font-size:0.7em;">Therefore, we need <span class="title"><?php echo $worstcase/6; ?></span> total power strips</span><br />
				<span class="subtext" style="font-size:0.7em;">and we need <span class="title"><?php echo $worstcase/12; ?></span> total wall sockets, if we are being dangerous.</span><br />
				<span class="subtext" style="font-size:0.7em;">If we are being safe, we need <span class="title"><?php echo $worstcaseamps ?></span> amps of electricity</span><br />
				<span class="subtext" style="font-size:0.7em;">and we need <span class="title"><?php echo $worstcaseamps/20 ?></span> 20 amp circuits.</span><br />
				<br />
				<span class="subtext" style="font-size:0.7em;">Best case, we need <span class="title"><?php echo $bestcase; ?></span> total plugs</span><br />
				<span class="subtext" style="font-size:0.7em;">Therefore, we need <span class="title"><?php echo $bestcase/6; ?></span> total power strips</span><br />
				<span class="subtext" style="font-size:0.7em;">and we need <span class="title"><?php echo $bestcase/12; ?></span> total wall sockets, if we are being dangerous</span><br />
				<span class="subtext" style="font-size:0.7em;">If we are being safe, we need <span class="title"><?php echo $bestcaseamps ?></span> amps of electricity</span><br />
				<span class="subtext" style="font-size:0.7em;">and we need <span class="title"><?php echo $bestcaseamps/20 ?></span> 20 amp circuits.</span><br />
<?php


?>
</div>
<div class="generic box">
	<span class="title">Fancy Charts</span><br /><br />
	<span class="subtext" style="font-size:0.7em;">Total distribution of different types</span><br />
	<div id="comptype" style="width:500px; height:500px;"></div><br />
	<span class="subtext" style="font-size:0.7em;">Distribution of Desktop, Laptops, Consoles, Unknown</span><br />
	<div id="comptype2" style="width:500px; height:500px;"></div><br />
	<span class="subtext" style="font-size:0.7em;">Distribution of Desktops, Laptops, Consoles</span><br />
	<div id="comptype3" style="width:500px; height:500px;"></div>
</div>

<?php

include 'footer.php';
?>
<script>
	$(function () {
		var placeholder = $("#comptype");
		var placeholder2 = $("#comptype2");
		var placeholder3 = $("#comptype3");
    	var data = [
    		<?php
    			for($i = 0; $i < count($totals)-1; $i++)
				{
					echo "{ label: '".$types[$i]."', data:".$totals[$i]."},";
				}
				echo "{ label: '".$types[count($totals)-1]."', data:".$totals[count($totals)-1]."}";
    		?>
    	];
    	var data2 = [
    		{ label: "Desktops", data:<?php echo $desktops; ?>},
    		{ label: "Laptops", data:<?php echo $laptops; ?>},
    		{ label: "Consoles", data:<?php echo $consoles; ?>},
    		{ label: "Unknown", data:<?php echo $unknown; ?>}
    	];
    	var data3 = [
    		{ label: "Desktops", data:<?php echo $desktops; ?>},
    		{ label: "Laptops", data:<?php echo $laptops; ?>},
    		{ label: "Consoles", data:<?php echo $consoles; ?>}
    	];
    	$.plot(placeholder, data, {
    		series: {
		        pie: {
		            show: true
		        }
    		},
    		legend: {
    			show: false,
    			labelFormatter: function(label, series) {
    					console.log(series);
    					return label + "<br />" + Math.round(series.percent * <?php echo $count; ?>/100) + " attendee(s)";
					}
    		}
		});
		$.plot(placeholder2, data2, {
    		series: {
		        pie: {
		            show: true
		        }
    		},
    		legend: {
    			show: false,
    			labelFormatter: function(label, series) {
    					console.log(series);
    					return label + "<br />" + Math.round(series.percent * <?php echo $count; ?>/100) + " attendee(s)";
					}
    		}
		});
		$.plot(placeholder3, data3, {
    		series: {
		        pie: {
		            show: true
		        }
    		},
    		legend: {
    			show: false,
    			labelFormatter: function(label, series) {
    					console.log(series);
    					return label + "<br />" + Math.round(series.percent * <?php echo $count-$unknown; ?>/100) + " attendee(s)";
					}
    		}
		});
    });
</script>