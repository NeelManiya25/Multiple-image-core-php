<?php
$conn = mysqli_connect("localhost","root","","user_login");

if(!$conn){
  die("Connection failed: ". mysqli_connect_error());
}
?>
