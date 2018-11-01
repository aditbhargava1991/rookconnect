<?php
    if ( stripos(','.$_SESSION['role'].',', ',super,') === false ) {
        echo '<script>window.location.replace("../home.php");</script>';
	}
?>