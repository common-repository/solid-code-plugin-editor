jQuery(document).ready(function(){
	jQuery('.scpe_inside_right ul ul').each(function(itm){
		if(jQuery(this).hasClass('scpe_show')){
			jQuery(this).parent('li').prepend('<a class="scpe_nav" id="scpe_a_' + itm + '" href="JavaScript:scpe_expand(' + itm + ');">-</a> ');
		}else{
			jQuery(this).parent('li').prepend('<a class="scpe_nav" id="scpe_a_' + itm + '" href="JavaScript:scpe_expand(' + itm + ');">+</a> ');	
		}
	});
});

function scpe_expand(itm){
	var htm = jQuery('#scpe_a_' + itm).html();
	if(htm=='+'){
		jQuery('.scpe_inside_right ul ul:eq(' + itm + ')').slideDown();
		jQuery('#scpe_a_' + itm).html('-');
	}else{
		jQuery('.scpe_inside_right ul ul:eq(' + itm + ')').slideUp();
		jQuery('#scpe_a_' + itm).html('+');
	}
}