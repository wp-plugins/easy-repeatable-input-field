jQuery(document).ready(function($) {
	
	jQuery('#mf-meta-boxes .inside').sortable();

	jQuery('#mf-add-field ').click( function() {
		jQuery('#mf-meta-boxes .inside').append('<p><input type="text" name="series[]" /> <a href="#" class="mf-remove">Remove me</a></p>');
		return false;		
	});
	
	jQuery('.mf-remove').live('click', function() {
		$(this).parent('p').remove();
		return false;
	});
}); //ready