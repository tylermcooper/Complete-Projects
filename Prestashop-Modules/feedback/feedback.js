jQuery( document ).ready(function( $ ) {
	$( ".feedback-overlay" ).click(function() {
	  $( ".feedback-overlay" ).toggle( "slow");
	  $('.feedback-form-wrapper').toggle("slow");  
		$('#success_message').remove(); 	
	});

	$( ".feedback" ).click(function() {
		window.scrollTo(0, 0);
	  	$('.feedback-overlay').toggle("slow");
	  	$('.feedback-form-wrapper').toggle("slow");  
	});

	$( "#survey" ).click(function() {
		$('#survey-section').toggle("slow");
		$('#error_message').remove();
		$('#generic').removeClass('error');
		if (fullsurvey)
			fullsurvey = false;
		else fullsurvey = true; 
	});

	$( ".close-survey" ).click(function() {
	  	$('.feedback-overlay').toggle("slow");
	  	$('.feedback-form-wrapper').toggle("slow");
		$('#success_message').remove(); 
	});

	fullsurvey = false;
});

  function PostData() {

    if (!fullsurvey)
	{
		if (!$('#generic').val()) {
			$('#generic').addClass('error');
			$( "#error_message" ).append( "<p>Sorry, this field can't be empty!</p>" );
		}
		else {
		    $('#error_message').remove();
		    $('#generic').removeClass('error');
			// 1. Create XHR instance - Start
			var xhr;
			if (window.XMLHttpRequest) {
			  xhr = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
			  xhr = new ActiveXObject("Msxml2.XMLHTTP");
			}
			else {
			  throw new Error("Ajax is not supported by this browser");
			}


			xhr.onreadystatechange = function () {
			  if (xhr.readyState === 4) {
			      if (xhr.status == 200 && xhr.status < 300) {
			          document.getElementById('div1').innerHTML = xhr.responseText;
			      }
			  }
			}

			var email = document.getElementById("email").value;
			var name = document.getElementById("name").value;
			var generic = document.getElementById("generic").value;
			var question1 = document.getElementById("question1").value;
			var question2 = document.getElementById("question2").value; 
			var question3 = document.getElementById("question3").value; 
			var question4 = document.getElementById("question4").value; 
			var question5 = document.getElementById("question5").value;      
			var param = "email=" + email + "&name=" + name + "&question1=" + question1 + "&question2=" + question2 + "&question3=" + question3 + "&question4=" + question4 +  "&question5=" + question5 + "&generic=" + generic; 


			xhr.open('POST', '/prestashop/modules/feedback/feedback-post.php');
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send(param);


			$('.inputs').val("");
			$('#survey-section').hide("slow");
			fullsurvey = false;

			if (!$('#successful').length) {
				$( '#success_message' ).append( "<p id=\"successful\">Thank you, your comments have been received!</p>" );	
			}	

		}
    }

      
	else {
		// 1. Create XHR instance - Start
		var xhr;
		if (window.XMLHttpRequest) {
		  xhr = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
		  xhr = new ActiveXObject("Msxml2.XMLHTTP");
		}
		else {
		  throw new Error("Ajax is not supported by this browser");
		}
		// 1. Create XHR instance - End

		// 2. Define what to do when XHR feed you the response from the server - Start
		xhr.onreadystatechange = function () {
		  if (xhr.readyState === 4) {
		      if (xhr.status == 200 && xhr.status < 300) {
		          document.getElementById('div1').innerHTML = xhr.responseText;
		      }
		  }
		}
		// 2. Define what to do when XHR feed you the response from the server - Start

		var email = document.getElementById("email").value;
		var name = document.getElementById("name").value;
		var generic = document.getElementById("generic").value;
		var question1 = document.getElementById("question1").value;
		var question2 = document.getElementById("question2").value;     
		var question3 = document.getElementById("question3").value;      
		var question4 = document.getElementById("question4").value;     
		var question5 = document.getElementById("question5").value;     
		var param = "email=" + email + "&name=" + name + "&question1=" + question1 + "&question2=" + question2 + "&question3=" + question3 + "&question4=" + question4 +  "&question5=" + question5 + "&generic=" + generic; 
		// 3. Specify your action, location and Send to the server - Start 
		xhr.open('POST', '/prestashop/modules/feedback/feedback-post.php');
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(param);



		// 3. Specify your action, location and Send to the server - End



			$('.inputs').val("");
			$('#survey-section').hide("slow");
			fullsurvey = false;
			
			if (!$('#successful').length) {
				$( '#success_message' ).append( "<p id=\"successful\">Thank you, your comments have been received!</p>" );	
			}	

	}



  }