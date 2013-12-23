<?php

require_once 'meekrodb.php';
DB::$user = 'registrations';
DB::$password = 'pztKBbrFyZf6zvRr';
DB::$dbName = 'registrations';

function randString($length, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
{
    $str = '';
    $count = strlen($charset);
    while ($length--) {
        $str .= $charset[mt_rand(0, $count-1)];
    }
    return $str;
}
session_start();

if (!isset($_SESSION['id']))
{
	header("Location: /");
	die();
}

$user = DB::queryFirstRow("SELECT * FROM registrations WHERE steamid=%s",$_SESSION['id']);

if ($user == null)
{
    DB::query("SELECT * FROM registrations WHERE state >= 0");
    if (DB::count() < 33)
    {
        DB::insert('registrations', array(
        	'steamid' => $_SESSION['id'],
        	'secret' => randString(72)
        	));
    } else {
        DB::query("SELECT * FROM registrations WHERE state < 0");
        DB::insert('registrations', array(
            'steamid' => $_SESSION['id'],
            'secret' => randString(72),
            'state' => (-1*(DB::count()+1))
            ));
    }
}

header("Location: /?register_success");