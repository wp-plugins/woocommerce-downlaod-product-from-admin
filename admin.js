jQuery(document).ready(function($){
		$('.dll-link-btn').on('click' , function(e){
		e.preventDefault();
		var	wdpfa_post_id = $(this).siblings('.wdpfa_post_id').val(),
			wdpfa_ajax_img = $(this).siblings('.ajax-loader');
			wdpfa_ajax_img.show();
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {action: 'wdpfa_dll_file', post_id: wdpfa_post_id},
			})
			.done(function(data) {
				wdpfa_ajax_img.hide();
				console.log("success");
			})
			.fail(function() {
				wdpfa_ajax_img.hide();
				alert('Problem,try again.');
				console.log("error");
			});
			
	});
});