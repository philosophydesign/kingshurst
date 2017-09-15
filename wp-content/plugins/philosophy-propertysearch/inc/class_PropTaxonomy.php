<?php
class PropTaxonomy {
	function __construct() {

	}
	
	function deleteterm ($id) {
		global $wpdb;
		$q = 'delete from propsrch_taxonomy_terms
				where 
					term_id = '.$id;
// 		echo $q;
		$wpdb->query($q);
		
	}
	function insertterm($term_name, $value) {
		global $wpdb;
		$q = 'insert into propsrch_taxonomy_terms
				(
				category,
				term_value
				)
				VALUES
				("'.$term_name.'",
				"'.trim($value).'")';
		$wpdb->query($q);
	}
	function getTermsByCat ($cat) {
		global $wpdb;
		$q = 'select * from propsrch_taxonomy_terms where category = "'.$cat.'" ORDER by term_value';
		#echo $q;
		#$wpdb->show_errors();
		$r = $wpdb->get_results($q);
		return($r);	
	}
	
	function updateterm ($term_id, $value) {
		global $wpdb;
		$q = 'update propsrch_taxonomy_terms set term_value = "'.$value.'" where term_id = '.$term_id;
		#$wpdb->show_errors();
		$r = $wpdb->get_results($q);
		return($r);	
	}
	function getValByPostAndCat ($post_id, $cat) {
		global $wpdb;
		$q = 'select * from  propsrch_taxonomy_links tl 
				left join propsrch_taxonomy_terms tt ON tt.term_id = tl.term_id
				where category = "'.$cat.'" and tl.post_id = '.$post_id;
// 		$wpdb->show_errors();
		$r = $wpdb->get_results($q);
		return($r);
	}
	function getValByPostAndTermId ($post_id, $term_id) {
		global $wpdb;
		if (empty($term_id)) {
			return ("");
		}
		$q = 'select term_value from propsrch_taxonomy_links tl 
				left join propsrch_taxonomy_terms tt ON tt.term_id = tl.term_id
				where tl.term_id = '.$term_id.' and tl.post_id = '.$post_id;
		$r = $wpdb->get_row($q);
		if (!empty($r)) {
			return($r->term_value);
		} else {
			return("");
		}
	}
	function flushLinks($post_id, $term_id) {
		global $wpdb;
		$q = 'delete from propsrch_taxonomy_links  where post_id = '.$post_id.' and term_id ';
		if (is_array($term_id)) {
			$q .= ' IN ('.implode(',',$term_id).')';
		} else {
			$q .= ' = '.$term_id;
		}
		
		$r = $wpdb->query($q);
	}
	function smartUpdateTerm ($post_id, $category, $val) {
		global $wpdb;
		$wpdb->show_errors();
		
		
		
		if(is_array($val)) {
			$terms = $this->getTermsByCat($category);
			$term_ids = array();
			foreach ($terms as $t) {
				$term_ids[] = $t->term_id;
			}
			$this->flushLinks($post_id, $term_ids);
			foreach ($val as $f_term_id) {
				$this->inserttermlink($post_id, $f_term_id);
			}
			return true;
		} else if (!is_numeric($val)) {
			$term_id = $this->getTermByIdByValueAndCat($val, $category);
		} else {
			$term_id= $val;
		}
		
		$existing = $this->getValByPostAndTermId($post_id, $term_id);
		if (empty($existing)) {
			$this->inserttermlink($post_id, $term_id);
		} else {
			$this->updatetermlink($post_id, $term_id);
		}
	}
	function getTermByIdByValueAndCat($val, $cat) {
		global $wpdb;
		$q = 'select * from propsrch_taxonomy_terms where category = "'.$cat.'" and term_value="'.$val.'"';
// 		echo $q.'<br>';
		$r = $wpdb->get_row($q);
		if (!empty($r)) {
			return($r->term_id);
		} else {
			return;
		}
	}
	function inserttermlink($post_id, $term_id, $sort=0) {
		if (empty($term_id)) {
			return ("");
		}
		global $wpdb;
		$q = 'insert into propsrch_taxonomy_links
				(
				post_id,
				term_id,
				sort
				)
				VALUES
				('.$post_id.',
				'.$term_id.', 
				'.$sort.'
				)';
		$wpdb->query($q);
	}
	function updatetermlink ($post_id, $term_id, $sort=0) {
		global $wpdb;
		$q = 'update  propsrch_taxonomy_links
				set 
					term_id = '.$term_id.'
					sort = '.$sort.'
				where 
				post_id = '.$post_id.'
						';
		$wpdb->query($q);
	}
}