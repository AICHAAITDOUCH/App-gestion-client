<?php
require 'db_conn.php';

session_start();
if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_image'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

$sql = "DELETE FROM tabclient WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header("Location: index.php");
    exit();
} else {
    echo "Erreur: " . $stmt->errorInfo()[2];
}
?>
