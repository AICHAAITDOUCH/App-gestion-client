<?php
session_start();
require 'db_conn.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);
    $profile_image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $profile_image = $target_file;
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    }

    try {
        $sql = "SELECT * FROM Inscription WHERE username = :username AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            
            if ($profile_image) {
                $updateSql = "UPDATE Inscription SET profile_image = :profile_image WHERE id = :id";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bindParam(':profile_image', $profile_image);
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();
                $_SESSION['user_image'] = $profile_image;
            } else {
                $_SESSION['user_image'] = $user['profile_image'];
            }

            if ($remember_me) {
                setcookie('username', $username, time() + (86400 * 30), "/");
                setcookie('password', $password, time() + (86400 * 30), "/");
            } else {
                if (isset($_COOKIE['username'])) {
                    setcookie('username', '', time() - 3600, "/");
                }
                if (isset($_COOKIE['password'])) {
                    setcookie('password', '', time() - 3600, "/");
                }
            }

            header("Location: index.php");
            exit();
        } else {
            $message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

$conn = null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="wrapper">
<header>Login</header>

<form method="POST" action="login.php" enctype="multipart/form-data">
    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="field name">
        <div class="input-areaN">
            <i class="fas fa-user-circle"></i>
            <input type="text" id="username" placeholder="Enter Username" name="username" value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; ?>" required>
        </div>
    </div>
    <div class="field password">
        <div class="input-area">
            <i class="icon fas fa-lock"></i>
            <input type="password" id="password" placeholder="Password" name="password" value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>" required>
        </div>
    </div>
    <div class="field file">
        <div class="input-area">
            <input type="file" id="image" name="image">
            <label for="image" class="file-label">Choose Profile Photo</label>
        </div>
    </div>
    <div class="field remember-me">
        <input type="checkbox" id="remember_me" name="remember_me" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
        <label for="remember_me">Remember Me</label>
    </div>
    <input type="submit" value="Login" name="submit">
</form>
</div>
</body>
</html>





<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Roboto', sans-serif;
}

body {
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f2f5;
}

.wrapper {
    width: 400px;
    padding: 40px 30px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.wrapper header {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
}

.wrapper form {
    width: 100%;
}

form .field {
    width: 100%;
    margin-bottom: 20px;
}

form .field .input-area {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
}

form input[type="text"],
form input[type="email"],
form input[type="password"],
form input[type="file"] {
    width: 100%;
    padding: 15px 20px 15px 45px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
    outline: none;
    transition: border 0.3s ease;
}

form input[type="text"]:focus,
form input[type="email"]:focus,
form input[type="password"]:focus,
form input[type="file"]:focus {
    border-color: #007bff;
}

form .field .icon {
    position: absolute;
    left: 15px;
    font-size: 18px;
    color: #aaa;
}

.field.file {
    display: flex;
    align-items: center;
    justify-content: center;
}

.field.file input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.field.file .file-label {
    width: 100%;
    padding: 15px;
    text-align: center;
    background: #007bff;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.field.file .file-label:hover {
    background: #0056b3;
}

form input[type="submit"] {
    width: 100%;
    padding: 15px;
    border: none;
    background: #007bff;
    color: #fff;
    font-size: 18px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

form input[type="submit"]:hover {
    background: #0056b3;
}

.input-areaN i {
    position: absolute;
   margin:15px;

    font-size: 18px;
    color: #aaa;
}

.field.remember-me {
    display: flex;
    align-items: center;
    justify-content: center;
}

.field.remember-me input {
    margin-right: 10px;
}

</style>
