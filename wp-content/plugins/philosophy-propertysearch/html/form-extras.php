<?php 

global $GESFGEN;

// $qa = ((isset($_GET['dopropsrch'])) || (isset($_GET['sb'])))  ? '&' : '?';
$PS = new PropertySearch();

// $struct = $PS->get_searchform_struct();

$newquerystr = '';
$html_select = '';
$sb = array(
		'n'=>'Newest First',
		'o'=>'Oldest First',
		'l'=>'Largest First',
		's'=>'Smallest First',
);
foreach ($sb as $k => $v) {
	$sel = (
			(isset($_GET['sb'])) &&
			($_GET['sb'] == $k)
			) ? 'selected="selected" ' : '';
			$html_select .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
}
$html_hidden = '';

parse_str($_SERVER['QUERY_STRING'], $qsvals);
$qa = array();
$PS = new PropertySearch();
$saved_posttypes = $PS->propsrch_posttypes();
$sptf = array();
foreach ($saved_posttypes as $k => $v) {
	if (($v->linked) && ($v->searchable)) {
		$fn = $PS->makeLinkedFieldName($k);
		if (isset($_GET[$fn])) {
			foreach ($_GET[$fn] as $hv) {
				$html_hidden .= '<input name="'.$fn.'[]" value="'.$hv.'" type="hidden" class="duplicate_'.$fn.'">';
			}
			$sptf[$fn] = 1;
		}
	}
}
foreach ($GESFGEN as $k) {
		$na = str_replace('[]','',$k);
		if (isset($sptf[$na])) {
			continue;
		}
// 		$s = str_replace('[]','',$s);
		$v = (isset($_GET[$na])) ? $_GET[$na] : '';
		$array = array();
		if (!is_array($v)) {
			$array[] = $v;
		} else {
			$array = $v;
		}
		foreach ($array as $value) {
			if (is_string($value)) {
				$value = (($k == 'un') && (empty($value))) ? 'sqm' : $value; 
				$html_hidden .= '<input class="duplicate_'.$na.'" type="hidden" name="'.$k.'" value="'.$value.'"  id="duplicate_'.$na.'">';
				if ($value != '') {
					$qa[$k] = $k.'='.$value.'&';
				}
			}
		}
}
$qa = implode("", $qa);
if (empty($html_hidden)) {
	$html_hidden = '<input name="dopropsrch" value="1" type="hidden"/>';
}


?>
<div id="propsrch_extras">
<form action="" class="propsrch_extras_form sel-<?php 
	echo ((empty($_GET['psv'])) || ($_GET['psv'] != 'map'))  ? 'viewlist' : 'viewmap'  
?>">
<?php if ((empty($_GET['psv'])) || ($_GET['psv'] != 'map')) {?>
	<label>Sort by</label>
	<select name="sb" id="propsrch_ordersel">
	<?php 
		echo $html_select 
	?>
	</select>
	<?php 
		echo $html_hidden
	?>
	<button>Update</button>
<?php } ?>
	<div id="propsrch-viewtoggle">
		<a href="?<?php echo $qa; ?>psv=list" class="viewaslist">List</a>
		<a href="?<?php echo $qa; ?>psv=map" class="viewasmap">Map</a>
	</div>
</form>
</div>
