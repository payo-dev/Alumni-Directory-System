<?php
// adminPage.php

include_once '../config.php';
include_once '../classes/Database.php';
include_once '../classes/Functions.php';

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();

// Initialize functions
$functions = new Functions($conn);

// Fetch alumni records for display
$alumniRecords = $functions->getAllAlumni();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h1>Admin Dashboard</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumniRecords as $alumni): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumni['id']); ?></td>
                    <td><?php echo htmlspecialchars($alumni['name']); ?></td>
                    <td><?php echo htmlspecialchars($alumni['email']); ?></td>
                    <td>
                        <a href="../functions/updateAlumni.php?id=<?php echo $alumni['id']; ?>">Edit</a>
                        <a href="../functions/deleteAlumni.php?id=<?php echo $alumni['id']; ?>">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>