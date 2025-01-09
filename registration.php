<?php
    include('connection.php');  
    session_start();
    $nameErr = $emailErr = $mobileErr = $dobErr = $genderErr = $hobbyErr = $passwordErr = $cpasswordErr = "";

    if(isset($_POST['submit'])){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $date = $_POST['date'];
        $gender = ['gender'];
        $hobby = $_POST['hobby'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        
        $fileNames = array();  
        if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name'][0])) {
            foreach ($_FILES['file']['name'] as $key => $fileName) {
                $newName = time() . '_' . $key; 
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileNewName = $newName . '.' . $ext;

                if (move_uploaded_file($_FILES['file']['tmp_name'][$key], 'upload/' . $fileNewName)) {
                    $fileNames[] = $fileNewName;  
                } else {
                    echo 'File upload failed: ' . $_FILES['file']['error'][$key];
                }
            }
        }

        $fileNamesJson = json_encode($fileNames);
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

        if (empty($password)) {
            $passwordErr = "Password is required";
        } elseif(strlen($password) < 8) {
            $passwordErr = "Password should be at least 8 characters long";
        } elseif(!preg_match("#[A-Z]+#", $password)) {
            $passwordErr = "Password must contain at least one uppercase letter";
        } elseif(!preg_match("#[a-z]+#", $password)) {
            $passwordErr = "Password must contain at least one lowercase letter";
        }

        if (empty($cpassword)) {
            $cpasswordErr = "Please confirm your password";
        } elseif($password !== $cpassword) {
            $cpasswordErr = "Passwords do not match";
        }

        if (empty($gender)) {
            $genderErr = "Gender is required";
        } elseif (!preg_match("/^[a-zA-Z]*$/", $gender)) {
            $genderErr = "Invalid gender";
        }

        if (empty($hobby)) {
            $hobbyErr = "Hobby is required";
        } elseif (!preg_match("/^[a-zA-Z ]*$/", $hobby)) {
            $hobbyErr = "Invalid hobby";
        }

        if (empty($nameErr) && empty($emailErr) && empty($mobileErr) && empty($dobErr) && empty($genderErr) && empty($hobbyErr) && empty($passwordErr) && empty($cpasswordErr)) {
            
            $sql = "INSERT INTO login (`name`, `email`, `mobile`, `date`, `gender`, `hobby`, `filenames`, `password`, `cpassword`) 
                    VALUES ('".$name."', '".$email."', '".$mobile."', '".$date."', '".$gender."', '".$hobby."', '".$fileNamesJson."', '".$password."', '".$cpassword."')";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: login.php"); 
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
?>      

<form method="POST" enctype="multipart/form-data">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>"><br>
    <span class="error"><?php echo $nameErr;?></span><br>

    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>"><br>
    <span class="error"><?php echo $emailErr;?></span><br>

    <label for="mobile">Mobile Number:</label><br>
    <input type="tel" id="mobile" name="mobile" value="<?php echo isset($mobile) ? $mobile : ''; ?>"><br>
    <span class="error"><?php echo $mobileErr;?></span><br>

    <label for="date">Date of Birth:</label><br>
    <input type="date" id="date" name="date" value="<?php echo isset($date) ? $date : ''; ?>"><br>
    <span class="error"><?php echo $dobErr;?></span><br>

    <label for="gender">Gender:</label><br>
    <input type="radio" id="Male" name="gender" value="Male" <?php echo (isset($gender) && $gender == 'Male') ? 'checked' : ''; ?>>
    <label for="Male">Male</label><br>
    <input type="radio" id="Female" name="gender" value="Female" <?php echo (isset($gender) && $gender == 'Female') ? 'checked' : ''; ?>>
    <label for="Female">Female</label><br>
    <span class="error"><?php echo $genderErr;?></span><br>

    <label for="hobby">Hobby:</label><br>
    <input type="text" id="hobby" name="hobby" value="<?php echo isset($hobby) ? $hobby : ''; ?>"><br>
    <span class="error"><?php echo $hobbyErr;?></span><br>

    <label for="file">Images:</label><br>
    <input type="file" id="file" name="file[]" multiple><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br>
    <span class="error"><?php echo $passwordErr;?></span><br>

    <label for="cpassword">Confirm Password:</label><br>
    <input type="password" id="cpassword" name="cpassword"><br><br>
    <span class="error"><?php echo $cpasswordErr;?></span><br>

    <input type="submit" value="Submit" name="submit">
</form> 
