function EmailSubmitFormSuccess(returnText)
{
	alert('Success!\n' + returnText);
}

function EmailSubmitFormError(xhr, textStatus, errorThrown)
{
	if(xhr != null)
	{
		//console.log(xhr);
		if(xhr.responseText != null)
		{
			var jsonValues = JSON.parse(xhr.responseText);
			alert('An error occurred: ' + jsonValues.Message);

			/*
			alert("Message: " + jsonValues.Message);
			alert("StackTrace: " + jsonValues.StackTrace);
			alert("ExceptionType: " + jsonValues.ExceptionType);
			*/
		}
	}
}

function SendEmailForm()
{
	var FirstName = document.getElementById("contact-first-name").value;
	var LastName = document.getElementById("contact-last-name").value;
	var Email = document.getElementById("contact-email").value;
	var Message = document.getElementById("contact-message").value;

	if(FirstName == "" || LastName == "" || Email == ""  || Message == "")
	{	
		alert("Please fill in all the fields before submitting.");
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