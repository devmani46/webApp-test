<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM `user_form` WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$fetch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fetch) {
    die('User not found.');
}

if (isset($_POST['update_profile'])) {
    $update_Fname = $_POST['update_Fname'];
    $update_Lname = $_POST['update_Lname'];
    $update_number = $_POST['update_number'];
    $update_email = $_POST['update_email'];

    $update_query = "UPDATE `user_form` SET 
                     Fname = :Fname, 
                     Lname = :Lname, 
                     number = :number, 
                     email = :email 
                     WHERE id = :user_id";

    $stmt = $conn->prepare($update_query);
    $stmt->execute([
        'Fname' => $update_Fname,
        'Lname' => $update_Lname,
        'number' => $update_number,
        'email' => $update_email,
        'user_id' => $user_id
    ]);

    if (!empty($_POST['old_pass']) && !empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])) {
        $old_pass = $_POST['old_pass']; 
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];

        $stmt = $conn->prepare("SELECT password FROM `user_form` WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $db_old_pass = $row['password'];

        if (!password_verify($old_pass, $db_old_pass)) {
            $message[] = 'Old password does not match!';
        } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'Confirm password does not match!';
        } else {
            $hashed_new_pass = password_hash($new_pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE `user_form` SET password = :password WHERE id = :user_id");
            $stmt->execute(['password' => $hashed_new_pass, 'user_id' => $user_id]);
            $message[] = 'Password updated successfully!';
        }
    }

    if (!empty($_FILES['update_image']['name'])) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_size = $_FILES['update_image']['size'];
        $image_folder = 'uploaded_img/' . $update_image;

        if ($update_image_size > 2000000) {
            $message[] = 'Image is too large! Max 2MB allowed.';
        } else {
            move_uploaded_file($update_image_tmp_name, $image_folder);
            $stmt = $conn->prepare("UPDATE `user_form` SET image = :image WHERE id = :user_id");
            $stmt->execute(['image' => $update_image, 'user_id' => $user_id]);
            $message[] = 'Image updated successfully!';
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
   <title>Update Profile</title>
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<div class="update-profile">
   <form action="" method="post" enctype="multipart/form-data">
      <?php
         if (empty($fetch['image'])) {
            echo '<img src="images/default-avatar.png">';
         } else {
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }

         if (isset($message)) {
            foreach ($message as $msg) {
               echo '<div class="message">'.$msg.'</div>';
            }
         }
      ?>

      <div class="flex">
        <div class="inputBox">
        <span>First name:</span>
        <input type="text" name="update_Fname" value="<?php echo htmlspecialchars($fetch['Fname']); ?>" class="box">
        <span>Last name:</span>
        <input type="text" name="update_Lname" value="<?php echo htmlspecialchars($fetch['Lname']); ?>" class="box">
        <span>number:</span>
        <input type="text" name="update_number" value="<?php echo isset($fetch['number']) ? htmlspecialchars($fetch['number']) : ''; ?>" class="box">
        <span>Email:</span>
        <input type="email" name="update_email" value="<?php echo htmlspecialchars($fetch['email']); ?>" class="box">
        </div>


         <div class="inputBox">
            <span>Old password:</span>
            <input type="password" name="old_pass" placeholder="Enter previous password" class="box">
            <span>New password:</span>
            <input type="password" name="new_pass" placeholder="Enter new password" class="box">
            <span>Confirm password:</span>
            <input type="password" name="confirm_pass" placeholder="Confirm new password" class="box">
            <span>Update your picture:</span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
         </div>
      </div>

      <input type="submit" value="Update Profile" name="update_profile" class="btn">
      <a href="home.php" class="delete-btn">Go Back</a>
   </form>
</div>

</body>

<?php
include 'footer.php';  

?>
</html>
