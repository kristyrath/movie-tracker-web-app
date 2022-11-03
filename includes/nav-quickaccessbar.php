   <!-- NAV -->

   <nav>
        <i class="clickables fa-solid fa-bars fa-2x"></i>
        <div class="nav-content hidden">
            <h3>Videos</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="addvid.php">Add Video</a></li>
                <li><a href="search.php">Search</a></li>
            </ul>
    
        </div>
    </nav>

    <!-- QUICK ACCESS BAR -->
    
    <div id="quickaccess-bar">
        <div class="search-btn">
            <a href="search.php"><i class="clickables fa-solid fa-magnifying-glass fa-2x"></i></a>
        </div>
        <div class="home-btn">
            <a href="index.php"><i class="clickables fa-solid fa-house-chimney fa-2x"></i></a>
        </div>
        <div class="user-quick-access-btn">
            <i class="clickables fa-solid fa-circle-user fa-2x"></i>
            <div class="quickaccess-acc-content hidden">
                <h3>Account</h3>

                <!-- show options according to session -->
                <ul>
                    <?php if (!isset($_SESSION["username"])) : ?>
                        <li><a href="login.php"><?="Log In"?></a></li> 
                        <li><a href="register.php"><?="Create Account"?></a></li>  
                    <?php else: ?>
                        <li><a href="editaccount.php"><?="Edit Account"?></a></li>
                        <li><a href="deleteaccount.php"><?="Delete Account"?></a></li>
                        <li><a href="logout.php"><?="Log Out"?></a></li>    
                    <?php endif ?>


                </ul>
            </div>
        </div>
    </div> 
