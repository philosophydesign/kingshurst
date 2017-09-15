<?php
//$_SESSION['philosri_nonce'] = array();
if (empty($_SESSION['philosri_nonce'])) {
	$_SESSION['philosri_nonce'] = array();
}

class GesForms {
	
	// Methods 
	// --- makerow
	// --- output
	// --- make_body
	// --- make_datepicker
	// --- make_datehidden
	// --- make_html
	// --- make_mediumtext
	// --- make_money
	// --- make_money_excvat
	// --- make_ref_select
	// --- make_smalltext
	
	var $formname;
	var $label;
	var $manyMode = 0;
	var $values;
	var $fieldClasses = array();
	var $justshowthevalues = 0;	
	var $dontuselabels = 0;	
	var $tabindex_start = 0;
	var $unique;
	var $counter = 0;
	var $fieldsAreArray;
	var $availableFields;
	var $mandatoryMarker;
	var $fieldPrefix;
	var $placeholder;
	var $generatedfields;
	function __construct()

	 {
		$this->mandatoryMarker = '<span class="mandatory">*</span>';
	 	$this->formname = rand(100,999);
	 	$this->fieldsAreArray = array(
 			'checkrefset',
 			'checkset',
 			'multiselect',
 			'radioset'
	 	);
	 	$this->availableFields = array(
	 			'appendtext',
	 			'attribute',
	 			'attribute_c',
	 			'attribute_c_a',
	 			'body',
	 			'checkbox',
	 			'checkrefset',
	 			'checkset',
	 			'datepicker',
	 			'double',
	 			'email',
	 			'file',
	 			'hidden',
	 			'html',
	 			'mediumtext',
	 			'money',
	 			'money_excvat',
	 			'multiselect',
	 			'multitextbox',
	 			'noornotsure',
	 			'password',
	 			'percentage',
	 			'radioset',
	 			'ref_select',
	 			'select',
	 			'smalltext',
	 			'textfield',
	 			'textstring',
	 			'time',
	 			'wrappedtextstring',
	 			'yesorno'
	 	);
	 	$this->generatedfields = array();
	 }
 	
	function makerow (
		$label, 				//1 - Human readable / instructions - uses label tag
		$ref, 					//2 - The mysql column name or computer friendly variable name
		$mandatory = false, 	//3 - Whether this row is mandatory.  This is dealt with individually in the controller
		$val = '', 				//4 - The preloaded value of the row
		$error = '', 			//5 - Whether this row is to be highlighted as an error
		$type='', 				//6 - The type of row to display (defaults to textfield)
		$options = array(), 	//7 - If this is a select/radioset/checkbox-list, use an array [ array('Col'=>'Val') ] 
		$readonly = false, 		//8 - To stop the user writing to the field use TRUE
		$rowindex = 0			//9 - This assigns a unique identifier to the row (default is incremented X from 'output' function
		) 
		
		{
			
			$r = '';
			#echo $label.' -- '.$val.'<br>';
// 			$this->counter++;
			if (empty($type)) {
				$type = 'textfield';
			}
			//$r .= $type;
			$class = '';
			$class .= ($mandatory == true) ? 'mandatory ' : '';
			$class .= (!empty($error)) ? 'error ' : '';
			$class .= (!empty($readonly)) ? 'readonly ' : '';
			$class .= (empty($value)) ? 'add ' : '';
			
			if (isset($this->fieldClasses[$ref])) {
				$class .= ' '.$this->fieldClasses[$ref].' ';
			}
			
			$this->label = '';
			$this->placeholder = $label;
			$rowid = 'row-'.$ref;
			if (!empty($this->unique)) {
				$class .= $rowid;
				$rowid .= $this->unique;
				#echo '| '.$this->unique.'<br>';
			}
			#$rowid .= '-'.$this->formname;
			
			if (in_array($type, array('attribute','attribute_c'))) {
				$this->label = $label;
				$r .= '<div class="form-row rowtype-'.$type.' '.$class.' fri-'.$rowindex.'" id="'.$rowid.'">';
				
			} else if ($type == 'attribute_c_a') {
				
				
			} else if (!in_array($type, array('hidden','html','textstring'))) {
				$r .= "\n".'<div class="form-row rowtype-'.$type.' '.$class.' fri-'.$rowindex.'" id="'.$rowid.'">';
				if ($this->dontuselabels) {
					$r .= '<p class="label">'.$label.'</p>';
				} else {
					$r .= '<label for="input-'.$ref.'-'.$this->formname.'" class="initial">'.$label.'</label>';
				}
				
			} else if (in_array($type, array('html','textstring'))) {
				$r .= "\n".'<div class="form-row rowtype-'.$type.' '.$class.' fri-'.$rowindex.'" id="'.$rowid.'"><div class="nonlabellabel">'.$label.'</div>';
				
			} else if ($type == 'appendtext') {
				$r .= "\n".'<div class="form-row rowtype-'.$type.' '.$class.' fri-'.$rowindex.'" id="'.$rowid.'">';
			}
			$readonly = ($readonly == true) ? ' readonly="readonly" ' : '';
			if ($this->justshowthevalues) {
				if ($type == 'yesornobinary') {
					$val = ($val == 1) ? 'Yes' : 'No';
				} else 
				if ($type == 'checkbox') {
					$val = ($val == 1) ? 'True' : 'False';
				}
				$type = 'wrappedtextstring';
			}
			$func = 'make_'.$type;
			//$r .= $this->counter;
			if (method_exists($this, $func)) {
				#echo $func.'<Br>';
				#echo $func.' is good<br>';
				$r .= $this->$func($ref,$val,$options,$readonly);
			} else {
				#echo $func.' has no function <br>';
				// echo 'V'.$val.'<br>';
				//$r .= $val;
				
				$r .= $this->make_textfield($ref, $val, $readonly);
			}
			if (!in_array($type, array('hidden','attribute_c'))) {
				$r .= "\n".'</div>'."\n";
			}
			return($r); 
		}
		function makeMany ($ref) {
			if ($this->manyMode) {
				if ($this->counter == 0) {
					return ($ref.'[]');
				} else {
					return ($ref.'['.$this->counter.']');
				}
			} else {
				return ($ref);
			}
		}
		function make_email ($ref,$val,$options,$readonly,$saywhat='Confirm email address', $noconfirm=false) {
			$value = (!empty($val)) ? ' value="'.$val.'"' : ' value=""';
			$name = $this->makeMany($ref);
			if (!empty($tabindex)) {
				$tabindex = ' tabindex="'.$tabindex.'"';
			}
			$r = '<input id="input-'.$ref.'-'.$this->formname.'" type="text" name="'.$this->fieldPrefix.$name.'"'.$value.' '.$readonly.' '.$tabindex.'/>';
			$this->generatedfields[] = $this->fieldPrefix.$name;
			if (!$noconfirm) {
				$star = ($readonly) ? '' : '';
				$r .= '<br><label for="input-'.$ref.'-'.$this->formname.'_confirm">'.$saywhat.'</label><input id="input-'.$ref.'-'.$this->formname.'_confirm" type="text" name="'.$this->fieldPrefix.$name.'"'.$value.' '.$readonly.' '.$tabindex.'/>';
				$this->generatedfields[] = $this->fieldPrefix.$name;
			}
			return($r);
		}
	function make_textfield($ref,$val='',$readonly='', $tabindex='') {
		$value = (!empty($val)) ? ' value="'.$val.'"' : ' value=""';
		$name = $this->makeMany($ref);
		if (!empty($tabindex)) {
			$tabindex = ' tabindex="'.$tabindex.'"';
		}
		$r = '<input placeholder="'.$this->placeholder .'" id="input-'.$ref.'-'.$this->formname.'" type="text" name="'.$this->fieldPrefix.$name.'"'.$value.' '.$readonly.' '.$tabindex.'/>';
		$this->generatedfields[] = $this->fieldPrefix.$name;
		return ($r);
	}
	function make_textfield_withselect ($ref, $val, $options, $readonly) {
		$output .= '<div class="textfield_withselect_container">';
		$output .= '<p class="propsrchtws_sel">Choose existing:</p>';
		$output .= $this->make_select($ref.'_choose', $val, $options, $readonly);
		$output .= '<p class="propsrchtws_add">Add new:</p>';
		$output .= $this->make_textfield($ref, '', $options, $readonly);
		
		$output .= '</div>';
		return($output);
	}
	function make_multitextbox($ref, $val, $options, $readonly='') {
		if ((empty($options['count'])) || (empty($options['options']))) {
			return false;
		}
		$output = '<div class="multitext-container">';
		
		$cols = count($options['options']);
		$c = 1;
		foreach ($options['options'] as $ref => $o) {
			$output .= '<div class="multitext-col mulitextbox-'.$ref.'">';
			$output .= '<label class="multitext-header">'.$o.'</label>';
			$x = 1;
			$tabindex = $this->tabindex_start + $c;
			
			while ($x <= $options['count']) {
				if ($options['count'] > 1) {
					$ref2 = $ref.'_'.$x;
				} else {
					$ref2 = $ref;
				}
				$val = (isset($this->values[$ref2])) ? $this->values[$ref2] : '';
				$output .= '<span class="visible-xs"></span>';
				$output .= $this->make_textfield($ref2, $val, 0, $tabindex);
				$tabindex = $tabindex + $cols;
				$x++;
			}
			$c++;
			$output .= '</div>';
		}
		$this->tabindex_start = $this->tabindex_start + ($cols * $options['count']);
		#exit;
		$output .= '</div>';
		return($output);
	}
	
	function make_password($ref,$val='',$readonly='') {
		$value = (!empty($val)) ? ' value="'.$val.'"' : ' value=""';
		$name = $this->makeMany($ref);
		$r = '<input id="input-'.$ref.'-'.$this->formname.'" type="password" name="'.$this->fieldPrefix.$name.'"'.$value.' '.$readonly.' />';
		$this->generatedfields[] = $this->fieldPrefix.$name;
		return ($r);
	}
	function make_datepicker ($ref,$val,$options=array(),$readonly='') {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$r = '<input id="input-'.$ref.'-'.$this->formname.'" class="large" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/>';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		if (empty($readonly)) {
		$r .= '<script>
	$(function() {
		$( "#input-'.$ref.'-'.$this->formname.'" ).datepicker({ dateFormat: "dd/mm/yy" , changeYear: true, changeMonth: true,yearRange: \'1900:2050\',});
	});
	</script>';
		}
		
		return($r);
		
	}
	function make_appendtext ($ref,$val,$options,$readonly) {
		return($val);
	}
	function make_html ($ref,$val,$options,$readonly) {
		return($val);
	}
	function make_textstring ($ref,$val,$options,$readonly) {
		return($val);
	}
	function make_wrappedtextstring ($ref,$val,$options,$readonly) {
		return('<p class="uservalue">'.nl2br($val).'</p>');
	}
	function make_hidden ($ref,$val,$options,$readonly) {
		$name = $this->makeMany($ref);
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$name;
		return('<input id="input-'.$ref.'-'.$this->formname.'" type="hidden" name="'.$this->fieldPrefix.$name.'"'.$value.'/>');
	}
	function make_mediumtext ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('<input id="input-'.$ref.'-'.$this->formname.'" class="medium" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/>');
	}
	function make_smalltext ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('<input id="input-'.$ref.'-'.$this->formname.'" class="small" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/>');
	}
	function make_double ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('<input id="input-'.$ref.'-'.$this->formname.'" class="medium" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/>');
	}
	function make_money ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('&pound; <input id="input-'.$ref.'-'.$this->formname.'" class="medium" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/> inc VAT');
	}
	function make_percentage ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('<input id="input-'.$ref.'-'.$this->formname.'" class="medium" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/> %');
	}
	function make_money_excvat ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('&pound; <input id="input-'.$ref.'-'.$this->formname.'" class="medium" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.'/>');
	}
	function make_ref_select ($ref,$val,$options,$readonly='',$multi=0) {
		
		$readonly = (!empty($readonly)) ? ' disabled="disabled"' : '';
		$output = $val;
		$output = '<select';
		if ($multi) {
			$output .= ' multiple';
			$ref .= '[]';
			$name .= '[]';
		} 
		$output .= ' id="select-'.$ref.'" name="'.$this->fieldPrefix.$ref.'"'.$readonly.'>';
		if (isset($this->placeholder[$ref])) {
			$output .= '<option value="">'.$this->placeholder[$ref].'</option>';
		} else {
			$output .= '<option value="">--</option>';
		}
		if (!empty($options)) {
			foreach ($options as $id=>$o) {
				if (is_array($val)) {
					$sel = (in_array($id, $val)) ? ' selected="selected"' : '';
				} else {
					$sel = ($id == $val) ? ' selected="selected"' : '';
				}
				$output .= '<option value="'.$id.'"'.$sel.'>'.$o.'</option>';
			}
		}
		$output .= '</select>';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return($output);
	}
	
	function make_multiselect ($ref,$val,$options,$readonly='') {
		$output = $this->make_select($ref,$val,$options,$readonly,1);
		return($output);
	}
	function make_multirefselect ($ref,$val,$options,$readonly='') {
		$output = $this->make_ref_select($ref,$val,$options,$readonly,1);
		return($output);
	}
	function make_select ($ref,$val,$options,$readonly='',$multi=0) {
		$name = $this->makeMany($ref);
		$readonly = (!empty($readonly)) ? ' disabled="disabled"' : '';
		$output = '<select';
		if ($multi) {
			$output .= ' multiple';
			$ref .= '[]';
			$name .= '[]';
		}
		$output .= ' id="select-'.$ref.'" name="'.$this->fieldPrefix.$name.'"'.$readonly.'>';
		if (isset($this->placeholder)) {
			$output .= '<option value="">'.$this->placeholder.'</option>';
		} else {
			$output .= '<option value="">--</option>';
		}
		if (is_array($options)) {
			$x = 1;
// 			ppr($options);
			foreach ($options as $o) {
				if (!empty($val)) {
					if ($multi) {
						$sel = in_array($o,$val) ? 'selected="selected"' : '';
					} else {
						$sel = ($val == $o) ? 'selected="selected"' : '';
					}
				}
				$output .= '<option value="'.$o.'"'.$sel.'>'.$o.'</option>';
				$x++;
			}
		}
		$output .= '</select>';
		$this->generatedfields[] = $this->fieldPrefix.$name;
		return($output);
	}
	function make_time ($ref,$val,$options,$readonly='') {
		$val = explode(':',$val);
		$options = array();
		$x = 0;
		while($x < 24) {
			$options[] = sprintf("%02d", $x);
			$x++;
		}
		$name = $this->makeMany($ref);
		$readonly = (!empty($readonly)) ? ' disabled="disabled"' : '';
		$output = '<select id="select-'.$ref.'-hour" name="'.$this->fieldPrefix.$name.'-hour"'.$readonly.'>';
		$output .= '<option value="">--</option>';
		foreach ($options as $o) {
			$sel = ($val[0] == $o) ? 'selected="selected"' : '';
			$output .= '<option value="'.$o.'"'.$sel.'>'.$o.'</option>';
		}
		$output .= '</select>';
		
		$this->generatedfields[] = $this->fieldPrefix.$name;
		
		$x = 0;
		$options = array();
		while($x < 60) {
			$options[] = sprintf("%02d", $x);
			$x = $x + 15;
		}
		$output .= '<select id="select-'.$ref.'-min" name="'.$this->fieldPrefix.$name.'-min"'.$readonly.'>';
		$output .= '<option value="">--</option>';
		foreach ($options as $o) {
			$sel = ($val[1]== $o) ? 'selected="selected"' : '';
			$output .= '<option value="'.$o.'"'.$sel.'>'.$o.'</option>';
		}
		$output .= '</select>';
		$this->generatedfields[] = $this->fieldPrefix.$name;
		return($output);
	}
	function make_body ($ref,$val='',$readonly=0) {
		$ref = $this->makeMany($ref);
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return('<textarea name="'.$this->fieldPrefix.$ref.'" id="textarea-'.$ref.'"'.$readonly.'>'.$val.'</textarea>');
	}
	function make_attribute ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$r = '<input id="input-'.$ref.'-'.$this->formname.'-label" type="text" name="'.$this->fieldPrefix.$ref.'" value="'.$this->label.'" readonly="readonly" class="attribute"/>';
		$r .= ' <input id="input-'.$ref.'-'.$this->formname.'" type="text" name="'.$this->fieldPrefix.$ref.'"'.$value.$readonly.' />';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		
		return($r);
	}
	function make_attribute_c ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$r = '<input id="input-'.$ref.'-'.$this->formname.'" type="text" name="'.$this->fieldPrefix.$ref.'" value="'.$val.'" '.$readonly.'  class="attribute"/>';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		
		return($r);
	}
	function make_attribute_c_a ($ref,$val,$options,$readonly) {
		$value = (!empty($val)) ? ' value="'.$val.'"' : '';
		$r = ' <input id="input-'.$ref.'-'.$this->formname.'" type="text" name="'.$this->fieldPrefix.$ref.'" value="'.$val.'"  '.$readonly.'/>';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return($r);
	}
	function make_file ($ref,$val,$options,$readonly) {
		$output = '<span class="currentfile">Current: <strong>'.basename($val).'</strong></span>';
		$output .= '<input name="'.$this->fieldPrefix.$ref.'" id="file-'.$ref.'"'.$readonly.' type="file" />';
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		return($output);
	}
	function make_radioset($ref, $val, $options, $readonly= '') {
		$output = '<div class="radioset">';
		
		foreach ($options as $opt) {
			$id = $ref.'-'.str_replace(' ','',ucwords($opt));
			$sel = ($opt == $val) ? ' checked="checked" ' : '';
			$output .= '<div class="radioset-duo">';
			$output .= '<input value="'.$opt.'" type="radio" name="'.$this->fieldPrefix.$ref.'" id="'.$id.'"'.$sel.'>';
			$output .= '<label for="'.$id.'">'.$opt.'</label>';
			$output .= '</div>';
			$this->generatedfields[] = $this->fieldPrefix.$ref;
		}
		$output .= '</div>';
		return($output);
	}
	function make_radiorefset($ref, $val, $options, $readonly= '') {
		$output = '<div class="radioset">';
		
		foreach ($options as $k=>$opt) {
			$id = $ref.'-'.str_replace(' ','',ucwords($opt));
			$sel = ($k == $val) ? ' checked="checked" ' : '';
			$output .= '<div class="radioset-duo">';
			$output .= '<input value="'.$k.'" type="radio" name="'.$this->fieldPrefix.$ref.'" id="'.$id.'"'.$sel.'>';
			$output .= '<label for="'.$id.'">'.$opt.'</label>';
			$output .= '</div>';
			
		}
		$this->generatedfields[] = $this->fieldPrefix.$ref;
		$output .= '</div>';
		return($output);
	}
	function make_checkset($ref, $val, $options, $readonly= '') {
		if (!empty($options)) {
			$output = '<span class="checkset">';
			foreach ($options as $opt) {
				$id = $ref.'-'.str_replace(' ','',ucwords($opt));
				if (is_array($val)) {
					$checked = (in_array($opt,$val)) ? ' checked="checked"' : '';
				} else {
					$checked = '';
				}
				$output .= '<input value="'.$opt.'" type="checkbox" name="'.$this->fieldPrefix.$ref.'[]" id="'.$id.'"'.$checked.'>';
				$output .= '<label for="'.$id.'">'.$opt.'</label>';
				$this->generatedfields[] = $this->fieldPrefix.$ref.'[]';
			}
			$output .= '</span>';
			return($output);
		} else {
			return('');
		}
	}
	function make_checkrefset($ref, $val, $options, $readonly= '') {
		
		$output = '<span class="checkset">';
		if (is_string($val)) {
			$val = json_decode($val);
		}
		if (!empty($options)) {
			foreach ($options as $k => $opt) {
				$id = $ref.'-'.$k;
				if (is_array($val)) {
					$checked = (in_array($k,$val)) ? ' checked="checked"' : '';
				} else {
					$checked = '';
				}
				$output .= '<input value="'.$k.'" type="checkbox" name="'.$this->fieldPrefix.$ref.'[]" id="'.$id.'"'.$checked.'>';
				$output .= '<label for="'.$id.'">'.$opt.'</label>';
				$this->generatedfields[] = $this->fieldPrefix.$ref.'[]';
			}
		}
		$output .= '</span>';
		return($output);
	}
	function make_yesorno($ref, $val, $options, $readonly= '') {
		if (empty($val)) {
			$val = 'No';
		}
		$options = array('No','Yes');
		$output = '<span class="radioset">';
		foreach ($options as $k=>$opt) {
			///echo $opt.' '.$val.'<br>';
			$id = $ref.'-'.str_replace(' ','',ucwords($opt));
			$sel = (strtolower($val) == strtolower($opt)) ? ' checked="checked" ' : '';
			$output .= '<input value="'.$opt.'" type="radio" name="'.$this->fieldPrefix.$ref.'" id="'.$id.'" '.$sel.'/>';
			$output .= '<label for="'.$id.'">'.$opt.'</label>';
			$this->generatedfields[] = $this->fieldPrefix.$ref;
		}
		$output .= '</span>';
		return($output);
	}

	function make_yesornobinary($ref, $val, $options, $readonly= '') {
		$options = array(0=>'No',1=>'Yes');
		$output = '<span class="radioset">';
		foreach ($options as $k=>$opt) {
			#echo $opt.' '.$val.'<br>';
			$id = $ref.'-'.str_replace(' ','',ucwords($opt));
			$sel = ($val == $k) ? ' checked="checked" ' : '';
			$output .= '<input value="'.$k.'" type="radio" name="'.$this->fieldPrefix.$ref.'" id="'.$id.'" '.$sel.'/>';
			$output .= '<label for="'.$id.'">'.$opt.'</label>';
			$this->generatedfields[] = $this->fieldPrefix.$ref;
		}
		$output .= '</span>';
		return($output);
	}
	function make_noornotsure($ref, $val, $options, $readonly= '') {
		$options = array('No','Yes/Unsure');
		$output = '<span class="radioset">';
		foreach ($options as $k=>$opt) {
			$id = $ref.'-'.str_replace(' ','',ucwords($opt));
			$sel = ($val == $k) ? ' checked="checked" ' : '';
			$output .= '<input value="'.$k.'" type="radio" name="'.$this->fieldPrefix.$ref.'" id="'.$id.'" '.$sel.'/>';
			$output .= '<label for="'.$id.'">'.$opt.'</label>';
			$this->generatedfields[] = $this->fieldPrefix.$ref;
		}
		$output .= '</span>';
		return($output);
	}
	function make_checkbox($ref, $val, $options, $readonly= '') {
		$name = $this->makeMany($ref);
		$sel = ($val) ? 'checked="checked" ': '';
		$output = '<input value="1" type="checkbox" name="'.$this->fieldPrefix.$name.'" id="checkbox-'.$ref.'" '.$sel.'/>';
		$this->generatedfields[] = $this->fieldPrefix.$name;
		return($output);
	}
	function output ($formdata,$formerrors,$breaks=array()) {
		$x = 0;
		$break = 1;
		
		echo '<div class="break-'.$break.' break">';
		foreach ($formdata as $row) {
			
			if (empty($row[6])) {
				$row[6] = false;
			} 
			if (empty($row[5])) {
				$row[5] = '';
			} 
			if (empty($row[4])) {
				$row[4] = '';
			} 
			if (empty($row[3])) {
				$row[3] = '';
			} 
			if (empty($row[2])) {
				$row[2] = false;
			}
			if (empty($row[1])) {
				$row[1] = '_untitledfield_'.rand();
			}
			if (empty($formerrors[$row[1]])) {
				$error = '';
			} else {
				$error = $formerrors[$row[1]];
			}
			if (!empty($row[0])) {
				echo $this->makerow(
					$row[0],	// 1 - Label
					$row[1], 	// 2 - Ref
					$row[2],	// 3 - Mandatory
					$row[3],	// 4 - Value 
					$error, 	// 5 - Error 
					$row[4],	// 6 - Type 
					$row[5],	// 7 - Options 
					$row[6],	// 8 - Readonly
					$x 			// 9 - Index
					); 
				$x++;
			}
			if (in_array($row[1], $breaks)) {
				$break++;
				echo '</div>';
				echo '<div class="break-'.$break.' break">';
			}
			
		}
		echo '</div>';
	}
	function output2 ($formdata) {
		foreach ($formdata as $r) {
			$x = 0;
			$v = array();
			while ($x <= 6) {
				$v[$x] = (isset($r[$x])) ? $r[$x] : ''; 
				$x++;
			}
			echo $this->makerow($v[0],$v[1],$v[2],$v[3],$v[4],$v[5],$v[6]);
		}
	}
	function lazyRowMaker($data,$x=0) {
		$new = array(); 
		if (!isset($data['label'])) {
			return;
		} 
		$new[0] = $data['label'];
		$new[1] = (!empty($data['lots'])) ? $data['ref'].'[]' : $data['ref'];
		$new[2] = (isset($data['mandatory'])) ? $data['mandatory'] : '';
		if (is_array($data['value'])) {
			$new[3] = (isset($data['value'][$x])) ? $data['value'][$x] : '';
		} else {
			$new[3] = (isset($data['value'])) ? $data['value'] : '';
		}
		$new[4] = (isset($data['error'])) ? $data['error'] : '';
		$new[5] = (isset($data['type'])) ? $data['type'] : '';
		$new[6] = (isset($data['value'])) ? $data['value'] : '';

		return ($this->makerow($new[0],$new[1],$new[2],$new[3],$new[4],$new[5],$new[6]));
	}
	function outputFields ($structure,$wrapperclass='fieldsetinner') {
		if (!$this->justshowthevalues) {
			$output = '<input name="'.$this->fieldPrefix.rand(100,999).'" value="'.rand(100,999).'" type="hidden"/>';
		}
		$output = '<div class="'.$wrapperclass.'">';
		if (!empty($structure)) {
			foreach ($structure as $row) {
				
				#exit;
				if (!isset($this->values[$row->ref])) {
					$val = '';
				} else if (is_object($this->values[$row->ref])) { 
					$val = (isset($this->values[$row->ref])) ? $this->values[$row->ref]->user_val : '';
				} else {
					$val = $this->values[$row->ref];
				}
				$type = (isset($row->type)) ? $row->type : NULL;
				if (isset($row->options)) {
					if (!is_array($row->options)) {
						$options = json_decode($row->options);
					} else {
						$options = $row->options;
					}
				} else {
					$options = NULL;
				}
				
				$output .= $this->makerow($row->label, $row->ref, $row->mandatory, $val, NULL, $type, $options);
			}
		}
		$output .= '</div>';
		return($output);
		
	}
	function makeStructObj ($structure) {
		$row = array();
		foreach ($structure as $s) {
			$s[3] = (empty($s[3])) ? NULL : $s[3];
			$s[4] = (empty($s[4])) ? NULL : $s[4];
			$s[5] = (empty($s[5])) ? 0 : $s[5];
			$row[] = (object) array(
					'label'		=>	$s[0],
					'ref'		=>	$s[1],
					'mandatory'	=>	$s[2],
					'type'		=>	$s[3],
					'options'	=>	$s[4],
					'readonly'	=>	$s[5]
			);
		}
		return($row);
	}
	function saveSubmission ($form_ref, $structure, $postdata, $submission_id = 0,$additional_identifier=0, $what = '') {
		global $wpdb;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}		
		if (empty($submission_id)) {
			$query = 'insert into form_submissions (date_submitted, ip_address, session_id, additional_identifier, what) VALUES ("'.date('Y-m-d H:i').'", "'.$ip.'", "'.session_id().'", "'.$additional_identifier.'","'.$what.'")';
			$wpdb->query($query);
			$submission_id = $wpdb->insert_id;
		} else {
			$values = $this->getSubmissionValues ($submission_id);
		}
		foreach ($structure as $form_section_ref => $d) {
			foreach ($d as $k => $s) {
				$val = (isset($postdata[$s->ref])) ? $postdata[$s->ref] : '';			
				if (is_array($val)) {
					$val = json_encode($val);
				}
				$label = esc_sql($s->label);
				#$val = esc_sql($val);
				#echo $s->ref.'<br>';
				
				#ppr($val);
				if (isset($values[$s->ref])) {
					#ppr($values[$s->ref]);
					$query = "update form_submissions_values set `value` = '$val' WHERE value_id = ".$values[$s->ref]->value_id;/*  */
				} else {
					$query = "insert into form_submissions_values 
							(`submission_id`, `form_ref` , `form_section_ref`, `field_ref`, `field_label`, `value`) 
							VALUES 
							($submission_id, '$form_ref', '$form_section_ref', '$s->ref', '$label', '$val')";
				}
				#echo $query.'<Br>';
				$wpdb->query($query);
				#exit;
			}
		}
		#exit;
		if (!empty($submission_id)) {
			return $submission_id;
		} else {
			return false;
		}
	}
	function getSubmissionValues ($submission_id) {
		global $wpdb;
		$q = 'SELECT * FROM form_submissions_values WHERE submission_id = '.$submission_id;
		#echo $q;
		$r = $wpdb->get_results($q,OBJECT);
		$values = array();
		foreach ($r as $v) {
			 $values[$v->field_ref] = $v;
		}
		return($values);
	}
	function getSubmission($submission_id,$what='') {
		global $wpdb;
		$q = 'SELECT * FROM form_submissions WHERE submission_id = '.$submission_id;
		if (!empty($what)) {
			$q .= ' AND what = "'.$what.'"';
		}
		#echo $q;
		$submission = $wpdb->get_row($q,OBJECT);
		return($submission);
	}
	function getSubmissionByAdditional($additional_identifier,$what) {
		global $wpdb;
		$q = 'SELECT * FROM form_submissions WHERE additional_identifier = '.$additional_identifier.' AND what = "'.$what.'"';
		$submission = $wpdb->get_row($q,OBJECT);
		return($submission);
	}
	function getFullSubmission ($submission_id, $additional_identifier=0,$what='') {
// 		echo 'A: '.$additional_identifier.'<br>';
// 		echo 'W: '.$what.'<br>';
		if (!empty($additional_identifier)) {
			$submission = $this->getSubmissionByAdditional($additional_identifier,$what);
			#ppr($submission);
			#exit;
		} else {
			#echo 'WHY';
			#exit;
			$submission = $this->getSubmission($submission_id,$what='');
		}
		if (!empty($submission)) {
			$submission->values = $this->getSubmissionValues ($submission->submission_id);
			return($submission);
		}
	}
	function processPostMulti ($structure) {
		$processed = array(
			'data'=>array(),
			'errors'=>array(),
		);
		foreach ($structure as $s) {
			$p = $this->processPost($s);
			if (!empty($p['errors'])) {
				$processed['errors'] = array_merge($processed['errors'], $p['errors']);
			}
			if (!empty($p['data'])) {
				$processed['data'] = array_merge($processed['data'], $p['data']);
			}
		}
		return($processed);
	}
	function processPost($structure) {
		$errors = array();
		foreach ($structure as $k => $s) {
			if (($s->type == 'html') || ($s->type == 'textstring')) {
				continue;
			} else if ($s->type == 'multitextbox') {
				$x = 1;
				while ($x <= $s->options['count']) {
					foreach ($s->options['options'] as $ref=>$label) {
						$ref = $ref.'_'.$x;
						if (isset($_POST[$ref])) {
							$data[$ref] = $_POST[$ref];
						}
					}
					$x++;
				}
			} else if ($s->type == 'multiselect') {
				if ((isset($_POST[$s->ref])) && (is_array($_POST[$s->ref]))) {
					$data[$s->ref] = serialize($_POST[$s->ref]);
				} else {
					$data[$s->ref] = (isset($_POST[$s->ref])) ? $_POST[$s->ref]	: '';
				}
			} else {
				if ((isset($_POST[$s->ref])) && (!is_array($_POST[$s->ref]))) {
					$data[$s->ref] = strip_tags($_POST[$s->ref]);
				} elseif ((isset($_POST[$s->ref])) && (is_array($_POST[$s->ref]))) {
					$data[$s->ref] = $_POST[$s->ref]	;
				} else {
					$data[$s->ref] = '';
				}
				if (($s->mandatory) && (empty($_POST[$s->ref]))) {
					$errors[] = 'Please enter your '.$s->label;
				}
			}
	
		}
		/*
		if  (get_magic_quotes_gpc()) {
			echo 'Magic quotes are on';
		} else {
			echo 'Magic quotes are off';
		}
		*/
		return(array(
				'data'=>$data,
				'errors'=>$errors
		));
	}
	function expandStructure($structure) {
		$structure_new = array();
		foreach ($structure as $k => $v) {
			if (is_object($v)) {
				$type = $v->type;
			} else {
				die('Cannot expand structure');
			}
			
			
			if ($type == 'multitextbox') {
				$x = 1;
				$add = array();
				while ($x <= $v->options['count']) {
					foreach ($v->options['options'] as $ref=>$label) {
						$ref = $ref.'_'.$x;
						$add[$ref] = array($label.' ('.$x.')', $ref, 0);
					}
					$x++;
				}
				//						ppr($add);
				$add = $this->makeStructObj($add);
	
				foreach ($add as $a) {
					$structure_new[] = $a;
				}
				#ppr($structure_new);
				#exit;
			} else {
				$structure_new[] = $v;
				#ppr($v);
			}
		}
		return($structure_new);
	}
	function make_form_html ($form, $args=array()) {
		//$prefix = (isset($args['prefix'])) ? $args['prefix'] : 'philosri';
		$prefix = 'philosri'; // If this needs to be dynamic then it should be in the database
	
		/* Make a preview for the form itself */
		$this->fieldClasses = array(
		);
		$structure = array();
		foreach ($form->fields as $f) {
			#echo $f->options.'<br>';
			$type = $f->type;
			$label = ($f->mandatory) ? $f->label.' '.$this->fb->mandatoryMarker : $f->label;
	
			$structure[$f->field_group][] = array(
					$label,
					$f->ref,
					$f->mandatory,
					$type,
					$this->opt2arr($f->options),
					$f->readonly
			);
			#ppr($f->options);
			#ppr($this->opt2str($f->options));
		}
		$html = '';
		foreach ($structure as $k=>$s) {
			$html .= '<h3>'.$k.'</h3>';
			$html .= $this->outputFields($this->makeStructObj($s));
		}
		if (!empty($args['wrap'])) {
			$post = (isset($args['post'])) ? $args['post'] : '';
			$action = preg_replace('/&philosri_status=(complete)/','',$_SERVER['REQUEST_URI']);
			$html = '<form class="'.$prefix.'" method="post" action="'.$action.'">
						'.$html.'
						<input type="hidden" name="'.$prefix.'_formref" value="'.$form->reference.'">
						<input type="hidden" name="philosri_nonce" value="'.$this->nonce($form->reference).'">
						<input type="hidden" name="philosri_post" value="'.$post.'">
						<button>Send</button>
					</form>';
		}
		return($html);
	}
	function opt2json ($opt) {
		if (!empty($opt)) {
			$o = preg_split('/\n/',$opt);
			$options = array();
			foreach ($o as $r) {
				if (strstr($r, ' : ')) {
					$r = explode(' : ', $r);
					$options[$r[0]] = $r[1];
				} else {
					$r = trim($r);
					$options[$r] = $r;
				}
			}
			$options = json_encode($options);
			return($options);
		} else {
			return('');
		}
	}
	function opt2str($opt) {
		$o = (array) json_decode($opt);
	
		$options = '';
		if ((!empty($o)) && (is_array($o))) {
			foreach ($o as $p => $t) {
				$options .= $p." : ".$t."\n";
			}
		}
		return(trim($options));
	}
	function opt2arr($opt) {
		$o = (array) json_decode($opt);
	
		$options = array();
		if ((!empty($o)) && (is_array($o))) {
			foreach ($o as $p => $t) {
				$options[$p] = $t;
			}
		}
		return($options);
	}
	function nonce($form='all',$len=50) {
		global $GESF_KEEP_NONCE;
// 		echo '$GESF_KEEP_NONCE = '.$GESF_KEEP_NONCE.'<br>';
		if (($GESF_KEEP_NONCE == true) && (!empty($_SESSION['philosri_nonce'][$form]))) {
			return($_SESSION['philosri_nonce'][$form]);
		}
		// Generate a random password
		// 74 characters
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789!";
		$x = 1;
		$str = '';
		while ($x < $len) {
			$str .= $chars[rand(0,73)];
			$x++;
		}
		$_SESSION['philosri_nonce'][$form] = $str;
		return($str);
	}
	function fieldValsFromPostMeta($post_id,$structure) {
		foreach ($structure as $s) {
			$val = get_post_meta($post_id, 'propsrch_'.$s->ref)[0];
			if ($s->type == 'multiselect') {
				$val = json_decode($val);
			}
			$this->values[$s->ref] = $val;
		}
	}
	
}
