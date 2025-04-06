<?php
session_start();
include '../config/conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" href="../assets/logo2.png" type="image/png">
</head>

<body>
    <div class="wrapper">
        <?php include 'components/sidebar.php'; ?>
        <div class="main-content">
            <h2>User Settings</h2>
            <form action="functions/update_profile.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label>No Phone:</label>
                <input type="text" name="nophone" value="<?php echo htmlspecialchars($user['nophone']); ?>">

                <label>Address:</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">

                <label>Date of Birth:</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">

                <label>Status:</label>
                <select name="status">
                    <option value="Active" <?php echo ($user['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo ($user['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>