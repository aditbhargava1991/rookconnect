<?php
    if ( stripos(','.$_SESSION['role'].',', ',super,') === false ) {
		header('location: home.php');
		die();
	}
?>