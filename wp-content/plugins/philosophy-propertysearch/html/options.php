<?php 

$PS = new PropertySearch();
$saved_heirachy = $PS->propsrch_heiropt();
$saved_buyingoptions = $PS->propsrch_buyingoptions();
$saved_posttypes = $PS->propsrch_posttypes();
$saved_searchoptions = $PS->propsrch_searchoptions();
$saved_deactivatedfields = $PS->propsrch_deactivatedfields();

$post_types = getFilteredPostTypes();
$args = array(
		'post_type'			=>	'page',
		'post_status'		=>	'publish',
		'posts_per_page'	=>	-1,
		'orderby'			=>	'post_title',
		'order'				=>	'ASC'
		
);
$pagesel = get_option('propsrch_searchaction');
$pages = get_posts($args);


?>
<h1>Property Search - Control Panel</h1>

<form action="" method="post">

	<div class="propsrch_option_special">
		<a href="#" class="button" id="regneratecache">Regenerate Cache</a>
		<button class="savebtn button button-primary button-large">Save</button> 
		<span id="cachefeedback"></span>
	</div>
	<input name="propsrch_saveoptions" value="1" type="hidden"/>

	<div class="propsrch_option_question">
		<h2>Search Options</h2>
		<h3>Activation</h3>
		<p>Activate the options you would like available in the search form</p>
		<?php 
		foreach ($PS->searchoptions as $k => $v) {
			$checked = (!empty($saved_searchoptions[$k])) ? ' checked="checked"': '';
			echo '
			<fieldset>
				<input type="checkbox" name="searchoptions[]" value="'.$k.'" id="searchoption-'.$k.'"'.$checked.'>
				<label for="searchoption-'.$k.'">'.$v['label'].'</label>
			</fieldset>';
		}
		?>
		<h3>Field Mechanisms</h3>
		<?php 
		$fmopt = [
			'Total Area'=>[
				'name'		=> 'totalarea',
				'options'=>[
					'prange'	=>	'Pre-Defined Ranges',				
					'urange'	=>	'User-Defined Ranges',				
					'slider'	=>	'Slider',				
				]
			]
		];
		foreach ($fmopt as $h4 => $data) {
			$selected = get_option('propsrch_fieldmech_'.$data['name']);
			
			if (empty($selected)) {
				$selected = reset($data['options']);
				$selected = key($data['options']);
			}
			echo '<h4>'.$h4.'</h4><ul>';
			foreach ($data['options'] as $k => $d) {
				$sel = ($k == $selected) ? ' checked="checked"' : '';
				echo '
				<li>
				<input id="fm-'.$data['name'].'-'.$k.'" name="fm-'.$data['name'].'" value="'.$k.'" type="radio" '.$sel.'/>
				<label for="fm-'.$data['name'].'-'.$k.'">'.$d.'</label>
				</li>';
			}
			echo '</ul>';
		}
		?>
	</div>
	<div class="propsrch_option_question">
		<h2>Deactivate Fields</h2> 
		<?php 
		foreach ($PS->deactivatable as $k => $v) {
			$checked = (!empty($saved_deactivatedfields[$k])) ? ' checked="checked"': '';
			echo '
			<fieldset>
				<input type="checkbox" name="deactivate[]" value="'.$k.'" id="deactivate-'.$k.'"'.$checked.'>
				<label for="deactivate-'.$k.'">'.$v['label'].'</label>
			</fieldset>';
		}
		?>
	</div>
	<div class="propsrch_option_question">
		<h2>Heirachy</h2>
		<p>Activate the heirachy that applies to this site</p>
<?php 

foreach ($PS->heirachy as $k => $v) {
	$checked = (!empty($saved_heirachy[$k])) ? ' checked="checked"': '';
	echo '
	<fieldset>
		<input type="checkbox" name="heirachy[]" value="'.$k.'" id="heiachy-'.$k.'"'.$checked.'>
		<label for="heiachy-'.$k.'">'.$v['label'].'</label>
	</fieldset>';
}
?>
	</div>
	<div class="propsrch_option_question">
		<h2>Buying Options</h2>
		<p>Activate the options that applies to this site</p>
	
<?php 

foreach ($PS->buyingopts as $k => $v) {
	$checked = (!empty($saved_buyingoptions[$k])) ? ' checked="checked"': '';
	echo '
	<fieldset>
		<input type="checkbox" name="buyingopts[]" value="'.$k.'" id="buyingopts-'.$k.'"'.$checked.'>
		<label for="buyingopts-'.$k.'">'.$v['label'].'</label>
	</fieldset>';
}
?>
	</div>
	<div class="propsrch_option_question" id="tenure">
		<h2>Tenure Options</h2>
		<p>Does this site use standard tenure types or frendly tenure?</p>
	
<?php 
$opt = array(
	'standard'	=>	'Standard',
	'friendly'	=>	'Friendly'
);

$saved_tenureoption = get_option('propsrch_tenureoption');
if (empty($saved_tenureoption)) {
	$saved_tenureoption = 'standard';
	update_option('propsrch_tenureoption', $saved_tenureoption);
}
foreach ($opt as $k => $v) {
	$checked = ($saved_tenureoption == $k) ? ' checked="checked"': '';
	echo '
	<fieldset>
		<input type="radio" name="tenureoption" value="'.$k.'" id="tenureoption-'.$k.'"'.$checked.'>
		<label for="tenureoption-'.$k.'">'.$v.'</label>
	</fieldset>';
}
?>
	</div>
	<div class="propsrch_option_question" id="status">
		<h2>Status Options</h2>
		<p>Does this site use do sales, rentals, or both?</p>
		<?php 
		$opt = array(
				'sales'	=>	'Just Sales',
				'rent'	=>	'Just Rentals',
				'both'	=>	'Both'
		);
		$saved_statusoption= get_option('propsrch_statusoption');
		if (empty($saved_statusoption)) {
			$saved_statusoption = 'both';
			update_option('propsrch_statusoption', $saved_statusoption);
		}
		foreach ($opt as $k => $v) {
			$checked = ($saved_statusoption == $k) ? ' checked="checked"': '';
			echo '
			<fieldset>
				<input type="radio" name="statusoption" value="'.$k.'" id="statusoption-'.$k.'"'.$checked.'>
				<label for="statusoption-'.$k.'">'.$v.'</label>
			</fieldset>';
		}
		?>
	
	</div>
<?php 
		

	$property_types = get_psterms('property_type');
?>

	<div class="propsrch_option_question termadminlist" id="typeslist">
		<h2>Property Types</h2>
		<p>What times of property are available?</p>
		<ul id="termadminlist-ui">
		<?php 
		if (!empty($property_types)) {
			foreach ($property_types as $p) {
				echo '<li data-id="'.$p->term_id.'">
						<input type="hidden"  class="termname update" name="termupdate['.$p->term_id.']" value="'.$p->term_value.'" id="update_'.$p->term_id.'"/>
						<p><span class="termname proptype">'.$p->term_value.'</span></p> 
						<span class="termopt"><a class="button remove" href="#">Remove</a> <a class="button update" href="#">Update</a> <a class="button cancelupdate" href="#">Cancel</a></span>
					</li>';
			}
		}
		?>
		</ul>
		<input name="proptypeadd"  id="proptypeadd"/> <a class="button add" href="#">Add</a>
	</div>
	<div class="propsrch_option_question" id="areainputunits">
		<h2>Area Input Units</h2>
		<?php 
		$saved_areainputunits= get_option('propsrch_areainputunits');
		if (empty($saved_areainputunits)) {
			$saved_areainputunits = 'metric';
			update_option('propsrch_areainputunits', $saved_areainputunits );
		}
		?>
		<input type="radio"<?php echo ($saved_areainputunits == 'metric') ? 'checked="checked"' : '';?> name="propsrch_areainputunits" value="metric" id="propsrch_areainputunits_metric"> <label for="propsrch_areainputunits_metric">Metric</label><br>
		<input type="radio"<?php echo ($saved_areainputunits == 'imperial') ? 'checked="checked"' : '';?> name="propsrch_areainputunits" value="imperial" id="propsrch_areainputunits_imperial"> <label for="propsrch_areainputunits_imperial">Imperial</label>
	</div>
	<div class="propsrch_option_question" id="linkedposttypes">
		<h2>Linked Post Types</h2>
		<p>Link extra post types that apply to this site</p>
		
<?php 

foreach ($post_types as $k => $v) {
	#if (isset($saved_posttypes[$k])) {
		if ($saved_posttypes[$k]->linked == 1) {
			$checked_post = ' checked="checked"';
			$class = ' active"';
		} else {
			$checked_post = '';
			$class = '';
		}
		if ($saved_posttypes[$k]->multiple == 1) {
			$checked_mult = ' checked="checked"';
		} else {
			$checked_mult = '';
		}
		if ($saved_posttypes[$k]->searchable == 1) {
			$checked_search = ' checked="checked"';
		} else {
			$checked_search = '';
		}
		echo '
		<fieldset id="linkedposttypes-fs-'.$k.'" class="'.$class.'">
			<input type="checkbox" class="linkedposttypecheckbox" name="linkedposttypes[]" value="'.$k.'" id="linkedposttypes-'.$k.'"'.$checked_post.'>
			<label for="linkedposttypes-'.$k.'">'.$v.'</label>
			<div class="moreoptions'.$class_mult.'">
				<input name="multiple['.$k.']" value="0" type="checkbox" id="mult-linkedposttypes-'.$k.'"'.$checked_mult.'/>
				<label for="mult-linkedposttypes-'.$k.'">Allow multiple selection</label>
				<Br>
				<input name="searchable['.$k.']" value="0" type="checkbox" id="mult-searchposttypes-'.$k.'"'.$checked_search.'/>
				<label for="mult-searchposttypes-'.$k.'">Searchable</label>
			</div>
		</fieldset>';
	#}
}


?>
	</div>
	<div class="propsrch_option_question" id="resultpage">
		<h2>Search Result Page</h2>
		<p>When a user submits the form where should they be taken to?</p>
		<select name="searchpage">
			<option>--</option>
		<?php 
		foreach ($pages as $p) {
			$sel = ($p->ID == $pagesel) ? ' selected="selected" ' : ''; 
			echo '<option value="'.$p->ID.'"'.$sel.'>'.$p->post_title.'</option>';
		}
		
		?>
		</select>
	</div>
	<div class="propsrch_option_question hastemplatevars" id="resulttemplate">
		<h2>Search Result Template</h2>
		<?php 
			
		$acffields = get_acf_fields();
		$getFuncVals = getFuncVals();
		?>
		<p>
		<label for="variable_chooser_1">Value</label>
		<select id="variable_chooser_1" class="variable_chooser">
			<option>-</option>
		<?php foreach ($PS->result_variables as $var => $d) { 
			echo '<option value="'.$var.'">'.$d['label'].'</option>';
		}
		foreach ($acffields as $f) { 
			echo '<option value="acf_'.$f['name'].'">ACF: '.$f['label'].'</option>';
		}?>
		</select>
		<a href="#" class="button insert_into_template_var" id="insert_into_template_1">Insert</a>
		<br>
		<label for="function_chooser_1">Function</label>
		<select id="function_chooser_1" class="function_chooser">
			<option>-</option>
		<?php 
		foreach ($getFuncVals as $f => $n) {
			echo '<option value="'.$f.'">'.$n.'</option>';
		}
		?>
		</select>
		
		<a href="#" class="button insert_into_template_func" id="insert_into_template_func_1">Insert</a>
		</p>
		<textarea rows="4" cols="50" class="template_destination  codelike" id="resulttemplate_textarea" name="resulttemplate"><?php echo stripslashes(get_option('propsrch_resulttemplate')); ?></textarea>
	</div>
	<div class="propsrch_option_question" id="featuredimagesize">
		<h2>Featured Image Size</h2>
		<?php $image_sizes = get_intermediate_image_sizes(); ?>
		<select name="propsrch_featured_image">
		  <?php 
		  $fis = get_option('propsrch_featured_image');
		  foreach ($image_sizes as $size_name) {
		  	$sel = ($fis == $size_name) ? 'selected="selected" ' : ''; 
		  	echo '<option '.$sel.' value="'.$size_name.'">'.$size_name.'</option>';
		  }
		  
		  ?>
		</select>
	</div>
	<div class="propsrch_option_question" id="noresults">
		<h2>No results</h2>
		
		<textarea rows="4" cols="50" class="codelike" id="resulttemplate_textarea" name="noresulttemplate"><?php echo stripslashes(get_option('propsrch_noresulttemplate')); ?></textarea>
	</div>
	<div class="propsrch_option_question hastemplatevars" id="relatedpropertytemplate">
		<h2>Related property template</h2>
		<p>
		<label for="variable_chooser_2">Value</label>
		<select id="variable_chooser_2" class="variable_chooser">
			<option>-</option>
		<?php foreach ($PS->result_variables as $var => $d) { 
			echo '<option value="'.$var.'">'.$d['label'].'</option>';
		}
		foreach ($acffields as $f) { 
			echo '<option value="acf_'.$f['name'].'">ACF: '.$f['label'].'</option>';
		}?>
		</select>
		<a href="#" class="button insert_into_template_var" id="insert_into_template_2">Insert</a>
		<br>
		<label for="function_chooser_2">Function</label>
		<select id="function_chooser_2" class="function_chooser">
			<option>-</option>
		<?php 
		foreach ($getFuncVals as $f => $n) {
			echo '<option value="'.$f.'">'.$n.'</option>';
		}
		?>
		</select>
		
		<a href="#" class="button insert_into_template_func" id="insert_into_template_func_2">Insert</a>
		</p>
		<textarea rows="4" cols="50" class="template_destination codelike" id="resulttemplate_textarea" name="relatedtemplate"><?php echo stripslashes(get_option('propsrch_relatedtemplate')); ?></textarea>
	</div>
	<div class="propsrch_option_question" id="resulttemplate">
		<h2>Colour of admin header</h2>
		<p>
		<input type="text" name="propsrch_admin_colour" value="<?php 
		$c = get_option("propsrch_admin_colour");
		
		echo (!empty($c)) ? $c : '#0000FF';  
		
		?>">
		</p>
	</div>
	

	<div class="propsrch_option_special">
		
		<button class="savebtn button button-primary button-large">Save</button> 
	</div>
</form>