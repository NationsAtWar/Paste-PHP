$(function() {
	$("#token_form").submit(function(e) {
		e.preventDefault();
		
		if(!validate($("#token_form"))) {
			return success(false);
		}
		
		var dataString = 'rpc=1&' + $("#token_form").serialize();
		var action = false;
		$.ajax({
			type: "POST",
			url: "index.php",
			data: dataString,
			context: $(this),
			success: function(data) {
				try {
					ret = jQuery.parseJSON( data );
				} catch(e) {
					return false;
				}
				console.log(ret);
				if(ret.result == "error") {
					return success(false);
				} else if(ret.result == "success") {
					return success(true);
				} else {
					return status(ret.result);
				}
			}
		});
	});
});

function status(res) {
	if(res == "token") {
		$("p#result").text("Your token did not match");
	}
	if(res == "down") {
		$("p#result").text("The Minecraft server is down, but rest assured it'll be back up shortly and you will be automatically upgraded.");
	}
	return false;
}

function validate(form) {
	return true;
}

function success(win) 
{
	if(win)
		window.location = "http://www.nationsatwar.org/panel/";
	else
		$("p#result").text("Your token didn't match or wasn't set properly.");
	return true;
}