/*
$( document ).ajaxError(function() 
{
	alert("Triggered ajaxError handler.");
});
*/

function showAddSubscriberErrorOverlay(jsonValues)
{
	if(jsonValues.status == "ERRORS!")
	{
		var IsNameError = false;
		var IsEmailError = false;
		$(jsonValues.errors).each(function()
		{
			if(this.errorString == "ERR_NAME") IsNameError = true;
			else if(this.errorString == "ERR_EMAIL") IsEmailError = true;
		});
		ShowErrorAddSubscriberForm(IsNameError, IsEmailError);
	}
}

function showEmailErrorOverlay(jsonValues)
{
	if(jsonValues.status == "ERRORS!")
	{
		var IsFirstNameError = false;
		var IsLastNameError = false;
		var IsEmailError = false;
		var IsMessageError = false;
		$(jsonValues.errors).each(function()
		{
			if(this.errorString == "ERR_FIRSTNAME") IsFirstNameError = true;
			else if(this.errorString == "ERR_LASTNAME") IsLastNameError = true;
			else if(this.errorString == "ERR_EMAIL") IsEmailError = true;
			else if(this.errorString == "ERR_MESSAGE") IsMessageError = true;	
		});
		ShowErrorEmailForm(IsFirstNameError, IsLastNameError, IsEmailError, IsMessageError);
	}
}

function AddSubscriberFormSuccess(returnText)
{
	var jsonValues = null;
	try
	{
		jsonValues = JSON.parse(returnText);
	}
	catch(err)
	{
		jsonValues = returnText;
	}
	
	if(jsonValues == "SUCCESS")
	{
		document.getElementById('contact-success-text').style.display = 'block';
	}
	else
	{
		showAddSubscriberErrorOverlay(jsonValues);
	}
}

function EmailSubmitFormSuccess(returnText)
{
	var jsonValues = null;
	try
	{
		jsonValues = JSON.parse(returnText);
	}
	catch(err)
	{
		jsonValues = returnText;
	}
	
	if(jsonValues == "SUCCESS")
	{
		document.getElementById('contact-success-text').style.display = 'block';
	}
	else
	{
		showEmailErrorOverlay(jsonValues);
	}
}

function EmailSubmitFormError(xhr, textStatus, errorThrown)
{
	if(xhr != null)
	{
		if(xhr.responseText != null)
		{
			var jsonValues = JSON.parse(xhr.responseText);
			console.log(JSON.stringify(jsonValues));
			console.log('An error occurred: ' + jsonValues.Message);
			console.log("Message: " + jsonValues.Message);
			console.log("StackTrace: " + jsonValues.StackTrace);
			console.log("ExceptionType: " + jsonValues.ExceptionType);
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

function ShowErrorEmailForm(IsFirstNameError, IsLastNameError, IsEmailError, IsMessageError)
{
	if(IsFirstNameError)
	{	
		document.getElementById('firstname-error').style.display = 'block';
		document.getElementById('contact-first-name').style.border="3px solid #cb2027";
	}
	if(IsLastNameError)
	{	
		document.getElementById('lastname-error').style.display = 'block';
		document.getElementById('contact-last-name').style.border="3px solid #cb2027";
	}
	if(IsEmailError)
	{	
		document.getElementById('email-error').style.display = 'block';
		document.getElementById('contact-email').style.border="3px solid #cb2027";
	}
	if(IsMessageError)
	{	
		document.getElementById('message-error').style.display = 'block';
		document.getElementById('contact-message').style.border="3px solid #cb2027";
	}	
}

function ClearErrorAddSubscriberForm()
{
	document.getElementById('name-error').style.display = 'none';
	document.getElementById('contact-name').style.border="none";

	document.getElementById('email-error').style.display = 'none';
	document.getElementById('contact-email').style.border="none";
}

function ShowErrorAddSubscriberForm(IsNameError, IsEmailError)
{
	if(IsNameError)
	{	
		document.getElementById('subscribe-name-error').style.display = 'block';
		document.getElementById('subscribe-name').style.border="3px solid #cb2027";
	}
	if(IsEmailError)
	{	
		document.getElementById('subscribe-email-error').style.display = 'block';
		document.getElementById('subscribe-email').style.border="3px solid #cb2027";
	}
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
		ShowErrorEmailForm(FirstName == "", LastName == "", Email == "", Message == "");
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
	var Name = document.getElementById("subscribe-name").value;
	var Email = document.getElementById("subscribe-email").value;

	ClearErrorAddSubscriberForm();

	if(Name == "" || Email == "")
	{
		ShowErrorAddSubscriberForm(Name == "", Email == "");
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
	         
	        success: AddSubscriberFormSuccess,
            error: EmailSubmitFormError
		}
	);
}