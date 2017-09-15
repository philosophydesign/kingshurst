<?php 
$sr_page_id = get_option('propsrch_searchaction');
$sr_page = get_post($sr_page_id);

?>
<form action="<?php echo get_permalink($sr_page) ?>" method="get" class="propsrch_form">
<?php 
echo $form_html;
?>
<input name="sb" value="n" type="hidden"/> 
<input name="psv" value="list" type="hidden"/> 
<button>Search</button>
<div class="formblocker"></div>
</form>
