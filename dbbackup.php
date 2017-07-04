<?php
/*
   PHP Database Backup Script For Drupal 8
   Copyright (c) 2017
   
   By Batsakidis Athanasios
   
   Tested on: Drupal 8.X and CPanel/XAMMP
*/

$password = "123"; //Change to whatever you want your password to be

$changelog = "CHANGELOG.txt";

if (file_exists($changelog)) {
	$lines = file($changelog);
}

if(isset($_POST['submit'])){
        if($_POST['password'] == $password){
        		
			$INC_DIR = dirname(__FILE__). "/sites/default/";
			include($INC_DIR. "settings.php"); 
			
			$DBUSER=$databases['default']['default']['username'];
			$DBPASSWD=$databases['default']['default']['password'];
			$DATABASE=$databases['default']['default']['database'];

			// Create connection
			$con=mysqli_connect("localhost",$DBUSER,$DBPASSWD,$DATABASE);
			// Check connection
			if (mysqli_connect_errno($con))
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}    
			
			$sql = "SELECT 'TRUNCATE TABLE '+TABLE_NAME+ ';' FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'cache%'";
			mysqli_query($con, $sql) or die(mysqli_error());
			
			$sql2 = "SELECT 'TRUNCATE TABLE '+TABLE_NAME+ ';' FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'search_%'";
			mysqli_query($con, $sql2) or die(mysqli_error());

			$sql3 = "TRUNCATE TABLE watchdog";	mysqli_query($con, $sql3) or die(mysqli_error());
			
			$filename = "backup-" . $_SERVER['HTTP_HOST'] . "-" . date("d-m-Y") . ".sql.gz";
			$mime = "application/x-gzip";

			header( "Content-Type: " . $mime );
			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

			$cmd = "mysqldump -u $DBUSER --password=$DBPASSWD $DATABASE | gzip --best";   

			passthru( $cmd );

			exit(0);	
        } 
		else 
		{
            echo "Sorry the password is incorrect";
        }
}
else 
{
	//IF THE FORM WAS NOT SUBMITTED - SHOW FORM
        ?><form method="post">
		         <?php 
		               echo "PHP Database Backup Script For Drupal 7"."<br>"; 
			           echo "Copyright (c) 2017"."<br><br>"; 
					   if (file_exists($changelog)) {echo "Drupal Version: <strong>" . $lines[1]."</strong><br><br>";}
			     ?>
				  
                 Password: <input type="password" name="password" />
                <input type='submit' name='submit' />
        </form><?php
}
?>
