jQuery(document).ready(function () {
	jQuery('#linkedposttypes .linkedposttypecheckbox').change(function() {
		var fs = jQuery(this).parents('fieldset').attr('id');
		var val = jQuery(this).val();
		var c = jQuery(this).attr('checked');
		
		jQuery('#'+fs+' .moreoptions').toggle();
		jQuery('#'+fs).toggleClass("active");
		
		if (c == "checked") {
			jQuery('#'+fs+' .moreoptions input').val(1);
		} else {
			jQuery('#'+fs+' .moreoptions input').val(0);
		}
	});
	jQuery('.textfield_withselect_container select').each(function () {
		var val = jQuery(this).val();
		if (val != "") {
			jQuery(this).parents('.textfield_withselect_container').addClass('chosen');
			jQuery(this).parents('.textfield_withselect_container input').hide();
		}
	});
	jQuery('.textfield_withselect_container select').change(function () {
		var val = jQuery(this).val();
		if ((val == '+ add new') || (val == '')) {
			jQuery(this).parents('.textfield_withselect_container').removeClass('chosen');
			jQuery(this).parents('.textfield_withselect_container input').show();
			jQuery(this).parents('.textfield_withselect_container').children('input').focus();
		} else {
			jQuery(this).parents('.textfield_withselect_container').addClass('chosen');
			jQuery(this).parents('.textfield_withselect_container input').hide();
		}
	});
	
	jQuery('.insert_into_template_var').click(function () {
		var parent = jQuery(this).parents('.propsrch_option_question');
		if (parent.find('.variable_chooser').val() != "-") {
			var selectbox = parent.find('.variable_chooser');
			var variable = " *|"+selectbox.val()+"|*";
			console.log(variable);
			
			var $txt = parent.children('.template_destination');
			var caretPos = $txt[0].selectionStart;
			var textAreaTxt = $txt.val();
	//		var txtToAdd = "stuff";
	        $txt.val(textAreaTxt.substring(0, caretPos) + variable + textAreaTxt.substring(caretPos) );
	
			//jQuery('#resulttemplate_textarea').val(jQuery().val()+variable);
	        selectbox.val("");
		}
		return false;
	});
	jQuery('.insert_into_template_func').click(function () {
		var parent = jQuery(this).parents('.propsrch_option_question');
		if (parent.find('.function_chooser').val() != "-") {
			var selectbox = parent.find('.function_chooser');
			var func = " *{"+selectbox.val()+"}*";
			console.log(func);
			
			var $txt = parent.children('.template_destination');
			var caretPos = $txt[0].selectionStart;
			var textAreaTxt = $txt.val();
	//		var txtToAdd = "stuff";
			$txt.val(textAreaTxt.substring(0, caretPos) + func + textAreaTxt.substring(caretPos) );
			
			//jQuery('#resulttemplate_textarea').val(jQuery().val()+variable);
			selectbox.val("");
		}
		return false;
	});
	
	
	
	var added = 0;
	if (typeof sortable === "function") {
		jQuery('#termadminlist-ul').sortable();
		jQuery('#termadminlist-ul').disableSelection();
	}
	
	jQuery('.termadminlist .add').click(function () {
		var value = jQuery('#proptypeadd').val();
		added++;
		var html = '<li data-id="0">';
		html += "\n"+'	<input type="hidden" class="termname update" name="termadd[]" value="'+value+'" id="update_add_'+added+'"/>';
		html += "\n"+'	<input type="hidden" class="termname update" name="termadd_type[]" value="property_type"/>';
		html += "\n"+'	<span class="termname proptype">'+value+'</span>';
		html += "\n"+'	<span class="termopt"><a class="button remove" href="#">Remove</a> <a class="button update" href="#">Update</a> <a class="button cancelupdate" href="#">Cancel</a></span>';
		html += "\n"+'</li>';
		jQuery('#termadminlist-ui').append(html);
		jQuery('#proptypeadd').val("");
		
		//jQuery('#termadminlist-ul').sortable();
		return false;
	});
	jQuery('.termadminlist').on('click','.update',function () {
		var lielem = jQuery(this).parents('span.termopt').parents('li');
		lielem.children('input.termname').attr('type','textfield');
		lielem.children('input.termname').show();
		lielem.children('span.termname').hide();
		lielem.children('.termopt').children('a.update').hide();
		lielem.children('.termopt').children('a.cancelupdate').show();
		
		return false;
	});
	jQuery('.termadminlist').on('click','.cancelupdate',function () {
		var lielem = jQuery(this).parents('span.termopt').parents('li');
		lielem.children('input.termname').attr('type','hidden');
		lielem.children('input.termname').val(lielem.children('span.termname').html());
		lielem.children('span.termname').show();
		lielem.children('.termopt').children('a.update').show();
		lielem.children('.termopt').children('a.cancelupdate').hide();
		
		return false;
	});
	jQuery('.termadminlist .remove').click(function () {
		var lielem = jQuery(this).parents('span.termopt').parents('li');
		var doit = confirm("Are you sure?");
		if (doit == true) {
			lielem.addClass('removed');
			lielem.html('<input name="termremove[]" value="'+lielem.data('id')+'" type="hidden"/>');
		}
		
		return false;
	});
	
	jQuery('[name=propsrch_latlng]').after("<p><a href=\"#\" class=\"lookuplatlng button\">Get coordinates</a></p>");
	jQuery('#row-latlng').on('click','.lookuplatlng', function () {
		getCoordinates();
		return false;
	});
	var address = jQuery('#textarea-address').val();
	jQuery('#textarea-address').blur(function () {
		if (jQuery(this).val() != address) {
			var c = confirm("Get new coordinates?");
			if (c) {
				getCoordinates();
			}
			address = jQuery(this).val();
		}
	});
	
	jQuery('#regneratecache').click(function () {
		console.log("Click");
		var container = jQuery(this).parents('div');
		container .addClass('regenerating');
		var starttime = new Date().getTime() / 1000;

		jQuery.get('?page=property-search&generatecache=true', function (data) {
			console.log("Regenerating caches"); 
			container .removeClass('regenerating');
			if (data == "END") {
				container.addClass('success');
			} else {
				container.addClass('error');
			}
			var timefinished = new Date();
			var endtime = timefinished.getTime() / 1000;
			var duration = endtime - starttime;
			var endtime = (timefinished.getHours()<10?'0':'') + timefinished.getHours()+':'+(timefinished.getMinutes()<10?'0':'') + timefinished.getMinutes();
			
			jQuery('#cachefeedback').html('Completed at '+endtime+' - process took '+duration.toFixed(2)+' seconds');
			setInterval(function () {
				container.removeClass('error');
				container.removeClass('success');
				jQuery('#cachefeedback').html("");
			}, 10000);
		});
		return false;
	});
	
});
function getCoordinates () {
	var address = jQuery('#textarea-address').val().replace(/\n/g, '%0A');
	jQuery('[name=propsrch_latlng]').val("...");
	jQuery.ajax({
		type: "GET",
		url: propsrch_uri+'propsrchaj.php?getlatlng='+address,
		success: function(response) {
			jQuery('[name=propsrch_latlng]').val(response);
		}
	});
	
}
function insertAtCaret(areaId, text) {
	var txtarea = document.getElementById(areaId);
	if (!txtarea) { return; }

	var scrollPos = txtarea.scrollTop;
	var strPos = 0;
	var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
		"ff" : (document.selection ? "ie" : false ) );
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart ('character', -txtarea.value.length);
		strPos = range.text.length;
	} else if (br == "ff") {
		strPos = txtarea.selectionStart;
	}

	var front = (txtarea.value).substring(0, strPos);
	var back = (txtarea.value).substring(strPos, txtarea.value.length);
	txtarea.value = front + text + back;
	strPos = strPos + text.length;
	if (br == "ie") {
		txtarea.focus();
		var ieRange = document.selection.createRange();
		ieRange.moveStart ('character', -txtarea.value.length);
		ieRange.moveStart ('character', strPos);
		ieRange.moveEnd ('character', 0);
		ieRange.select();
	} else if (br == "ff") {
		txtarea.selectionStart = strPos;
		txtarea.selectionEnd = strPos;
		txtarea.focus();
	}

	txtarea.scrollTop = scrollPos;
}