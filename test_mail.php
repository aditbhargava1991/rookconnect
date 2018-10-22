<?php
// the message
$msg = "First line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
send_email("jenish.khunt@gmail.com","My subject",$msg);
?>
