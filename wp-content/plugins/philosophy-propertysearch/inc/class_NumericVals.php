<?php
class NumericVals {
	var $fields;
	function __construct() {

	}

	function get_value($post_id, $field_name) {
		global $wpdb;
		$q = 'select numericvals_id, value from propsrch_numericvals
				where
					post_id = '.$post_id.' AND
					field_name = "'.$field_name.'"';
// 		echo $q.'<br>';
		$r = $wpdb->get_row($q);
// 		ppr($r);
		return($r);
	}
	function delete ($post_type, $field_name, $value) {
		global $wpdb;
		$q = 'delete from propsrch_numericvals
				where 
					post_type = "'.$post_type.'" AND 
					field_name = "'.$field_name.'" AND
					value = "'.$value.'"';
// 		echo $q;
		$wpdb->query($q);
		
	}
	function insert($post_id, $field_name, $value) {
		global $wpdb;
		$q = 'insert into propsrch_numericvals
				(
				post_id,
				field_name,
				value
				)
				VALUES
				("'.$post_id.'",
				"'.$field_name.'",
				'.$this->sanitise($value).')';
		$wpdb->show_errors();
		$wpdb->query($q);
	}
	function update ($post_id, $field_name, $value) {
		global $wpdb;
		$q = 'update propsrch_numericvals
				set value = '.$this->sanitise($value).' where post_id = '.$post_id.' and field_name = "'.$field_name.'"';
		$wpdb->query($q);
	}
	function smart_update ($post_id, $field_name, $value) {
		$existing = $this->get_value($post_id, $field_name);
		if (empty($existing)) {
			$this->insert($post_id, $field_name, $value);
		} else {
			$this->update($post_id, $field_name, $value);
		}
	}
	function sanitise ($value) {
		//echo 'SANVAL: '.$value.'<Br>';
		if (empty($value)) {
			return(0);
		} else {
			$v = preg_replace('/[^\d.]+/','',$value);
			$v = ($v > 0) ? $v : 0;
			return($v);
			
		}
	}
	function getAllMax () {
		global $wpdb;
		$q = 'select field_name, max(value) as max_value from propsrch_numericvals group by field_name';
		$r =  $wpdb->get_results($q);
		return($r);
	}
	function getMax ($field_name) {
		global $wpdb;
		$q = 'select field_name, max(value) as max_value from propsrch_numericvals where field_name = "'.$field_name.'"';
		$r =  $wpdb->get_row($q);
		if (!empty($r)) {
			return($r->max_value);
		}
	}
	function getMin ($field_name, $allow_zero = true) {
		global $wpdb;
		$q = 'select field_name, min(value) as min_value from propsrch_numericvals where field_name = "'.$field_name.'"';
		if (!$allow_zero) {
			$q .= ' and `value` > 0';
		}
		
		$r =  $wpdb->get_row($q);
		if (!empty($r)) {
			return($r->min_value);
		}
	}
	function getCount ($field_name) {
		global $wpdb;
		$q = 'select count(numericvals_id)  as valcount from propsrch_numericvals';
		$r =  $wpdb->get_row($q);
		if (!empty($r)) {
			return($r->valcount);
		}
		
	}
	function getAverage($field_name) {
		global $wpdb;
		$q = 'select count(numericvals_id) as valcount, sum(`value`) as valsum from propsrch_numericvals';
		$r =  $wpdb->get_row($q);
		if (!empty($r)) {
			return($r->valsum / $r->valcount);
		}
		
	}
	function getAll($field_name) {
		global $wpdb;
		$q = 'select * from propsrch_numericvals where field_name = "'.$field_name.'" order by `value`';
		$r =  $wpdb->get_results($q);
		return($r);
	}
	function clean () {
		global $wpdb;
		$q = 'select numericvals_id
				from propsrch_numericvals 
				left join '.$wpdb->posts.' p on post_id = p.ID
				where ID IS NULL';
		$r =  $wpdb->get_results($q);
		$del = [];
		foreach ($r as $rr) {
			$del[] = $rr->numericvals_id;
		}
		if ((!empty($del)) && (is_array($del))) {
			$q = 'delete from propsrch_numericvals where numericvals_id IN ('.implode(',',$del).')';
			$wpdb->query($q);
		}
	}
	function generateRanges ($field_name, $ideal_number_of_choices = 10) {
		global $wpdb;
		$wpdb->show_errors(1);
		
		$min = $this->getMin($field_name, false);
		$max = $this->getMax($field_name);
		$average = $this->getAverage($field_name);
		
		
		$mid = 0;
		$tmp = $max * 2; // force it out of our range
		
		$r = $this->getAll($field_name);
		
		foreach ($r as $rr) {
			$dif = abs($rr->value - $average);
			if (($rr->value > $average) && ($dif > $tmp))  {
				break;
			}
			if ($dif < $tmp) {
				$mid = $rr->value;
				$tmp = $dif;
			}
		}	
		
		//echo '<br>Avg: '.$average.'<br>Min: '.$min.'<br>Mid: '.$mid.'<br>Max: '.$max.'<hr>';
		//ppr($byval);
		
		$ranges = [];
		
		$inc = $mid / ($ideal_number_of_choices / 2);
		$dp = str_pad(10, round(strlen($inc)/2), '0', STR_PAD_RIGHT); 
		$inc = round($inc/$dp)*$dp;
		$nmax = (ceil($max / $inc)) * $inc;
		
		$v = $inc;
		
		$l = 0;
 		while ($v <= $nmax) {
 			$q = 'select numericvals_id
				from propsrch_numericvals
				where `value` > '.$l.' and `value` <= '.$v;
 			$r =  $wpdb->get_results($q);
 			if (!empty($r)) {
 				$ranges[] = [$l,$v];
 				$l = $v;
 			}
 			
 			$v = $v + $inc;
 		}
 		return($ranges);
	}
}