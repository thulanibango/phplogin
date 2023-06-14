<?php 
    include_once 'header.php';
    include_once './helpers/session_helpers.php';
?>

    <h1 class="header">Please Signup</h1>

    <?php flash('registration'); ?>

    <form method="post" action="./controllers/Users.php">
        <input type="hidden" name="type" value="registration">
        <input type="text" name="userName" 
        placeholder="Full name...">
        <input type="text" name="email" 
        placeholder="Email...">
        <!-- <input type="text" name="userUid" 
        placeholder="Username..."> -->
        <input type="password" name="password" 
        placeholder="Password...">
        <input type="password" name="password2" 
        placeholder="Repeat password">
        <button type="submit" name="submit">Sign Up</button>
    </form>
    
<?php 
    include_once 'footer.php'
?>