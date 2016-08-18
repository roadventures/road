/*
$( document ).ajaxError(function() 
{
	alert("Triggered ajaxError handler.");
});
*/

function EmailSubmitFormSuccess(returnText)
{
	alert("Success function with values: " + returnText);
	var jsonValues = JSON.parse(returnText);
	if(jsonValues == "SUCCESS")
	{
		document.getElementById('contact-success-text').style.display = 'block';
		// alert('SUCCESS' + jsonValues);
	}
}

function EmailSubmitFormError(xhr, textStatus, errorThrown)
{
	if(xhr != null)
	{
		//console.log(xhr);
		if(xhr.responseText != null)
		{
			var jsonValues = JSON.parse(xhr.responseText);
			alert(jsonValues);
			alert('An error occurred: ' + jsonValues.Message);

			/*
			alert("Message: " + jsonValues.Message);
			alert("StackTrace: " + jsonValues.StackTrace);
			alert("ExceptionType: " + jsonValues.ExceptionType);
			*/
		}
	}
}

function ClearErrorEmailForm()
{
	document.getElementById('firstname-error').style.display = 'none';
	document.getElementById('contact-first-name').style.border="none";

	document.getElementById('lastname-error').style.display = 'none';
	document.getElementById('contact-last-name').style.border="none";

	document.getElementById('email-error').style.display = 'none';
	document.getElementById('contact-email').style.border="none";

	document.getElementById('message-error').style.display = 'none';
	document.getElementById('contact-message').style.border="none";
}


function SendEmailForm()
{
	var FirstName = document.getElementById("contact-first-name").value;
	var LastName = document.getElementById("contact-last-name").value;
	var Email = document.getElementById("contact-email").value;
	var Message = document.getElementById("contact-message").value;

	ClearErrorEmailForm();


	if(FirstName == "" || LastName == "" || Email == "" || Message == "")
	{
		if(FirstName == "")
		{	
			document.getElementById('firstname-error').style.display = 'block';
			document.getElementById('contact-first-name').style.border="3px solid #cb2027";
		}
		if(LastName == "")
		{	
			document.getElementById('lastname-error').style.display = 'block';
			document.getElementById('contact-last-name').style.border="3px solid #cb2027";
		}
		if(Email == "")
		{	
			document.getElementById('email-error').style.display = 'block';
			document.getElementById('contact-email').style.border="3px solid #cb2027";
		}
		if(Message == "")
		{	
			document.getElementById('message-error').style.display = 'block';
			document.getElementById('contact-message').style.border="3px solid #cb2027";
		}
		return;
	}

	
	$.ajax
	(
		{ 
			url: 'http://rana.carlstrom.fi/Roadventures/php/email-submit-form.php',
			/*async: false,*/
			dataType: "text",
	        type: 'get',
			
	        data:
	        { 
	         	firstname: FirstName, 
	         	lastname: LastName,
	         	email: Email, 
	         	message: Message 
	        },
	         
	        success: EmailSubmitFormSuccess,
            error: EmailSubmitFormError
		}
	);
}

function AddSubscriberForm()
{
	var Name = document.getElementById("subscribe-first-name").value;
	var Email = document.getElementById("subscribe-email").value;

	if(Name == "" || Email == "")
	{	
		alert("Please fill in all the fields before submitting.");
		return;
	}
	
	$.ajax
	(
		{ 
			url: 'http://rana.carlstrom.fi/Roadventures/php/add-subscriber.php',
			/*async: false,*/
			dataType: "text",
	        type: 'get',
			
	        data:
	        { 
	         	name: Name, 
	         	email: Email
	        },
	         
	        success: EmailSubmitFormSuccess,
            error: EmailSubmitFormError
		}
	);
}