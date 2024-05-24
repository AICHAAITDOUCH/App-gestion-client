<?php
session_start();
require 'db_conn.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, name, email, contact FROM tabclient WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = "clients_" . date('Ymd') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

fputcsv($output, array('ID', 'Name', 'Email', 'Contact'));

foreach ($clients as $client) {
    fputcsv($output, $client);
}

fclose($output);
exit();
?>
