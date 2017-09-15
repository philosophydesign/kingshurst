<h2>Edit Form</h2>
<ul class="horizontal-tabs">
	<li class="active" data-tabid="form-settings">Settings</li>
	<li data-tabid="form-edit">Edit Fields</li>
	<li data-tabid="form-preview">Preview</li>
</ul>

<div id="form-settings" class="gesform tab-container active">
	<h3>Settings</h3>
	<form action="?page=register-interest&ri-action=editform&form=1&save" method="post">
			<button class="button button-primary">Save</button>
			<input type="hidden" name="RI_runearly" value="editsettings">
			<?php 
			echo $data['settingsform'];
			?>
			<button class="button button-primary">Save</button>
	</form>
</div>
<div id="form-edit" class="gesform tab-container">
	<h3>Edit</h3>
	
    <script  src=<?php echo REGINT_URI ?>/assets/js/form.react.js' type="text/babel"></script>
    
	<form action="?page=register-interest&ri-action=editform&form=1&save" method="post">
		<button class="button button-primary">Save</button>
		<input type="hidden" name="RI_runearly" value="editform">
		<div id="form-react-root">
		  <!-- This div's content will be managed by React. -->
		</div>
		<button class="button button-primary">Save</button>
	</form>

</div>
<div id="form-preview" class="gesform  tab-container">
	<h3>Preview</h3>
	<?php 
	echo $data['preview'];
	?>
</div>