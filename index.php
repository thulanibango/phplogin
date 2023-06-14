<?php 
    include_once 'header.php'
?>

    <h1 id="index-text">Welcome, <?php if(isset($_SESSION['userId'])){
        echo $_SESSION['name'];
    }else{
        echo 'Guest';
    } 
    ?> </h1>
    

<?php 
    include_once 'footer.php'
?>