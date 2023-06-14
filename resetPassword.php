
<?php 
    include_once './header.php';
    include_once './helpers/session_helpers.php';
?>
    <h1 class="header">Reset Password</h1>

   <?php flash("reset"); ?>

    <form method="post" action="./controllers/resetPassword.php">
        <input type="hidden" name="type" value="reset" />
        <input type="email" name="email" placeholder="Email...">
        <button type="submit" name="submit">Receive Email</button>
    </form>
    
<?php 
    include_once './footer.php';
?>