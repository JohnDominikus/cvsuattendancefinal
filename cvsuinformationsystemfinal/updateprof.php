<?php
include 'connection/config.php';
session_start();
$user_id = $_SESSION['user_id'];

$message = [];

// Retrieve user data from the database
$select_query = $conn->prepare("SELECT * FROM `user_form` WHERE id = ?");
$select_query->execute([$user_id]);
$fetch = $select_query->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['update_profile'])){
   $update_name = $_POST['update_name'];
   $update_email = $_POST['update_email'];

   // Update name and email if provided
   if (!empty($update_name) && !empty($update_email)) {
      $update_query = $conn->prepare("UPDATE `user_form` SET first_name = ?, email = ? WHERE id = ?");
      $update_query->execute([$update_name, $update_email, $user_id]);
      $message[] = 'Name and Email updated successfully!';
   }

   // Check if password fields are not empty and match
   if(!empty($_POST['update_pass']) && !empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])){
      $old_pass = $_POST['old_pass'];
      $update_pass = $_POST['update_pass'];
      $new_pass = $_POST['new_pass'];
      $confirm_pass = $_POST['confirm_pass'];

      if($old_pass != $fetch['password']){
         $message[] = 'Old password not matched!';
      } elseif($new_pass != $confirm_pass){
         $message[] = 'Confirm password not matched!';
      } else {
         // Update password if old password matches and new passwords match
         $update_pass_query = $conn->prepare("UPDATE `user_form` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$confirm_pass, $user_id]);
         $message[] = 'Password updated successfully!';
      }
   }

   // Check if image file is uploaded
   if(!empty($_FILES['update_image']['name'])){
      $update_image = $_FILES['update_image']['name'];
      $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
      $update_image_folder = 'uploaded_img/'.$update_image;

      // Move uploaded image to folder
      move_uploaded_file($update_image_tmp_name, $update_image_folder);

      // Update image path in the database
      $update_image_query = $conn->prepare("UPDATE `user_form` SET image = ? WHERE id = ?");
      $update_image_query->execute([$update_image, $user_id]);
      $message[] = 'Image updated successfully!';
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

   <!-- Bootstrap CSS -->
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
   
   <style>
      /* Additional CSS styles here */
   </style>
</head>
<body>
   
<div class="update-profile">

   <form action="" method="post" enctype="multipart/form-data">
      <?php
      // Display user profile image
      if(empty($fetch['image'])){
         echo '<img src="images/default-avatar.png">';
      } else {
         echo '<img src="uploaded_img/'.$fetch['image'].'">';
      }

      // Display messages, if any
      if(isset($message)){
         foreach($message as $msg){
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>
      <div class="flex">
         <div class="inputBox">
            <span>Username:</span>
            <input type="text" name="update_name" value="<?php echo $fetch['first_name']; ?>" class="box">
            <span>Your Email:</span>
            <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            <span>Update Your Picture:</span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
            <span>Old Password:</span>
            <input type="password" name="update_pass" placeholder="Enter Previous Password" class="box">
            <span>New Password:</span>
            <input type="password" name="new_pass" placeholder="Enter New Password" class="box">
            <span>Confirm Password:</span>
            <input type="password" name="confirm_pass" placeholder="Confirm New Password" class="box">
         </div>
      </div>
      <input type="submit" value="Update Profile" name="update_profile" class="btn">
      <a href="home.php" class="delete-btn">Go Back</a>
   </form>

</div>

</body>
</html>
