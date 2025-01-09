<?php
    include("connection.php");
?>
<html>
    <style>
        table, th, td {
            border: 2px solid;
        }
    </style>
    <h2>Dashboard</h2>
    <button onclick="location.href='registration.php'">Add+</button>
    <br>
    <?php
        $sql = "SELECT * FROM login";
        $result = mysqli_query($conn, $sql);
    ?>
    <br>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Dob</th>
            <th>Gender</th>
            <th>Hobby</th>
            <th>Images</th>
            <th>Action</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $imageList = $row['filenames'];
            // echo '<pre>';
            // print_r($imageList);
            // die();
            $imagejson = json_decode($imageList);
            // echo '<pre>';
            // print_r($imagejson);
            // die();

        ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['mobile']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['gender']; ?></td>
            <td><?php echo $row['hobby']; ?></td>
            <td>
                <?php
                foreach ($imagejson as $image) {
                    // echo '<pre>';
                    // print_r($image);
                    // die();
                    echo '<img src="upload/'.$image.'"style="height:40px;width:40px;margin-right:20px;">';

                }
                ?>
            </td>
            <td>
                <a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>&nbsp;
                <a href="update.php?id=<?php echo $row['id']; ?>">Edit</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</html>
