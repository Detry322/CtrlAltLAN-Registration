<?php
require_once 'meekrodb.php';
DB::$user = 'registrations';
DB::$password = 'pztKBbrFyZf6zvRr';
DB::$dbName = 'registrations';
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
session_start();

function removeFromWaitlist($location, $addtoregular = false)
{
    $users = DB::query("SELECT * FROM registrations WHERE state < 0");
    if (DB::count() < 1)
        return;

    $user = DB::queryFirstRow("SELECT * FROM registrations WHERE state=%i",-1*($location+1));

    if ($user == null)
        return;

    DB::delete('registrations','state=%i',intval($user['state']));

    $orderedUsers = array();

    foreach ($users as $person)
    {
        $orderedUsers[intval($person['state'])*-1 - 1] = $person;
    }

    $secondOrderedUsers = array();

    for ($i = 0; $i < count($orderedUsers); $i++)
    {
        if ($i != $location)
            array_push($secondOrderedUsers, $orderedUsers[$i]);
    }

    for ($i = 0; $i < count($secondOrderedUsers); $i++)
    {
        DB::update('registrations', array(
                'state' => (-1*($i + 1))
            ),'steamid=%s',$secondOrderedUsers[$i]['steamid']);
    }


    if ($addtoregular)
        DB::insert('registrations', array(
                'steamid' => $user['steamid'],
                'secret' => $user['secret']
            ));
}

if (!isset($_SESSION['id']))
{
	header("Location: /");
	die();
}

$user = DB::queryFirstRow("SELECT * FROM registrations WHERE steamid=%s",$_SESSION['id']);

$person_to_remove = $_SESSION['id'];

if ($user['id'] == 1 && isset($_GET['steamid']))
{
    $person_to_remove = $_GET['steamid'];
    $user = DB::queryFirstRow("SELECT * FROM registrations WHERE steamid=%s",$person_to_remove);
}

if ($user == null)
{
    header("Location: /");
    die();
}

if  ($user['state'] >= 0)
{
    DB::delete('registrations','steamid=%s',$person_to_remove);
    removeFromWaitlist(0,true);
} else {
    removeFromWaitlist((-1*intval($user['state'])) - 1);
}

header("Location: /");