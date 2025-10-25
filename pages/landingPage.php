<?php
include '../includes/header.php';
?>

<div class="container">
    <h1>Welcome to the Alumni Directory System</h1>
    <p>Please fill out the form below to add your information to our alumni directory.</p>
    
    <form action="../functions/addAlumni.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="graduationYear">Graduation Year:</label>
        <input type="number" id="graduationYear" name="graduationYear" required>

        <input type="submit" value="Submit">
    </form>
</div>

<?php
include '../includes/footer.php';
?>