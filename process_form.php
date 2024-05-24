<?php
session_start();
require 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifiez si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];

        $sql = "INSERT INTO tabclient (name, email, contact, user_id) VALUES (:name, :email, :contact, :user_id)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            header("location:index.php");
        } else {
            echo "Erreur: " . $stmt->errorInfo()[2];
        }
    } else {
        echo "Erreur: l'utilisateur n'est pas connecté.";
    }
}
?>
