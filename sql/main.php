<?php

session_start();
require_once('connection.php');
$db = getDataBaseConnection();

if (!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch();


?>

<h1> MAIN PAGE </h1>

<ul>
    <a href="ticket.php"><li>Ticket</li></a>
    <a href="profile.php"><li>Profile</li></a>
</ul>