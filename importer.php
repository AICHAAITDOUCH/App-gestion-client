<?php
session_start();
require 'db_conn.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['importFile'])) {
    $fileName = $_FILES['importFile']['name'];
    $fileTmpName = $_FILES['importFile']['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    if ($fileExtension == 'csv') {
        $reader = new Csv();
    } elseif ($fileExtension == 'xlsx') {
        $reader = new Xlsx();
    } else {
        echo "Format de fichier non supporté.";
        exit();
    }

    $spreadsheet = $reader->load($fileTmpName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();

    $conn->beginTransaction();
    try {
        // Récupérer l'ID de l'utilisateur connecté
        $user_id = $_SESSION['user_id'];

        foreach ($sheetData as $row) {
            if ($row === $sheetData[0]) continue;

            $name = $row[0];
            $email = $row[1];
            $contact = $row[2];

            // Utiliser l'ID de l'utilisateur pour l'insertion
            $sql = "INSERT INTO tabclient (name, email, contact, user_id) VALUES (:name, :email, :contact, :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
        $conn->commit();
        header("Location: index.php");
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur: " . $e->getMessage();
    }
} else {
    echo "Aucun fichier sélectionné.";
}
?>
