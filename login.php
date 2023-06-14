
<?php 
    include_once 'header.php';
    include_once './helpers/session_helpers.php';
?>
    <h1 class="header">Please Login</h1>

  <?php 
    flash("login"); 
  ?> 

    <form method="post" action="./controllers/Users.php">
        <input type="hidden" name="type" value="login">
        <input type="email" name="email"  
        placeholder="Email...">
        <input type="password" name="password" 
        placeholder="Password...">
        <button type="submit" name="submit">Log In</button>
    </form>

    <div class="form-sub-msg"><a href="./resetpassword.php">Forgotten Password?</a></div>
    
<?php 
    include_once 'footer.php'
?>