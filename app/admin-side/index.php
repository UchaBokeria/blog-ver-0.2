<?php 
    if(!isset($_SESSION["token"]))
        header("Location:../../assets/wildcard.php"); 
?>
<style>
    #head{
        width:100%;
        height:11vh;
        color:#252525;
        padding:4vh 3% 0 5%;
        border-bottom:3px solid rgb(187, 187, 187);

    }
    #head p{
        width:auto;
        height:4vh;
        cursor:pointer;
        float:left;
        margin-right:5%;
    }
    .active-page{
        border-bottom:1px solid #252525;
    }
    .logout_but{
        position: absolute;
        right: 5%;
        border-radius: 5px;
        width:9%;
    }
</style>

<div id="head">
    <p class="active-page"  route-link="home">Ausstellung</p>
    <p class="aactive-page" route-link="blog">My Blog</p>
    <p class="aactive-page" route-link="profile">Profil</p>
    
    <form action="app/admin-side/index.php" method="POST">
        <button class="logout_but" name="logout">log out</button>
    </form>
</div>



<div id="admin-content" style="padding:3vh 5%;">
    <!-- AJAX FILL -->
</div>

<!-- logout -->
<?php 
    session_start();

    if(!isset($_SESSION["token"]))
        header("Location:../../../assets/wildcard.php"); 

    if(isset($_POST["logout"])) {
        $_SESSION["token"] = "";
        unset($_SESSION['token']);
        $_SESSION = array(); // destroy all $_SESSION data
        setcookie("PHPSESSID", "", time() - 3600, "/");
        session_destroy();
        header("Location:../../login.php?loggedout"); 
    }
?>