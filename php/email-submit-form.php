<?php
	// Allow cross-domain XmlHTTPRequests
	/* Warning : This contains a security issue for your PHP file that it could be called by attackers. 
	 * You have to use sessions and cookies for authentication to prevent your file/service against this attack. 
	 * Your service is vulnerable to cross-site request forgery (CSRF).
	https://en.wikipedia.org/wiki/Cross-site_request_forgery
	*/
	header('Access-Control-Allow-Origin: *');

	if ((include 'common/nocache.php') == FALSE) 
	{
		echo 'Failed to include nocache.php';
	}

	if ((include 'common/http_response_code.php') == FALSE) 
	{
		echo 'Failed to include http_response_code.php';
	}

    function ReturnErrorMessage($error) 
    {
        http_response_code(400);
		$Result = "<BR>We are very sorry, but there were error(s) found with the form you submitted. ";
        $Result .= "These errors appear below.<br /><br />";
        $Result .= $error . "<br /><br />";
        $Result .= "Please go back and fix these errors.<br /><br />";
		exit($Result);
    }

    function clean_string($string) 
    {
		$bad = array("content-type","bcc:","to:","cc:","href");
		return str_replace($bad,"",$string);
    }
     
	function validateInput($FirstName, $LastName, $Email, $Message)
	{     
		$error_message = "";

		$first_name = $FirstName;
		$last_name = $LastName;
		$email_from = $Email;
		$comments = $Message;
		 
		$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';

		if(!preg_match($email_exp,$email_from)) {
			$error_message .= 'The Email Address you entered does not appear to be valid.<br />';
		}
			
		$string_exp = "/^[A-Za-z .'-]+$/";
		if(!preg_match($string_exp,$first_name)) {
			$error_message .= 'The First Name you entered does not appear to be valid.<br />';
		}

		if(!preg_match($string_exp,$last_name)) {
			$error_message .= 'The Last Name you entered does not appear to be valid.<br />';
		}
		  
		if(strlen($comments) < 2) {
			$error_message .= 'The Comments you entered do not appear to be valid.<br />';
		}
		
		if(strlen($error_message) > 0) 
		{
			ReturnErrorMessage($error_message);
		}
		else
		{
			$email_message = "Form details below.\n\n";
			$email_message .= "First Name: " . clean_string($first_name)."\n";
			$email_message .= "Last Name: " . clean_string($last_name)."\n";
			$email_message .= "Email: " . clean_string($email_from)."\n";
			$email_message .= "Comments: " . clean_string($comments)."\n";
			return $email_message;
		}
		return "";
	}

	function sendEmail($FirstName, $LastName, $Email, $Message)
	{		
		$validated_email_message = validateInput($FirstName, $LastName, $Email, $Message);
		if(strlen($validated_email_message) > 0)
		{ 
			// create email headers
			// CHANGE THE TWO LINES BELOW
			$email_to = "theroadventures@gmail.com";
			$email_subject = "Roadventures - New Contact - {$FirstName} {$LastName}";
     
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/plain; charset=iso-8859-1";
			$headers[] = "From: {$FirstName} {$LastName} <{$Email}>";
			//$headers[] = "Bcc: JJ Chong <bcc@domain2.com>";
			$headers[] = "Reply-To: {$FirstName} {$LastName} <{$Email}>";
			$headers[] = "Subject: {$email_subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();

			// Mail to report
			$AcceptedForDelivery = false;
			$AcceptedForDelivery = mail($email_to, $email_subject, $validated_email_message, implode("\r\n", $headers));
			WriteToFile("EmailLog.html", ComposeEmailLogMessage($AcceptedForDelivery, $email_to, $email_subject, $validated_email_message, implode("\r\n", $headers)));

			// Mail reply to submitter
			$AcceptedForDeliveryReply = false;
			$AcceptedForDeliveryReply = mail($Email, "Roadventures contact request successfully sent", "This is just a confirmation message for your records, we have received your message and if you have requested a response we will get back to you soon.\r\n" . $validated_email_message, implode("\r\n", $headers));
			
			/*
			$headers = 'From: ' . $email_from . "\r\n".
						'Reply-To: ' . $email_from . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
			@mail($email_to, $email_subject, $validated_email_message, $headers);  */
			//http_response_code(200);
			echo "Thank you for contacting us. We will be in touch with you very soon.";
		}
		else
		{
			http_response_code(405);
			echo "<BR>An unspecified error occurred, you may try again in a moment.";
		}
	}
	
	function WriteToFile($Filename, $Message)
	{
		$fullFilename  = dirname(__FILE__) . '/' . $Filename;
		file_put_contents($fullFilename, $Message . PHP_EOL, FILE_APPEND);
	}
	
	function ComposeEmailLogMessage($AcceptedForDelivery, $email_to, $email_subject, $validated_email_message, $headers)
	{
		$EmailLogMessage = "";
		$dateStamp = date('Y-m-d H:i:s');
		$EmailLogMessage .= "<Datestamp>{$dateStamp}</Datestamp>";
		$EmailLogMessage .= "<Success>{$AcceptedForDelivery}</Success>";
		$EmailLogMessage .= "<To>{$email_to}</To>";
		$EmailLogMessage .= "<Subject>{$email_subject}</Subject>";
		$EmailLogMessage .= "<Message>{$validated_email_message}</Message>";
		$EmailLogMessage .= "<Headers>{$headers}</Headers>";
		return "<Email>{$EmailLogMessage}</Email><BR>\r\n";
	}
	
	function GetValueFromPostOrGet($requested_value)
	{
		if(isset($_POST[$requested_value])) 
		{
			return $_POST[$requested_value];
		}
		else if(isset($_GET[$requested_value]))
		{
			return $_GET[$requested_value];
		}
		return "";
	}

$FirstName = GetValueFromPostOrGet('firstname');
$LastName = GetValueFromPostOrGet('lastname');
$Email = GetValueFromPostOrGet('email');
$Message = GetValueFromPostOrGet('message');

if(empty($FirstName) || empty($LastName) || empty($Email) || empty($Message))
{
	http_response_code(400);
	echo '<BR>We are sorry, but there appears to be a problem with the form you submitted (some values appeared empty).';
}
else
{
	sendEmail($FirstName, $LastName, $Email, $Message);
}
?>