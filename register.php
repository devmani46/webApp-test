<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header('location: home.php');
    exit();
}

if (isset($_POST['submit'])) {
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_folder = 'uploaded_img/' . $image;

    $stmt = $conn->prepare("SELECT * FROM `user_form` WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $message[] = 'Email already registered!';
    } elseif ($password !== $cpassword) {
        $message[] = 'Confirm password does not match!';
    } elseif ($image_size > 2000000) {
        $message[] = 'Image size too large! Max 2MB allowed.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $insert_query = "INSERT INTO `user_form` (Fname, Lname, email, number, password, image) 
                         VALUES (:Fname, :Lname, :email, :number, :password, :image)";
        
        $stmt = $conn->prepare($insert_query);
        
        $stmt->bindParam(':Fname', $Fname);
        $stmt->bindParam(':Lname', $Lname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':number', $number);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':image', $image);

        if ($stmt->execute()) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'Registered successfully! You can now login.';
        } else {
            $message[] = 'Registration failed! Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Register</h3>
      
      <?php
      if (isset($message)) {
         foreach ($message as $msg) {
            echo '<div class="message">' . $msg . '</div>';
         }
      }
      ?>

      <input type="text" name="Fname" required placeholder="Enter First Name" class="box">
      <input type="text" name="Lname" required placeholder="Enter Last Name" class="box">
      <input type="email" name="email" required placeholder="Enter Email" class="box">
      <input type="text" name="number" required placeholder="Enter Phone number" class="box">
      <input type="password" name="password" required placeholder="Enter Password" class="box">
      <input type="password" name="cpassword" required placeholder="Confirm Password" class="box">
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
      
      <input type="submit" name="submit" value="Register Now" class="btn">
      <p>Already have an account? <a href="login.php">Login Now</a></p>
   </form>
</div>

</body>
</html>
