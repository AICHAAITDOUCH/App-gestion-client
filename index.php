<?php
session_start();
require 'db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_image = $_SESSION['user_image'];

$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Récupérer les clients ajoutés par cet utilisateur avec leur ID utilisateur
    $sql = "SELECT id, name, email, contact, user_id FROM tabclient WHERE user_id = :user_id";
    if ($search) {
        $sql .= " AND name LIKE :search";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    if ($search) {
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$conn = null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mes Clients</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
/* Ajoutez ici le style copié de index.php */
@import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap");

:root {
    --primary-color: #f1faff;
    --text-dark: #030712;
    --text-light: #6b7280;
    --extra-light: #fbfbfb;
    --white: #ffffff;
    --max-width: 1200px;
}

* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

.section__container {
    max-width: var(--max-width);
    margin: auto;
    padding: 5rem 1rem;
}

.section__title {
    padding-bottom: 0.5rem;
    margin-bottom: 4rem;
    text-align: center;
    font-size: 2rem;
    font-weight: 600;
    color: var(--text-dark);
    position: relative;
}

.section__title::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    height: 3px;
    width: 75px;
    background-color: var(--text-dark);
}

.btn {
    padding: 0.75rem 2rem;
    font-size: 0.8rem;
    outline: none;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

a {
    text-decoration: none;
}

img {
    width: 100%;
    display: block;
}

.header__bar {
    padding: 0.5rem;
    font-size: 0.8rem;
    text-align: center;
    background-color: var(--text-dark);
    color: var(--white);
}

.nav__container {
    padding: 2rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nav__logo {
    font-size: 1.5rem;
    font-weight: 300;
    color: var(--text-dark);
}

.nav__logo img {
    width: 50%;
}

.nav__links {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.link a {
    padding: 0 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-light);
    transition: 0.3s;
}

.link a:hover {
    color: var(--text-dark);
}

.nav__icons {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav__icons .profile-image {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.nav__icons span {
    font-size: 1.25rem;
    cursor: pointer;
}

.nav__icons #searchForm {
    display: flex;
    flex-direction: row;
    gap: 1px;
}

.nav__icons #searchInput {
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: rgb(228, 226, 226);
    background-position: 10px 10px;
}

.nav__icons #searchForm button {
    border: none;
    padding: 5px;
    cursor: pointer;
}

.container {
    width: 95%;
    background: #fff;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    margin: 20px;
}

.button-container {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.btn {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

#modifier {
    background: #9ADE7B;
}

#supprimer {
    background: #DA1212;
}

.import-btn {
    background-color: #007bff;
    color: #fff;
    margin-right: 10px;
}

.ajouter-btn {
    background-color: red;
    color: #fff;
    margin-right: 10px;
}

.mes-btn {
    background-color: #A67B5B;
    color: #fff;
    margin-right: 10px;
}

.import-btn:hover {
    background-color: #0056b3;
}

.export-btn {
    background-color: #28a745;
    color: #fff;
}

.export-btn:hover {
    background-color: #218838;
}

.btn i {
    margin-right: 5px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.data-table thead {
    background-color: #007bff;
    color: #fff;
}

.data-table th, .data-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.data-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.modifier-supprimer {
    display: flex;
    gap: 5px;
}

#searchForm {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

#searchInput {
    padding: 8px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

#searchForm button {
    padding: 8px 16px;
    font-size: 16px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

#searchForm button:hover {
    background-color: #0056b3;
}

.logout-btn {
    background-color: #ECB176;
    color: #fff;
    border-radius: 4px;
    padding: 8px 16px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.logout-btn:hover {
    background-color: #c0392b;
}

.nav__icons .profile-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

#importFormContainer label {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
    margin-right: 10px;
}

#importFormContainer label:hover {
    background-color: #0056b3;
}

#importFile {
    display: none;
}

.hidden-submit-btn {
    display: none;
}

</style>
</head>
<body>
<nav class="section__container nav__container">
    <div class="nav__icons">
        <img src="uploads/A.png" alt="Image" class="profile-image">
    </div>
    <form id="searchForm" method="get" action="">
        <input type="text" id="searchInput" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
    <div class="nav__icons">
        <img src="<?php echo $user_image; ?>" alt="Profile Image" class="profile-image">
        <span><?php echo $user_name; ?></span>
        <form method="post" action="logout.php" style="display: inline;">
            <button type="submit" class="btn logout-btn"> <i class="fa fa-sign-out"></i> Logout</button>
        </form>
    </div>
</nav>
<div class="container">
    <div class="button-container">
        <button class="btn ajouter-btn" onclick="window.location.href='ajouter.php'">
            <i class="fa fa-plus-circle"></i> Ajouter
        </button>
        <div id="importFormContainer">
            <form action="importer.php" method="post" enctype="multipart/form-data">
                <label for="importFile" class="btn import-btn"><i class="fas fa-file-import"></i> Importer</label>
                <input type="file" id="importFile" name="importFile" accept=".csv, .xlsx" required>
                <button type="submit" class="hidden-submit-btn">Submit</button>
            </form>
        </div>
        <button class="btn export-btn" onclick="window.location.href='export.php'">
            <i class="fas fa-file-export"></i> Export
        </button>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['id']); ?></td>
                    <td><?php echo htmlspecialchars($client['name']); ?></td>
                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                    <td><?php echo htmlspecialchars($client['contact']); ?></td>
                    <td>
                        <div class="modifier-supprimer">
                            <button id="modifier" class="btn modifier-btn" onclick="window.location.href='modifier.php?id=<?php echo $client['id']; ?>'">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button id="supprimer" class="btn supprimer-btn" onclick="window.location.href='supprimer.php?id=<?php echo $client['id']; ?>'">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    document.getElementById('importFile').addEventListener('change', function() {
        document.querySelector('.hidden-submit-btn').click();
    });
</script>
</body>
</html>