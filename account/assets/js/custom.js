$(document).ready(function()
	{
		$('#msgSend').on('submit', function(e)
			{
				e.preventDefault();
				$('#msgButton').prop('disabled', true);
				$("#output").html('Processing...');
				$(this).ajaxSubmit({
					target: '#output',
					success: afterSuccess
				});
			});
	});

function afterSuccess()
{
	$('#msgButton').prop('disabled', false); //enable submit button
}

<!--username check-->
$(document).ready(function() {
	var min_chars = 4;
	var characters_error = '<span id="blink-text-red" style="font-family:tahoma-bold;font-size:125%;padding-left:3px;color:#F00">@vex.email</span>';
	$('#check_username_availability').click(function(){
		if($('#username').val().length < min_chars){
			$('#username_availability_result').html(characters_error);
		}else{
			check_availability();
		}
	});});
function check_availability(){
	var username = $('#username').val();
	$.post("lib/check_username.php", { username: username },
		function(result){
			if(result == 1){
				$('#username_availability_result').html('<span id="blink-text-green" style="font-family:tahoma-bold;font-size:125%;padding-left:3px;color:#BFED46">@vex.email</span>');
			}else{
				$('#username_availability_result').html('<span id="blink-text-red" style="font-family:tahoma-bold;font-size:125%;padding-left:3px;color:#F00">@vex.email</span>');
			}
		});
	
}
<!--number check-->
jQuery('.numbersOnly').keyup(function () {
	this.value = this.value.replace(/[^0-9\.]/g,'');
});

<!--key check-->
jQuery('.key-input').keyup(function () {
	this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});