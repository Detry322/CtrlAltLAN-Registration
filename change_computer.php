<?php

require_once 'meekrodb.php';
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

if (!isset($_POST['computer']))
{
    DB::update('registrations', array(
        'computer' => 0
        ), 'steamid=%s', $_SESSION['id']);
    header("Location: /");
    die();
}

$computer = intval($_POST['computer']);
$computer = ($computer < 1) ? 1 : $computer;
$computer = ($computer > 11) ? 11 : $computer;
DB::update('registrations', array(
        'computer' => $computer
        ), 'steamid=%s', $_SESSION['id']);

header("Location: /?change_success");