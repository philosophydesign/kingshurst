<?php
class SrchCache {
	var $datetime;
	var $NV;
	var $saved_heirachy;

	var $tenureopt;
	var $imagesize;
	var $acffields;
 	var $template;
	var $PS;
	
	function __construct() {
		$this->datetime = date('Y-m-d H:i');
		$this->NV = new NumericVals();
		$this->PS = new PropertySearch();
		
		$this->tenureopt = get_option('propsrch_tenureoption');
		$this->imagesize = get_option('propsrch_featured_image');
		$this->acffields = get_acf_fields();
 		$this->template = get_option('propsrch_resulttemplate');
		$this->saved_heirachy = $this->PS->propsrch_heiropt();
	}

	function get() {
		global $wpdb;
		$q = 'select results from propsrch_srchcache where query = "'.$this->makeSearchQueryString().'"';
		$r = $wpdb->get_row($q);
		if (!empty($r)) {
// 			echo 'USING CACHE';
			$r = json_decode($r->results);
			return($r);
		} else {
// 			echo 'NO CACHE - GENERATE';
			return(array());
		}
	}
	function clearcache ($post_id) {
		global $wpdb;
		/*
		if (!empty($post_id)) {
			$q = 'select * from propsrch_srchcache';
			$r = $wpdb->get_results($q);
			foreach ($r as $sc) {
				$res = json_decode($sc->results);
				ppr($res);
				exit;
			}
			
		} else {
		*/
			$q = 'TRUNCATE propsrch_srchcache;';
			$wpdb->query($q);
			/*
		}
		*/
		
	}
	function get_pp($post_id) {
		global $wpdb;
		$q = 'select * from propsrch_ppsrchcache where post_id = '.$post_id;
		$r = $wpdb->get_row($q);
		if (!empty($r)) {
			return $r;
		} else {
			return false;
		}
	}
	function update_pp($post_id, $datajson) {
		global $wpdb;
		
		$exists = $this->get_pp($post_id);
		if (empty($exists)) {
		
			$q = 'insert into propsrch_ppsrchcache
					(
					post_id,				
					data
					)
					VALUES
					("'.$post_id.'",
					"'.esc_sql($datajson).'")';
			$wpdb->show_errors();
			$wpdb->query($q);
			return($wpdb->insert_id);
		} else {
			$q = 'update propsrch_ppsrchcache set data = "'.esc_sql($datajson).'" where post_id = '.$post_id;
			$wpdb->query($q);
			
			return false;
		}
	}
	function insert($results, $srcsql) {
		global $wpdb;
		/*
		$ids = array();
		foreach ($result as $i) {
			$ids[] = $i->ID;	
		}
		*/
		$q = 'insert into propsrch_srchcache
				(
				datetime,
				query,
				srcsql,
				results
				)
				VALUES
				("'.$this->datetime.'",
				"'.$this->makeSearchQueryString().'",
				"'.esc_sql($srcsql).'",
				"'.esc_sql(json_encode($results)).'")';
		$wpdb->show_errors();
		$wpdb->query($q);
	}
	function makeSearchQueryString() {
		$r = json_encode($_GET);
		return(md5($r));
		/*
		$sq = array();
		foreach ($_GET as $k => $v) {
			if (!empty($v)) {
				$sq[$k] = $v;
			}
		}
		$sq = json_encode($sq);
		return($sq);
		*/
	}
	function buildCacheValues ($r) {
		
		
		
		
		$foundfields = array();
// 		$P = new Properties();
		
		
		$resulthtml = stripslashes($this->template);
		$foundfields[$x][] = array('post_id',$r->ID);
		foreach ($this->acffields as $f) {
			$fn = '*|acf_'.$f['name'].'|*';
			if (strstr($resulthtml, $fn)) {
				$v = get_field($f['name'], $r->ID);
				$foundfields[$x][] = array($fn,$v);
			}
		}
		foreach ($this->PS->result_variables as $k=>$v) {
			if (!empty($v['var'])) {
				if (!empty($v['gen'])) {
					$new = $this->PS->generate_var($k, $v, $r->ID, $saved_heirachy);
				} else if (!empty($r->$v['var'])) {
					$new = $r->$v['var'];
				} else {
					$new = "";
				}
					
				if ($v['var'] == 'total_area') {
					$mult = $this->PS->whatMult();
					// 									$new = number_format(ceil($new * $mult));
		
					if (($_GET['un'] == 'sqm') || (empty($_GET['un']))) 		{$new .= ' Sq M';}
					else if ($_GET['un'] == 'sqf') 								{$new .= ' Sq Ft';}
					else if ($_GET['un'] == 'acr') 								{$new .= ' AC';}
					else if ($_GET['un'] == 'hec') 								{$new .= ' ha';}
				}
					
					
				if (($jsonmode) && ($new == "")) {
					$new = "=!U!=";
				}
				$foundfields[$x][] = array($k, $new);
					
			}
		}
		preg_match_all('/\*{([a-zA-Z]+)/', $resulthtml, $funcmatch);
		$foundfields[$x]['funcvals'] = array();
		if (!empty($funcmatch[1])) {
			foreach($funcmatch[1] as $f) {
				$fnc = preg_replace('/[\*{}]/','',$f);
				$fn = 'PROPSRCH_resfunc_'.$fnc;
				if (function_exists($fn)) {
					$html = $fn($r);
				} else {
					$html = $fn;
				}
		
				$foundfields[$x]['funcvals'][] = [$fnc,$html];
		
			}
		}
		$json = json_encode($foundfields[$x]);
		$this->update_pp($r->ID, $json);
	}
	
}