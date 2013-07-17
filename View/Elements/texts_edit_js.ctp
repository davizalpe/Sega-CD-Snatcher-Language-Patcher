<?php
$type = Configure::read('Snatcher.Characters.type');

switch ($character_id)
{
	case $type['menu']['id']:
		$text_function = "
		var limit = ".$type['menu']['limit']."; // Value in pixels from core.php Snatcher.Characters.type
		var maxLimit = ".$type['menu']['maxLimit'].";
		var text = $('#newtext').val();
		var remaining = pixel_lenght(text);
		
		var remaining_txt = '<span class=\"remaining\">'+(limit-remaining)+'</span>';
		var limit_txt  = '<span class=\"limit\">'+limit+'</span>';
				
		var message_txt = '".__('There are $1 pixels left of $2 (up to $3 out of the blue bar)', true)."';
		
		$('#counter').html(message_txt.replace('$1', remaining_txt).replace('$2', limit_txt).replace('$3', maxLimit));
		
		if(remaining <= limit){				
			$('.remaining').css('color','green');
			$('#newtext').css('color','');
		}else if(remaining <= maxLimit){		
			$('.remaining').css('color','orange');
			$('#newtext').css('color','orange');
		}else{
			$('.remaining').css('color','red');
			$('#newtext').css('color','red');
		}";		
		break;
		
	case $type['response_number']['id']:
		$text_function = "
		var limit = ".$type['response_number']['limit']."; // Value in chars		
		var text = $('#newtext').val();
		var remaining = text.length;
		
		var remaining_txt = '<span class=\"remaining\">'+(limit-remaining)+'</span>';
		var limit_txt  = '<span class=\"limit\">'+limit+'</span>';
				
		var message_txt = '';				
		
		if(remaining <= limit){		
			$('.remaining').css('color','green');
			$('#newtext').css('color','');				
			message_txt = '".__('There are $1 characters of $2', true)."';
			message_txt = message_txt.replace('$1', remaining_txt).replace('$2', limit_txt);
		}else{
			$('.remaining').css('color','red');
			$('#newtext').css('color','red');					
			message_txt = '".__('Answer can not be more than $2 characters', true)."';
			message_txt = message_txt.replace('$2', limit_txt);	
		}					
		var valid = /^[0-9¡#]+$/;					
		if ( !valid.test(text) ){
			$('.remaining').css('color','red');
			$('#newtext').css('color','red');
			message_txt += '<br/>".__("It only accepts numbers, \"¡\" and \"#\"")."';
		}		
		$('#counter').html(message_txt);		
		";
		break;
		
	case $type['response_text']['id']:
		$text_function = " 
		var limit = ".$type['response_text']['limit']."; // Value in chars 		
		var text = $('#newtext').val();
		var remaining = text.length;
		
		var remaining_txt = '<span class=\"remaining\">'+(limit-remaining)+'</span>';
		var limit_txt  = '<span class=\"limit\">'+limit+'</span>';
				
		var message_txt = '';				
		
		if(remaining <= limit){				
			$('.remaining').css('color','green');
			$('#newtext').css('color','');				
			message_txt = '".__('There are $1 characters of $2', true)."';
			message_txt = message_txt.replace('$1', remaining_txt).replace('$2', limit_txt);		
		}else{
			$('.remaining').css('color','red');
			$('#newtext').css('color','red');					
			message_txt = '".__('Answer can not be more than $2 characters', true)."';
			message_txt = message_txt.replace('$2', limit_txt);
		}							
		var valid = /^[A-Z -.\']+$/;	
		if ( !valid.test(text) ){
			$('.remaining').css('color','red');
			$('#newtext').css('color','red');
			message_txt += '<br/>".__("It only accepts upper case, \"-\", \".\" and single quotation marks")."';
		}		
		$('#counter').html(message_txt);
		";			
		break;
		
	default:
		$text_function = " 
		var text = $('#newtext').val().replace(/<[0-9]+>/, '-');
		var remaining = text.length;
		
		var remaining_txt = '<span class=\"remaining\">'+remaining+'</span>';
		var message_txt = '".__('$1 characters written', true)."';
		$('#counter').html(message_txt.replace('$1', remaining_txt));";		
		break;
}
?>
<?php 
$this->Js->buffer("
	function checkChars(){
		".$text_function."
	}
	checkChars();");
$this->Js->get('#newtext')->event('keyup', "checkChars();");
?>