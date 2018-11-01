<?php
/*
 * Login
 */
error_reporting(0);
$guest_access = true;
include ('database_connection.php');
include ('function.php');
include ('global.php');

if (isset($_POST['loginformsubmitted'])) {
    $msg     = '';
    $user_name  = filter_var($_POST['user_name'],FILTER_SANITIZE_STRING);
    $password   = encryptIt($_POST['password']);
    $result_check_credentials = mysqli_query($dbc, "SELECT contactid, first_name, last_name, category, software_tile_menu_choice, toggle_tile_menu, newsboard_menu_choice, password_update, role, user_name, email_address, office_email FROM contacts WHERE BINARY user_name='$user_name' AND BINARY password='$password' AND status > 0");

    if (@mysqli_num_rows($result_check_credentials) > 0)//if Query is successfull
    { // A match was made.
        session_start(['cookie_lifetime' => 518400]);
        $_SESSION = mysqli_fetch_assoc($result_check_credentials);

        if(empty($_SESSION['role']) && !empty($_SESSION['category'])) {
            $_SESSION['role'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `role` FROM `field_config_security_contact_categories` WHERE `category` = '".$_SESSION['category']."'"))['role'];
        }

        $contactid  = $_SESSION['contactid'];
		$login_ip	= filter_var(isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']),FILTER_SANITIZE_STRING);
        $result_insert_history = mysqli_query($dbc, "INSERT INTO `login_history` (`contactid`, `user_name`, `login_ip`) VALUES ('$contactid', '$user_name', '$login_ip')");

        if($_POST['location'] != '') {
            $redirect = $_POST['location'];
            header("Location:".WEBSITE_URL.str_replace(WEBSITE_URL,'',$redirect));
        } else {
			$get_config_size = get_user_settings();
			$newsb_red = $_SESSION['newsboard_menu_choice'] != NULL ? $get_config_size['newsboard_redirect'] : '';
			$calendar_red = $get_config_size['calendar_redirect'];
			$daysheet_red = $get_config_size['daysheet_redirect'];
			if($newsb_red == '1') {
				header('Location: '.WEBSITE_URL.'/News Board/index.php');
			} else if($calendar_red == '1') {
				header('Location: '.WEBSITE_URL.'/Calendar/calendars.php');
			} else if($daysheet_red == '1') {
				header('Location: '.WEBSITE_URL.'/Profile/daysheet.php');
			} else {
				$default_login = get_config($dbc, 'default_login');
				if($default_login == 'News Board') {
					header('Location: '.WEBSITE_URL.'/News Board/index.php');
				} else if($default_login == 'Calendar') {
					header('Location: '.WEBSITE_URL.'/Calendar/calendars.php');
				} else if($default_login == 'Day Sheet') {
					header('Location: '.WEBSITE_URL.'/Profile/daysheet.php');
				} else {
					header('Location: '.WEBSITE_URL.'/home.php');
				}
			}
		}
    } else {
		$contactid = @mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `user_name`='$user_name'"))['contactid'];
		$login_ip = filter_var(isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']),FILTER_SANITIZE_STRING);
        $result_insert_history = mysqli_query($dbc, "INSERT INTO `login_history` (`contactid`, `user_name`, `login_ip`, `success`) VALUES ('$contactid', '$user_name', '$login_ip', 0)");
        $msg .= '<div class="alert alert-danger">Username and/or password is incorrect. Please try again.</div>';
    }

    /// var_dump($error);
    mysqli_close($dbc);

} // End of the main Submit conditional.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ROOK Connect Login</title>
    <link href="img/favicon.ico" rel="shortcut icon">
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon-precomposed">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,600' rel='stylesheet' type='text/css'>
	<?php include ('login_page_style.php'); ?>
</head>
<style>
@media (max-width:479px) {
    .login form { width: 100vw; margin-top: 0; }
}
</style>
<body>
    <div class="login">
        <div class="middle">

            <form role="form" action="index.php" method="post" class="registration_form triple-padded">
                <?php
                    echo '<input type="hidden" name="location" value="';
                    if(isset($_GET['location'])) {
                        echo htmlspecialchars($_GET['location']);
                    }
                    echo '" />';
                ?>
                <div class="row">
                    <div class="col-lg-12 double-pad-bottom">
                        <?php
                        $logo_upload = get_config($dbc, 'logo_upload');
			            if($logo_upload == '') {
                            echo '<img src="img/logo.png" alt="Fresh Focus Media" class="center-block" width="300">';
                        } else {
                            echo '<img src="Settings/download/'.$logo_upload.'" alt="Fresh Focus Media" class="center-block" width="300">';
                        }
                        ?>
                    </div>
                </div>
                <div class="row triple-pad-top">
                    <ul class="list-inline text-center">
                         <?php include('Navigation/social_media_links.php'); ?>
                    </ul>
                </div>

				<?php if ( $msg ) { ?>
                    <div class="form-group double-gap-top">
                        <?= $msg ?>
                    </div>
				<?php } ?>

                <div class="form-group">
                    <label for="name" class="h3 hp-red">Username:</label>
                    <?php
                        $user_val = '';
                        if(!empty($_POST['user_name'])) {
                            $user_val = $_POST['user_name'];
                        }
                    ?>
                    <input type="text" id="user_name" required class="form-control" name="user_name" size="25" maxlength="50" value="<?php echo $user_val; ?>" >
                </div>
                <div class="form-group">
                    <label for="password" class="h3 hp-red">Password:</label>
                    <input type="password" required id="password" class="form-control" name="password" size="25" />
                </div>
                <div class="form-group">
                    <a href="forgot_pwd.php" class="double-pad-right">Forgot Password</a>
                </div>
                <div class="form-group">
                    <input type="hidden" name="loginformsubmitted" value="TRUE" />
                    <button type="submit" value="Login" class="btn brand-btn btn-lg">Submit</button>
                </div>
            </form>
            <div class="clearfix">&nbsp;</div>
        </div>
    </div>
</body>
</html>
