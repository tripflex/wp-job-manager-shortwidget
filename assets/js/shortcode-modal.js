
jQuery(function($){
	var selection = false;
	var wp_job_manager_shortwidgetShortcodePanel = $('#wp-job-manager-shortwidget-shortcode-panel-tmpl').html();

	$('body').append(wp_job_manager_shortwidgetShortcodePanel);
	$('.media-modal-backdrop, .media-modal-close').on('click', function(){
		wp_job_manager_shortwidget_hideModal();
	})
	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			wp_job_manager_shortwidget_hideModal();
		}
	});

	// show modal
	$(document).on('click', '#wp-job-manager-shortwidget-shortcodeinsert', function(){
		if($(this).data('shortcode')){
			window.send_to_editor('['+$(this).data('shortcode')+']');
			return;
		}
				
		// autoload item
		var autoload = $('.wp-job-manager-shortwidget-autoload');
		if(autoload.length){
			wp_job_manager_shortwidget_loadtemplate(autoload.data('shortcode'));
		}
		$('#wp-job-manager-shortwidget-category-selector').on('change', function(){
			wp_job_manager_shortwidget_loadtemplate('');
			$('.wp-job-manager-shortwidget-elements-selector').hide();
			$('#wp-job-manager-shortwidget-elements-selector-'+this.value).show().val('');
		});

		$('.wp-job-manager-shortwidget-elements-selector').on('change', function(){
			wp_job_manager_shortwidget_loadtemplate(this.value);
		});

		if(typeof tinyMCE !== 'undefined'){
			if(tinyMCE.activeEditor !== null){
				selection = tinyMCE.activeEditor.selection.getContent();
			}else{
				selection = false;
			}
		}else{
			selection = false;
		}
		if(selection.length > 0){
			$('#wp-job-manager-shortwidget-content').html(selection);
		}
		$('#wp-job-manager-shortwidget-shortcode-panel').show();
	});
	$('#wp-job-manager-shortwidget-insert-shortcode').on('click', function(){
		wp_job_manager_shortwidget_sendCode();
	})
	// modal tabs
	$('#wp-job-manager-shortwidget-shortcode-config').on('click', '.wp-job-manager-shortwidget-shortcode-config-nav li a', function(){
		$('.wp-job-manager-shortwidget-shortcode-config-nav li').removeClass('current');
		$('.group').hide();
		$(''+$(this).attr('href')+'').show();
		$(this).parent().addClass('current');
		return false;
	});


});

function wp_job_manager_shortwidget_loadtemplate(shortcode){
	var target = jQuery('#wp-job-manager-shortwidget-shortcode-config');
	if(shortcode.length <= 0){
		target.html('');
	}
	target.html(jQuery('#wp-job-manager-shortwidget-'+shortcode+'-config-tmpl').html());
}

function wp_job_manager_shortwidget_sendCode(){

	var shortcode = jQuery('#wp-job-manager-shortwidget-shortcodekey').val(),
		output = '['+shortcode,
		ctype = '',
		fields = {};
	
	if(shortcode.length <= 0){return; }

	if(jQuery('#wp-job-manager-shortwidget-shortcodetype').val() === '2'){
		ctype = jQuery('#wp-job-manager-shortwidget-default-content').val()+'[/'+shortcode+']';
	}
	jQuery('#wp-job-manager-shortwidget-shortcode-config input,#wp-job-manager-shortwidget-shortcode-config select,#wp-job-manager-shortwidget-shortcode-config textarea').not('.configexclude').each(function(){
		if(this.value){
			// see if its a checkbox
			var thisinput = jQuery(this),
				attname = this.name;
			
			if ( attname == 'per_page' && this.value == '0' ) {
				return;
			}

			if(thisinput.prop('type') == 'checkbox'){
				if(!thisinput.prop('checked')){
					return;
				}
			}
			if(thisinput.prop('type') == 'radio'){
				if(!thisinput.prop('checked')){
					return;
				}
			}

			if(attname.indexOf('[') > -1){
				attname = attname.split('[')[0];
				var newloop = {};
				newloop[attname] = this.value;
				if(!fields[attname]){
					fields[attname] = [];
				}
				fields[attname].push(newloop);
			}else{
				var newfield = {};
				fields[attname] = this.value;
			}
		}
	});
	for( var field in fields){
		if(typeof fields[field] == 'object'){
			for(i=0;i<fields[field].length; i++){
				output += ' '+field+'_'+(i+1)+'="'+fields[field][i][field]+'"';
			}
		}else{
			output += ' '+field+'="'+fields[field]+'"';
		}
	}
	wp_job_manager_shortwidget_hideModal();
	window.send_to_editor(output+']'+ctype);

}
function wp_job_manager_shortwidget_hideModal(){
	jQuery('#wp-job-manager-shortwidget-shortcode-panel').hide();
	wp_job_manager_shortwidget_loadtemplate('');
	jQuery('#wp-job-manager-shortwidget-elements-selector').show();
	jQuery('.wp-job-manager-shortwidget-elements-selector').val('');	
	jQuery('#wp-job-manager-shortwidget-category-selector').val('');
}
