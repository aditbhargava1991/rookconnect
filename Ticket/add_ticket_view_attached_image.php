<?php include_once('../include.php');
ob_clean();
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);

$image = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `attached_image` FROM `tickets` WHERE `ticketid`='$ticketid'"))['attached_image'];
if(file_exists('download/'.$image) && !empty($image)) {
	echo '<a href="../Ticket/download/'.$image.'" target="_blank">View</a>';
} else if(file_exists('../Calendar/download/'.$image) && !empty($image)) {
	echo '<a href="../Calendar/download/'.$image.'" target="_blank">View</a>';
}