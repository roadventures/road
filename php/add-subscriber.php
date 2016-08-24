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

	function JSONReturnValueSuccess()
	{
		exit(json_encode(array("status" => "SUCCESS", "errors" => null)));
	}
	
	function JSONReturnValueError($returnValue)
	{
		exit(json_encode(array("status" => "ERRORS!", "errors" => $returnValue)));
	}
	
	function createError($errorString, $errorReason)
	{
		return array("errorString" => $errorString, "errorReason" => $errorReason);
	}

    function clean_string($string) 
    {
		$bad = array("content-type","bcc:","to:","cc:","href");
		return str_replace($bad,"",$string);
    }
     
	function validateInput($Name, $Email)
	{     
		$errorArray = array();
		 
		$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
		if(!preg_match($email_exp, $Email)) {
			array_push($errorArray, createError("ERR_EMAIL", "INVALID"));
		}
			
		$string_exp = "/^[A-Za-z .'-]+$/";
		if(!preg_match($string_exp, $Name)) {
			array_push($errorArray, createError("ERR_NAME", "INVALID"));
		}
		
		if(sizeof($errorArray) > 0) 
		{
			JSONReturnValueError($errorArray);
		}
		else
		{
			$email_message = "Form details below.\n\n";
			$email_message .= "Name: " . clean_string($name)."\n";
			$email_message .= "Email: " . clean_string($email_from)."\n";
			return $email_message;
		}
		return "";
	}

	function addSubscriber($Name, $Email)
	{		
		$validated_email_message = validateInput($Name, $Email);
		if(strlen($validated_email_message) > 0)
		{ 
			// create email headers
			// CHANGE THE TWO LINES BELOW
			$email_to = "theroadventures@gmail.com";
			$email_subject = "Roadventures - New Subscriber - " . $Name;
     
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/plain; charset=iso-8859-1";
			$headers[] = "From: {$Name} <{$Email}>";
			//$headers[] = "Bcc: JJ Chong <bcc@domain2.com>";
			$headers[] = "Reply-To: {$Name} <{$Email}>";
			$headers[] = "Subject: {$email_subject}";
			$headers[] = "X-Mailer: PHP/".phpversion();

			// Mail to report
			WriteToFile("Subscribers.html", ComposeSubscriberLogMessage($Name, $Email));
			
			// Mail to report
			$AcceptedForDelivery = false;
			$AcceptedForDelivery = mail($email_to, $email_subject, $validated_email_message, implode("\r\n", $headers));
			WriteToFile("EmailLog.html", ComposeEmailLogMessage($AcceptedForDelivery, $email_to, $email_subject, $validated_email_message, implode("\r\n", $headers)));

			// Mail reply to submitter
			$AcceptedForDeliveryReply = false;
			$AcceptedForDeliveryReply = mail($Email, "Roadventures subscription enabled", "This is just a confirmation message for your records, we have received your subscription request for new articles and you will receive a new notification as soon as a new article appears.\r\n" . $validated_email_message, implode("\r\n", $headers));
			
			/*
			$headers = 'From: ' . $email_from . "\r\n".
						'Reply-To: ' . $email_from . "\r\n" .
						'X-Mailer: PHP/' . phpversion();
			@mail($email_to, $email_subject, $validated_email_message, $headers);  */
			//http_response_code(200);
			JSONReturnValueSuccess();
		}
		else
		{
			JSONReturnValueError("Unspecified");
		}
	}
	
	function WriteToFile($Filename, $Message)
	{
		$fullFilename  = dirname(__FILE__) . '/' . $Filename;
		file_put_contents($fullFilename, $Message . PHP_EOL, FILE_APPEND);
	}
	
	function ComposeSubscriberLogMessage($Name, $Email)
	{
		$EmailLogMessage = "";
		$dateStamp = date('Y-m-d H:i:s');
		$EmailLogMessage .= "<Datestamp>{$dateStamp}</Datestamp>";
		$EmailLogMessage .= "<Name>{$Name}</Name>";
		$EmailLogMessage .= "<Email>{$Email}</Email>";
		return "<Subscriber>{$EmailLogMessage}</Subscriber><BR>\r\n";
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

$Name = GetValueFromPostOrGet('name');
$Email = GetValueFromPostOrGet('email');

if(empty($Name) || empty($Email))
{
	$errorArray = array();
	if(empty($Name)) array_push($errorArray, createError("ERR_NAME", "EMPTY"));
	if(empty($Email)) array_push($errorArray, createError("ERR_EMAIL", "EMPTY"));
	JSONReturnValueError($errorArray);
}
else
{
	addSubscriber($Name, $Email);
}
?>