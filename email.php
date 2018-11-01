<?php require_once 'swiftmailer-5.x/lib/swift_required.php';
$temp_config_email_address = get_config($_SERVER['DBC'], 'main_email_address');
DEFINE('EMAIL_ADDRESS', ($temp_config_email_address == '' ? 'info@rookconnect.com' : $temp_config_email_address));
$temp_config_email_name = get_config($_SERVER['DBC'], 'main_email_name');
DEFINE('EMAIL_NAME', ($temp_config_email_name == '' ? 'ROOK Connect' : $temp_config_email_name));

function send_email($from, $to, $cc, $bcc, $subject, $body, $attachment = '') {
	// Get Email Settings and Set Defaults
	if(!defined('EMAIL_SERVER')) {
		$temp_config_email_server = get_config($_SERVER['DBC'], 'main_email_server');
		DEFINE('EMAIL_SERVER', ($temp_config_email_server == '' ? 'smtp.gmail.com' : $temp_config_email_server));
		$temp_config_email_port = get_config($_SERVER['DBC'], 'main_email_port');
		DEFINE('EMAIL_PORT', ($temp_config_email_port == '' ? 465 : $temp_config_email_port));
		$temp_config_email_mode = get_config($_SERVER['DBC'], 'main_email_mode');
		DEFINE('EMAIL_MODE', ($temp_config_email_mode == '' ? 'ssl' : $temp_config_email_mode));
		$temp_config_email_user = get_config($_SERVER['DBC'], 'main_email_user');
		DEFINE('EMAIL_USER', ($temp_config_email_user == '' ? 'info@rookconnect.com' : $temp_config_email_user));
		$temp_config_email_pass = get_config($_SERVER['DBC'], 'main_email_pass');
		DEFINE('EMAIL_PASS', ($temp_config_email_pass == '' ? decryptIt('OkUMtNluu/hXFA8EQrFtk2WalhiO8v1RDccUUWaGoeI=') : decryptIt($temp_config_email_pass)));
	}
	// Setup the sending address and reply address
	$sender = [];
	if(EMAIL_SERVER != 'smtp.gmail.com') {
		$sender = EMAIL_USER;
		if($from == '') {
			$replyTo = EMAIL_ADDRESS;
		} else if(is_array($from)) {
			foreach($from as $address => $name) {
				if(count($sender) == 0) {
					$replyTo = ($address == '' ? EMAIL_ADDRESS : $address);
				}
			}
		} else {
			$replyTo = $from;
		}
	} else {
		if($from == '') {
			$sender = [EMAIL_ADDRESS => EMAIL_NAME];
		} else if(is_array($from)) {
			foreach($from as $address => $name) {
				if(count($sender) == 0) {
					$sender[($address == '' ? EMAIL_ADDRESS : $address)] = ($name == '' ? EMAIL_NAME : $name);
				}
			}
		} else {
			$sender[$from] = $from;
		}
		$replyTo = $sender;
	}
	
	// Setup the recipient addresses
	$recipient = [];
	if(defined('OVERRIDE_SEND_EMAIL_TO')) {
		$recipient[OVERRIDE_SEND_EMAIL_TO] = 'OVERRIDE TARGET';
	} else if(is_array($to)) {
		foreach($to as $address => $name) {
			$recipient[($address == '' || $address > 0 ? $name : $address)] = ($name == '' ? $address : $name);
		}
	} else {
		$recipient[$to] = $to;
	}
	$recipient = array_filter($recipient);
	$cc = array_filter(is_array($cc) ? $cc : [$cc]);
	$bcc = array_filter(is_array($bcc) ? $bcc : [$bcc]);
    if(defined('OVERRIDE_SEND_EMAIL_TO')) {
		$cc = [];
        $bcc = [];
	}
	if(count($recipient) == 0 && count($cc) == 0 && count($bcc) == 0) {
		throw new Exception('No Recipient Address Given');
	}

	// Initialize the connection to the server
    $transport = Swift_SmtpTransport::newInstance(EMAIL_SERVER, EMAIL_PORT, EMAIL_MODE)->setUsername(EMAIL_USER)->setPassword(EMAIL_PASS)->setSourceIP('0.0.0.0');
	$transport->start();
	if(!$transport->isStarted()) {
		throw new Exception("Unable to connect to Email Server.");
	}

	// Create the outbound message, and handle any html encoded entities
    $message = Swift_Message::newInstance();
    $message->setSubject(html_entity_decode($subject))->setFrom($sender)->setReplyTo($replyTo)->setTo($recipient)->setCc($cc)->setBcc($bcc)->setBody('<html><head></head><body>'.html_entity_decode($body).'</body></html>', 'text/html');
	
	// Add attachments
    Swift_Preferences::getInstance()->setCacheType('null');
	foreach(array_filter(explode('*#FFM#*', $attachment)) as $attach) {
        if(file_exists($attach) && $attach != '') {
            $message->attach(Swift_Attachment::fromPath($attach), "application/octet-stream");
        }
	}

	// Send the message
    $mailer = Swift_Mailer::newInstance($transport);
	$error = false;
	try {
		if(!$mailer->send($message, $error)) {
			throw new Exception('Error Sending Message: '.print_r($error, true));
		}
	} catch (\Swift_TransportException $e) {
		throw new Exception($e->getMessage());
	}
}