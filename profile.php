<?php
include 'config.php';

session_start();
$id = $_SESSION['user_id'];
$select_users = $conn->query("SELECT * FROM users_info WHERE Id = '$id'") or die('Query failed');

if (mysqli_num_rows($select_users) > 0) {
  $row = mysqli_fetch_assoc($select_users);
  $existing_fname = $row['name'];
  $existing_lname = $row['surname'];
  $existing_email = $row['email'];
  $existing_password = $row['password'];
  $existing_file = $row['profile_picture'];
} else {
  $message[] = 'No user data selected.';
}

if(isset($_POST['submit'])) {
  $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
  $user_type = $_POST['user_type'];

  if (isset($_FILES['profile_picture'])) {
    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    
    $target_directory = 'C:/xampp/htdocs/book-store-2/images/';
    
    $target_path = $target_directory . $file_name;
    move_uploaded_file($file_tmp, $target_path);
  }

  $file_path = 'C:/xampp/htdocs/book-store-2/images/' . $existing_file;

  if (file_exists($file_path)){
      unlink($file_path);
  }

  if($password != $cpassword){
    $message[] = 'Confirm password not matched.';
  } else {
    $update_query = "UPDATE users_info SET name = '$fname', surname = '$lname', email = '$email', password = '$password', user_type = '$user_type', profile_picture = '$file_name' WHERE Id = $id";
    if (mysqli_query($conn, $update_query)) {
      $existing_fname = $fname;
      $existing_lname = $lname;
      $existing_email = $email;
      $existing_password = $password;

      $message[] = 'Profile Updated Successfully';
    } else {
      $message[] = 'Query failed: ' . mysqli_error($conn);
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Profile</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
    }

    /* Message */
    .message {
      position: sticky;
      top: 0;
      margin: 0 auto;
      width: 61%;
      background-color: #fff;
      padding: 6px 9px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 100;
      gap: 0px;
      border: 2px solid rgb(68, 203, 236);
      border-top-right-radius: 8px;
      border-bottom-left-radius: 8px;
    }
    .message span {
      font-size: 22px;
      color: rgb(240, 18, 18);
      font-weight: 400;
    }
    .message i {
      cursor: pointer;
      color: rgb(3, 227, 235);
      font-size: 15px;
    }

    form {
      width: 75%;
      /* height:75vh; */
      margin: 10% auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #f2f2f2;
      padding: 20px;
      border-radius: 5px;
    }

    label {
      margin-bottom: 5px;
    }

    input[type="text"],
    input[type="email"],
    input[type="file"],
    input[type="password"] {
      width: 50%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <?php
    if(isset($message)){
      foreach($message as $message){
        echo '
        <div class="message" id= "messages"><span>'.$message.'</span>
        </div>
        ';
      }
    }
  ?>
  <form action="profile.php" method="post" enctype="multipart/form-data">
    <label for="name">First Name</label>
    <input type="text" name="fname" id="fname" value="<?php echo $existing_fname; ?>" required>
    <br>
    <label for="name">Last Name</label>
    <input type="text" name="lname" id="lname" value="<?php echo $existing_lname; ?>" required>
    <br>
    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?php echo $existing_email; ?>" required>
    <br>
    <label for="password">Password</label>
    <input type="password" name="password" id="password" value="<?php echo $existing_password; ?>">
    <br>
    <label for="password">Confirm Password</label>
    <input type="password" name="cpassword" id="cpasword" value="<?php echo $existing_password; ?>">
    <br>
    <select style="display:none" name="user_type" id="user-type" required class="text_field" >
      <option value="User" selected>User</option>
      <option value="Admin">Admin</option>
    </select>
    <label for="profile-picture">Profile Picture</label>
    <input type="file" name="profile_picture" id="profile_picture" accept="images/*" >
    <br>
    <input type="submit" name="submit" value="Update Profile">
  </form>
</body>
</html>