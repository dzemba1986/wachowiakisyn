$(function(){
	$('.button_create_installation').click(function(){
		$('#modal_create_installation').modal('show')
			.find('#modal_content')
			.load($(this).attr('value'));
	});
});