<?php
class FieldCache {
	var $fields;
	function __construct() {

	}
// 	function getMatching ($post_type, $field_name, $value) {
// 		global $wpdb;
// 		$q = 'select * from propsrch_fieldcache
// 				where
// 					post_type = "'.$post_type.'" AND
// 					field_name = "'.$field_name.' AND 
// 					value = "'.$value.'"';
											
// 		$r = $wpdb->get_results($q);
// 		return($r);
// 	}
	function getVals($post_type, $field_name, $aslist) {
		global $wpdb;
		$q = 'select * from propsrch_fieldcache
				where
					post_type = "'.$post_type.'" AND
					field_name = "'.$field_name.'"
							order by value';
		$r = $wpdb->get_results($q);
		if ($aslist) {
			$list = array();
			foreach  ($r as $v) {
				$list[] = $v->value;
			}
			return($list);
		} else {
			return($r);
			
		}
	}
	function delete ($post_type, $field_name, $value) {
		global $wpdb;
		$q = 'delete from propsrch_fieldcache
				where 
					post_type = "'.$post_type.'" AND 
					field_name = "'.$field_name.'" AND
					value = "'.$value.'"';
// 		echo $q;
		$wpdb->query($q);
		
	}
	function insert($post_type, $field_name, $value) {
		global $wpdb;
		$q = 'insert into propsrch_fieldcache
				(
				post_type,
				field_name,
				value
				)
				VALUES
				("'.$post_type.'",
				"'.$field_name.'",
				"'.trim($value).'")';
		$wpdb->query($q);
	}
	function getPostMeta () {
		
	}
	function syncFieldCache ($fields, $cachelist, $post_type, $status='publish') {
		global $wpdb;
	
		$fieldrefs = array();
		foreach ($fields as $f) {
			if ((!empty($f[1])) && (in_array($f[1], $cachelist)))  {
				$v = get_meta_values($f[1], $post_type);
				$fieldrefs[$f[1]] = 'propsrch_'.$f[1];
			}
		}
		
		
		$q = 'select * from propsrch_fieldcache';
		$r = $wpdb->get_results($q);
		
		$tofind = [];
		foreach ($r as $c) {
 			$tofind[$c->post_type]['propsrch_'.$c->field_name][$c->value] = $c->value;
 			#echo 'GOT: '.$m->post_type.' propsrch_'.$c->field_name.' &quot;'.$c->value.'&quot;<br>';
		}
		$q = "SELECT p.post_type, pm.meta_key, pm.meta_value FROM {$wpdb->postmeta} pm
		LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
		WHERE pm.meta_key IN ('".implode('", "', $fieldrefs) ."')
		AND p.post_status = '$status'
		AND p.post_type = '$post_type'
		GROUP by meta_value";
		$r = $wpdb->get_results($q);
// 		ppr($r);
		$postmeta = array();
		foreach ($r as $m) {
			#echo 'TRY: '.$m->post_type.' '.$m->meta_key.' &quot;'.$m->meta_value.'&quot;<br>';
			if ((isset($tofind[$m->post_type])) && 
					(isset($tofind[$m->post_type][$m->meta_key]))) {
				#echo 'Test = YES<br>';
			}
			if (
					(isset($tofind[$m->post_type])) && 
					(isset($tofind[$m->post_type][$m->meta_key])) && 
					(isset($tofind[$m->post_type][$m->meta_key][$m->meta_value]))  
					
				) {
				// This one is fine
				#echo '...This one is fine<br>';
				unset($tofind[$m->post_type][$m->meta_key][$m->meta_value]);
			} else {
				#echo '...ADD<br>';
				$rk = str_replace('propsrch_', '', $m->meta_key);
				$this->insert($m->post_type, $rk, $m->meta_value);
			}
		}
		if (count($tofind)) {
			//echo 'C1: '.count($tofind).'<Br>';
			foreach ($tofind as $pt => $fields) {
				if (count($fields)) {
					//echo 'C2: '.count($fields).'<Br>';
					foreach ($fields as $f => $v) {
						if (count($v)) {
							//echo 'C3: '.count($v).'<Br>';
							$rk = str_replace('propsrch_', '', $f);
							foreach ($v as $removeval) {
								$this->delete($pt,$rk,$removeval);
							}
						}
					}
				}
			}
		}
	}
}