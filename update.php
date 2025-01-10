<html>
<body>
<?php
include('connection.php');

$id = $_GET['id'];
$sql = "SELECT * FROM login WHERE id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$name = $row['name'];
$email = $row['email'];
$mobile = $row['mobile'];
$date = $row['date'];
$gender = $row['gender'];
$hobby = $row['hobby'];
$password = $row['password'];
$filenames = $row['filenames']; 
$nameErr = $emailErr = $mobileErr = $dobErr = $genderErr = $hobbyErr = $passwordErr = "";
$imageList = json_decode($filenames, true);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $date = $_POST['date'];
    $gender = $_POST['gender'];
    $hobby = $_POST['hobby'];
    $NewPassword = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if (!empty($_FILES['file']['name'][0])) {
        foreach ($_FILES['file']['name'] as $key => $filename) {
            $path = $_FILES['file']['name'][$key];
            $newname = time() . "_" . $key;
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $newfileName = $newname . '.' . $ext;
            // echo '<pre>';
            // print_r($newfileName);
            // die();
            if (move_uploaded_file($_FILES['file']['tmp_name'][$key], 'upload/' . $newfileName)) {
                $imageList[] = $newfileName;
            } else {
                echo "Error in file upload.";
            }
        }
    }
    if (isset($_POST['delete_images'])) {
        $deleteImages = $_POST['delete_images']; 
        foreach ($deleteImages as $deleteImage) {
                if(!empty('unchecked' != $deleteImage )){     
            if (($key = array_search($deleteImage, $imageList)) !== false) {
                    if (file_exists('upload/'.$deleteImage)) {
                        unlink('upload/'.$deleteImage); 
                    }
                unset($imageList[$key]);
            }
        }
    }
}

    if (empty($name)) {
        $nameErr = "Name is required";
    } elseif (!preg_match('/^[a-zA-Z ]*$/', $name)) {
        $nameErr = "Invalid Name";
    }
    if (empty($email)) {
        $emailErr = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid Email format";
    }
    if (empty($mobile)) {
        $mobileErr = "Mobile number is required";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) { 
        $mobileErr = "Invalid mobile number";
    }
    if (empty($date)) {
        $dobErr = "Date of birth is required";
    }
    if (empty($gender)) {
        $genderErr = "Gender is required";
    }
    if (empty($hobby)) {
        $hobbyErr = "Hobby is required";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $hobby)) {
        $hobbyErr = "Invalid hobby";
    }
    if (!empty($NewPassword) || !empty($cpassword)) {
        if ($NewPassword !== $cpassword) {
            $passwordErr = "Passwords do not match";
        } elseif (strlen($NewPassword) < 8) {
            $passwordErr = "Password must be at least 8 characters";
        } elseif (!preg_match("#[A-Z]+#", $NewPassword)) {
            $passwordErr = "Password must contain at least 1 uppercase letter";
        } elseif (!preg_match("#[a-z]+#", $NewPassword)) {
            $passwordErr = "Password must contain at least 1 lowercase letter";
        }
    }

    if (empty($nameErr) && empty($dobErr) && empty($genderErr) && empty($hobbyErr) && empty($passwordErr)) {
        $filenames = json_encode(array_values($imageList)); 

        $updateSQL = "UPDATE login SET 
                      `name` = '$name',
                      `email` = '$email',
                      `mobile` = '$mobile',
                      `date` = '$date',
                      `gender` = '$gender',
                      `hobby` = '$hobby',
                      `filenames` = '$filenames'";

        if (!empty($NewPassword)) {
            $updateSQL .= ", `password` = '$NewPassword'"; 
        }
        $updateSQL .= " WHERE id = '$id'";
        if (mysqli_query($conn, $updateSQL)) {
            header("Location: dashboard.php"); 
        } else {
            echo "Error: " . $updateSQL . "<br>" . mysqli_error($conn);
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">  
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?php echo $name; ?>"><br><br>
    <span class="error"><?php echo $nameErr;?></span><br>

    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" value="<?php echo $email; ?>"><br><br>
    <span><?php echo $emailErr;?></span><br>

    <label for="mobile">Mobile Number:</label><br>
    <input type="tel" id="mobile" name="mobile" value="<?php echo $mobile;?>">
    <span><?php echo $mobileErr;?></span><br>

    <label for="date">Date of Birth:</label><br>
    <input type="date" id="date" name="date" value="<?php echo $date; ?>"><br><br>
    <span><?php echo $dobErr;?></span><br>

    <label for="gender">Gender:</label><br>
    <input type="radio" id="Male" name="gender" value="Male" <?php echo ($gender == 'Male') ? 'checked' : ''; ?>>
    <label for="male">Male</label><br>
    <input type="radio" id="Female" name="gender" value="Female" <?php echo ($gender == 'Female') ? 'checked' : ''; ?>>
    <label for="female">Female</label><br><br>
    <span><?php echo $genderErr;?></span>

    <label for="hobby">Hobby:</label><br>
    <input type="text" id="hobby" name="hobby" value="<?php echo $hobby?>"><br><br>
    <span><?php echo $hobbyErr;?></span><br>

    <label for="file">Image:</label><br>
    <input type="file" id="file" name="file[]" multiple>
    <?php
    // if ($imageList) {
    //     foreach ($imageList as $image) {
    //         echo '<input type="checkbox" name="delete_images[]" value="'. $image .'"checked>
    //               <img src="upload/'. $image . '" style="height:40px;width:40px;margin-right:20px;">';
    //     }
    // }
    // else{
    //     echo "No images found.";
    // }
    ?>
    <br><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <span><?php echo $passwordErr;?></span><br>
    <label for="cpassword">Confirm Password:</label><br>
    <input type="password" id="cpassword" name="cpassword"><br><br>
    <span><?php echo $passwordErr; ?></span><br>
    <input type="submit" value="Submit" name="submit">
</form> 
</body>
</html>