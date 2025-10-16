<?php 
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php 
         if (isset($_POST["submit"])) {
            $fullName = $_POST['fullname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordRepeat = $_POST['repeat_password'];

            $passwordhash = password_hash($password, PASSWORD_DEFAULT);
            $errors = array();

            if(empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
                array_push($errors, "Todos os campos são requeridos!");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                array_push($errors, "Email não é válido!");
            }
            if (strlen($password) < 8){
                array_push($errors, "Senha deve conter mais de 8 caracteres");
            }
            if ($password !== $passwordRepeat){
                array_push($errors, "As senhas não conferem!");
            }

            require_once "database.php";
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);
            $rowCount = mysqli_num_rows($result);
            if($rowCount>0) {
                array_push($errors, "Email já registrado!");
            }

            if(count($errors)>0){
                foreach ($errors as $error){
                    echo "<div class='alert alert-danger'>$error</div>";
                } 
            } else {
                $sql = "INSERT INTO users (full_name,email,password) VALUES( ?, ?, ?)";
                //$fullName,$email,$password
                $stmt = mysqli_stmt_init($conn);
                $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                if($prepareStmt) {
                    mysqli_stmt_bind_param($stmt,"sss", $fullName, $email, $passwordhash);
                    mysqli_stmt_execute($stmt);
                    echo "<div class='alert alert success'>Você foi registrado com sucesso!</div>";
                } else{
                    die("Algo deu errado!");
                }
            }
         }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:" id="">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email:" id="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:" id="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:" id="">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" name="submit" value="submit">
            </div>
        </form>
    </div>
    <div>
          <p>Already registred <a href="login.php">Login Here</a></p>  
        </div>
</body>
</html>