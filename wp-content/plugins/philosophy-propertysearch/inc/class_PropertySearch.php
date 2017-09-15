<?php
if (empty($_SESSION)) {
	session_start();
}
class PropertySearch {
	var $admin_prefix;
	var $heirachy;
	var $buyingopts;
	var $tenureopts;
	var $tenureopts_friendly;
	var $deactivatable;
	var $result_variables;
	var $status_list;
	var $gesf_search_fields;
	var $search_sql;
	function __construct() {
		$this->admin_prefix = 'propsrch_';
		$this->heirachy = array(
			'location'		=>	array('label'=>'Locations',		'cpt'=>'locations'),
			'development'	=>	array('label'=>'Developments',	'cpt'=>'developments'),
			'collection'		=>	array('label'=>'Collections',		'cpt'=>'collections')
		);
		$this->buyingopts = array(
			'private'		=>	array('label'=>'Private Sale'),
			'shared'		=>	array('label'=>'Shared Ownership'),
			'resale'		=>	array('label'=>'Resales'),
			'equityloan'	=>	array('label'=>'Equity Loans')
		);
		$this->tenureopts = array(
			'freehold'		=>	array('label'=>'Freehold'),
			'leasehold'		=>	array('label'=>'Leasehold'),
			'longlease'		=>	array('label'=>'Long Leasehold'),
		);
		$this->tenureopts_friendly = array(
			'sale'			=>	array('label'=>'Sale',	'inc'=>array('freehold')),
			'rent'			=>	array('label'=>'Rent',	'inc'=>array('leasehold','longlease')),
		);
		$this->searchoptions = array(
			'keywords'		=>	array('label'=>'Keywords'),
			'location'		=>	array('label'=>'Location'),
			'size'			=>	array('label'=>'Size'),
			'price'			=>	array('label'=>'Price'),
			'no_bedrooms'	=>	array('label'=>'Bedrooms'),
			'tenure'		=>	array('label'=>'Tenure'),
			
		);
		$this->deactivatable = array(
			'price'			=>	array('label'=>'Price'),
			'no_bedrooms'	=>	array('label'=>'Bedrooms'),
			'address'		=>	array('label'=>'Address'),
		);
		$this->result_variables = array(
			'image'			=>	array('label'=>'Featured Image','var'=>'featured_image', 'gen'=>true),
			'post_title'	=>	array('label'=>'Property title','var'=>'post_title'),
			//'post_content'	=>	array('label'=>'Body text','var'=>'post_content'),
			'list_desc'		=>	array('label'=>'List Description','var'=>'list_desc'),
			'location'		=>	array('label'=>'Location','var'=>'location', 'gen'=>true),
			'no_bedrooms'	=>	array('label'=>'Number of bedrooms','var'=>'no_bedrooms'),
			'linktoproperty'=>	array('label'=>'Permalink','var'=>'permalink', 'gen'=>true),
			'price'			=>	array('label'=>'Price of property','var'=>'price','gen'=>true),
			'tenure'		=>	array('label'=>'Tenure','var'=>'tenure','gen'=>true),
			'total_area'	=>	array('label'=>'Total Area','var'=>'total_area','gen'=>true),
			'address'		=>	array('label'=>'Address','var'=>'address','gen'=>true),
			'latlng'		=>	array('label'=>'LatLng','var'=>'latlng'),
		);
		$this->status_list = array(
			'available'		=>	array('label'=>'Available','sor'=>'rent'),
			'forsale'		=>	array('label'=>'For Sale','sor'=>'sales'),
			'let'			=>	array('label'=>'Let','sor'=>'rent'),
			'sold'			=>	array('label'=>'Sold','sor'=>'sales'),
			'underoffer'	=>	array('label'=>'Under Offer','sor'=>'sales'),
			'reserved'		=>	array('label'=>'Reserved','sor'=>'both'),
		);
		/*
		$this->type_list = array(
			'studio'		=>	array('label'=>"Studio", 'cor'=>'resi'), 
			'house'			=>	array('label'=>"House", 'cor'=>'resi'), 
			'apartment'		=>	array('label'=>"Apartment", 'cor'=>'resi'),
				
			'development'	=>	array('label'=>"Development", 'cor'=>'comm'),
			'healthcare'	=>	array('label'=>"Healthcare", 'cor'=>'comm'),
			'industrial'	=>	array('label'=>"Industrial", 'cor'=>'comm'),
		);
		*/
		$this->gesf_search_fields = array();
	}
	function get_searchform_struct () {
		$PS = new PropertySearch();
		$saved_searchoptions = $PS->propsrch_searchoptions();
		$str = [];
		
		
		if (!empty($saved_searchoptions['keywords'])) {
			$str[] = ['Keywords', 'k', 0];
		}  
		if (!empty($saved_searchoptions['tenure'])) {
			$list = [''=>'All'];
			if (get_option('propsrch_tenureoption') == 'friendly') {
				foreach ($this->tenureopts_friendly as $k => $t) {
					$list[$k] = $t['label'];
				}
			} else {
				foreach ($this->tenureopts as $k => $t) {
					$list[$k] = $t['label'];
				}
			}
				
			$str[] = ['Tenure', 'tn', 0, 'radiorefset', $list];
		}
		
		$saved_heirachy = $this->propsrch_heiropt();
		
		if (!empty($saved_heirachy['development'])) {
			$str[] = ['Development', 'dv', 0, 'select',	development_list()];
		} 
		
		$str[] = ['Property Type',		'pt',	0,	'select',	property_type_list(true)];
		
		if (!empty($saved_heirachy['collection'])) {
			$str[] = ['Collection', 'co', 0, 'select',	collection_list()];
		} 
		if (
				(!empty($saved_heirachy['location'])) &&
				(!empty($saved_searchoptions['location']))
				){
			$str[] = ['Location', 'lo', 0, 'multiselect',	locations_list()];
		} else if (
				(empty($saved_heirachy['location'])) &&
				(empty($saved_heirachy['development'])) &&
				(empty($saved_heirachy['collection'])) && 
				(!empty($saved_searchoptions['location']))
				) {
			$str[] = ['Location', 'lo', 0, 'multiselect',	fieldcache_list('properties','location')];
		}
		
 		if (!empty($saved_searchoptions['price'])) {
			$str[] = ['Price', 'pr', 0, 'multitextbox', ['options'=>['prf'=>'From','prt'=>'To'],'count'=>1]];
 		}
		if (!empty($saved_searchoptions['size'])) {
			$rangetype = get_option('propsrch_fieldmech_totalarea');
			if ($rangetype == 'prange') {
				$ranges = json_decode(get_option('propsrch_total_area_ranges'));
				$options = [];
				foreach ($ranges as $r) {
					$options[$r[0].' - '.$r[1]] = number_format($r[0]).' - '.number_format($r[1]);
				}
				$str[] = ['Size', 'sz', 0, 'ref_select', $options];
			} else {
				$str[] = ['Size', 'sz', 0, 'multitextbox', ['options'=>['szf'=>'From','szt'=>'To'],'count'=>1]];
			}
			$str[] = ['Unit', 'un', 0, 'ref_select', ['sqm'=>'Sq M','sqf'=>'Sq Ft','acr'=>'Acres','hec'=>'Hectacres']];
		}
		
		
		

		$saved_posttypes = $PS->propsrch_posttypes();
		foreach ($saved_posttypes as $k => $v) {
			if (($v->linked) && ($v->searchable)) {
				$pt = get_post_type_object($k);
				$make = $v->multiple ? 'multiselect' : 'ref_select';
	 			$str[] =  [$pt->labels->name,	$this->makeLinkedFieldName($k),	0,	$make,	post_list($k, FALSE)];
			}
		}
		
		return($str);
	}
	function makeLinkedFieldName ($k) {
		return('l'.preg_replace('/[aeiou]/i','',$k));
	}
	function outputSearchForm($exclude) {
		
		$GF = new GesForms();
		$GF->fieldClasses = array();
		$GF->unique = '-'.rand(100,999);
		$GF->placeholder = array(
				'area'				=>	'Select from list',
				'development'		=>	'Select from list',
				'buying_options'	=>	'Select from list',
				'price_min'			=>	'Minium',
				'price_max'			=>	'Maximum',
		);
		$structure = $this->get_searchform_struct();
		$structure[] = ['dopropsrch','dopropsrch',0,'hidden'];
		$GF->values['dopropsrch'] = true;
		$GF->values['un'] = 'sqm';
		
		$concealed = array('multitextbox');
		if ((!empty($_GET['dopropsrch'])) || (!empty($_SESSION['GET_CACHE']))) {
			foreach ($structure as $s) {
				if (in_array($s[3], $concealed)) {
					if (isset($s[4]['options'])) {
						foreach ($s[4]['options'] as $k => $o)  {
							if (isset($_GET[$k])) {
								$GF->values[$k] = $_GET[$k];
							}
						}
					}
				} else {
					if (isset($_GET[$s[1]])) {
						$GF->values[$s[1]] = $_GET[$s[1]];
					} else if (isset($_SESSION['GET_CACHE'][$s[1]])) {
						$GF->values[$s[1]] = $_SESSION['GET_CACHE'][$s[1]];					
					}
				}
			}
		}

		$output = $GF->outputFields($GF->makeStructObj($structure));
		
		$this->gesf_search_fields = $GF->generatedfields;
		return($output);
	}
	function get_mult_opt ($n) {
		$o = get_option($n);
		$o = (array) json_decode($o);
		return($o);
}
	function propsrch_heiropt () {
		return($this->get_mult_opt('propsrch_heirachy'));
	}
	function propsrch_deactivatedfields () {
		return($this->get_mult_opt('propsrch_deactivatedfields'));
	}
	function propsrch_searchoptions () {
		return($this->get_mult_opt('propsrch_searchoptions'));
	}
	function propsrch_buyingoptions () {
		return($this->get_mult_opt('propsrch_buyingoptions'));
	}
	function propsrch_posttypes () {
		return($this->get_mult_opt('propsrch_posttypes'));
	}
	function ouput_admin_fields($fields, $cached=array(), $numeric=array(), $taxfields=array()) {
		global $post;
		$PS = new PropertySearch();
		
		$saved_deactivatedfields = $PS->propsrch_deactivatedfields();
// 		ppr($saved_deactivatedfields);
		$FC = new FieldCache();
		$NV = new NumericVals();
		$PT = new PropTaxonomy();
		
		$placeholders = [];
		foreach ($fields as $n => $f) {
			if (!empty($saved_deactivatedfields[$f[1]])) {
				unset($fields[$n]);
				continue;
			}
			if (in_array($f[1], $cached)) {
				$fields[$n][3] = 'textfield_withselect';
				$fields[$n][4] = $FC->getVals($post->post_type, $f[1], true);
				$placeholders[$f[1].'_choose'] = '+ add new';
			}
		}
		
		$GF = new GesForms();
		$GF->fieldPrefix = $this->admin_prefix;
		$stucture = $GF->makeStructObj($fields);
		$GF->fieldValsFromPostMeta($post->ID, $stucture);
		if (!empty($placeholders)) {
// 			ppr($placeholders);
			$GF->placeholder = $placeholders;
// 			echo 'Setting placeholder<br>';
// 			ppr($GF->placeholder);
		} else {
// 			echo 'Empty placeholders';
		}
		$saved_areainputunits= get_option('propsrch_areainputunits');
		
		foreach ($numeric as $n) {
			$val = $NV->get_value($post->ID, $n);
			if (($n == 'total_area') && ($saved_areainputunits == 'imperial')) {
				$GF->values[$n] = round($val->value * RATIO_SQUARE_FEET_METRES,1);
			} else {
				$GF->values[$n] = $val->value;
			}

		}
		//total_area
		foreach ($taxfields as $n) {
			$val = $PT->getValByPostAndCat($post->ID, $n);
			$vals = [];
			foreach ($val as $v) {
				$vals[] = $v->term_id;
			}
			$GF->values[$n] = $vals;
		}
		$vals = getPostLinks($post->ID);
		$saved_posttypes = $PS->propsrch_posttypes();
		foreach ($saved_posttypes as $k => $v) {
			if ($v->linked) {			
 				$fk = 'linked_'.$k;
 				if ($v->multiple) {
 					$list = [];
 					if (isset($vals[$k])) {
 						foreach ($vals[$k] as $p) {
 							$list[] = $p->ID;
 						}
 					}
 					$GF->values[$fk] = $list;
 				} else {
 					$GF->values[$fk] = $vals[$k][0]->ID;
 				}
				$GF->fieldClasses[$fk] = 'relateditem';
			}
		}
		echo $GF->outputFields($stucture);
	}
	function makeResults () {
		global $PROPSRCH_results;
		$struct = $this->get_searchform_struct();
		$saved_heirachy = $this->propsrch_heiropt();
		
		$SC = new SrchCache();
		//$cachedresult = $SC->get();
		$cachedresult = [];
		if (empty($cachedresult)) {
			
			$PRO = new Properties();
			$FC = new FieldCache();
			
			$saved_searchoptions = $this->propsrch_searchoptions();
			$property_types = get_psterms('property_type');
			global $wpdb;
			$sql_select = [];
			$sql_join = [];
			$sql_where = [];
			
			$keywordscols = array('p.post_title','p.post_content');
			if (empty($this->deactivatable['tenure'])) {
				$keywordscols[] = 'pm_tnr.meta_value';
			}
			$saved_posttypes = $this->propsrch_posttypes();
			foreach ($saved_posttypes as $k => $v) {
				if (($v->linked) && ($v->searchable)) {
					$fieldname = $this->makeLinkedFieldName($k);
					if (!empty($_GET[$fieldname])) {
						
						$args = [
							'post_type'		=>$k,
							'post_status'	=>'publish',
							'orderby'		=>'post_name',
							'order'			=>'ASC'
						];
						$posts = get_posts($args);
						$get_post_list = $_GET[$fieldname];
						
						$get_post_ids = array();
						foreach ($posts as $p) {
							if (!in_array($p->post_title,$get_post_list))	 {
								unset($get_post_list[$k]);
							} else {
								$get_post_ids = array_merge($get_post_ids, getPIDsFromLink($p->ID));
							}
						}
						if (!empty($get_post_ids)) {
							$sql_where[$fieldname] =' p.ID IN ('.implode(',',$get_post_ids).')';
						}
						if (!$v->multiple) {
						}
					}
				}
			}
			foreach ($struct as $s) {
				if (isset($s[1])) {
					
					switch ($s[1]) {
						/* ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-*/
						case 'k': // Keywords
							if (!empty($_GET[$s[1]])) {
								$keywords = explode(' ',strtolower(preg_replace('/[^\w\s]/','',$_GET['k'])));
								$w = [];
								$noisewords = [
									'the','and','a','to','of','in','i','is','that','it','on','you','this','for','but','with','are','have','be','at','or','as','was','so','if','out','not'
								];
								$ptt = 0;
								$keywords_prop_type_where = "";
								foreach ($keywords as $k) {
									if ((in_array($k, $noisewords)) || (empty($k)))  {
										continue;
									}
									$wks = [];
									foreach ($keywordscols as $col) {
										$wks[] = $col." LIKE '%$k%' ";
									}
									$w[] = implode(' OR ', $wks);
									$sel = array();
									$whe = array();
									foreach ($property_types as $pt) {
										if (stristr($pt->term_value, $k)) {
											$sel[] = "if (ptt$ptt.term_id IS NOT NULL, 'true', 'false') as has_".strtolower(preg_replace("/\W/", "", $pt->term_value));
											$whe[] = "ptt$ptt.term_id IS NOT NULL";
											$sql_join['keywords_prop_type'] .= 'LEFT JOIN propsrch_taxonomy_links ptt'.$ptt.' ON (term_id = '.$pt->term_id." and ptt$ptt.post_id = p.ID)";
										}
									}
									if (!empty($sel)) {
										$sql_select['keywords_prop_type'] = implode(", ", $sel);
										$keywords_prop_type_where .= "(".implode(" OR ", $whe).")";
									}
									$ptt++;
								}
								if (!empty($w)) {
									$sql_where['keywords'] = "(".implode(" AND ", $w).")";
									if ((!empty($keywords_prop_type_where)) && (!empty($sql_where['keywords']))) {
										$sql_where['keywords'] = "(".$keywords_prop_type_where.' OR '.$sql_where['keywords'].")";
									} elseif (!empty($keywords_prop_type_where)) { 
										$sql_where['keywords'] = $keywords_prop_type_where;
									}
								}
								
								
									
							}
						break;
						/* ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-*/
						
						case 'lo': // Location
							if (!empty($_GET[$s[1]])) {
								
								$locations = $_GET[$s[1]];
								
								if (!empty($locations[0])) {
									if (!empty($saved_heirachy['location'])) {
										// The meta value - will be a post ID - we join that too
										$sql_select['location'] = 'p_locp.post_title as location';
										$sql_join['location'] = 'left join '.$wpdb->postmeta.' pm_locp on p.ID = pm_locp.post_id AND pm_locp.meta_key = "propsrch_location_post"
												left join '.$wpdb->posts.' p_locp on p_locp.ID = pm_locp.post_id';
										$sql_where['location'] = 'p_locp.post_title IN ("'.implode('","',$_GET[$s[1]]).'")';
										
									} else {
										$loclist = $FC->getVals('properties', 'location', true);
										$getlocs = $_GET[$s[1]];
										foreach ($getlocs as $k => $l) {
											if (!in_array($l, $loclist)) {
												unset($getlocs[$k]);
											}
										}
										// Use the meta value as is
										$sql_select['location'] = 'pm_loc.meta_value as location';
										$sql_join['location'] = 'left join '.$wpdb->postmeta.' pm_loc on (p.ID = pm_loc.post_id AND pm_loc.meta_key = "propsrch_location")';
										$sql_where['location'] = 'pm_loc.meta_value IN ("'.implode('","',$getlocs).'")';
									}
								} else {
// 									die("NO LOCATION");
								}
							}
						break;
						/* ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-*/
						case 'tn': // Tenure
							if (!empty($_GET[$s[1]])) {
								if (!empty($saved_searchoptions['tenure'])) {
									$list = [];
									if (get_option('propsrch_tenureoption') == 'friendly') {
										foreach ($this->tenureopts_friendly as $k => $t) {
											if ($k == $_GET[$s[1]]) {
												$sql_where['tenure'] = 'pm_tnr.meta_value IN ("'.implode('","', $t['inc']).'")';
												break;
											}
										}
									} else {
										$sql_where['tenure'] = 'pm_tnr.meta_value =  "'.esc_sql($_GET[$s[1]]).'"';
									}
								}					
							}
						break;
						/* ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-*/
						case 'sz': // Size
							
							$rangetype = get_option('propsrch_fieldmech_totalarea');
							$NV = new NumericVals();
							
							if ($rangetype == 'prange') {
								if (!empty($_GET['sz'])) {
									$ft = explode(' - ', $_GET['sz']);
									$from = $ft[0];
									$to = $ft[1];
								} else {
									$from = 0;
									$to = $NV->getMax('total_area');
								}
								
							} else {
							
								$mult = $this->whatMult();
								$from = (!empty($_GET['szf'])) ? ($_GET['szf'] / $mult): 0;  
								$to = (!empty($_GET['szt'])) ? ($_GET['szt'] / $mult) : $NV->getMax('total_area');  
							}
							if (empty($to)) {
								$to = 100000;
							}
							$sql_where['size'] = '(nv_siz.value >= '.$from.' and nv_siz.value <= '.$to.')';
						
						break;
						/* ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-*/
							
						case 'pt': // Property Type
							$requested_property_types = (array) $_GET['pt'];
	// 						ppr($requested_property_types);
							if (isset($requested_property_types[0])) {
								
								$ptl = 1;
								$sel = array();
								$whe = array();
								foreach ($property_types as $pt) {
									if (in_array($pt->term_value, $requested_property_types)) {
										$sel[] = "if (ptl$ptl.term_id IS NOT NULL, 'true', 'false') as has_".strtolower(preg_replace("/\W/", "", $pt->term_value));
										$whe[] = "ptl$ptl.term_id IS NOT NULL";
										$sql_join['prop_type'] .= 'LEFT JOIN propsrch_taxonomy_links ptl'.$ptl.' ON (ptl'.$ptl.'.term_id = '.$pt->term_id." and ptl$ptl.post_id = p.ID)";
										
	// 									echo 'Found property type: '.$pt->term_value.'<br>';
									} else {
	// 									echo 'No property type: '.$pt->term_value.'<br>';
									}
									$ptl++;
								}
								if (!empty($sel)) {
									$sql_select['prop_type'] = implode(", ", $sel);
									$sql_where['prop_type'] = "(".implode(" OR ", $whe).")";
								}
							}
						break;
						/* ~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-~-*/
							
					}
				}
			}
			$sql_select['list_desc'] = 'pm_ld.meta_value as list_desc';
			$sql_join['list_desc'] = 'left join '.$wpdb->postmeta.' pm_ld on (p.ID = pm_ld.post_id AND pm_ld.meta_key = "propsrch_list_desc")';
			
			
			if ((empty($this->deactivatable['bedrooms'])) && (empty($sql_select['bedrooms']))) {
				$sql_select['bedrooms'] = 'pm_bed.meta_value as no_bedrooms';
				$sql_join['bedrooms'] = 'left join '.$wpdb->postmeta.' pm_bed on (p.ID = pm_bed.post_id AND pm_bed.meta_key = "propsrch_bedrooms")';
			}
			if ((empty($this->deactivatable['price'])) && (empty($sql_select['price']))) {
				$sql_select['price'] = 'pm_prc.value as price';
				$sql_join['price'] = 'left join propsrch_numericvals pm_prc on (p.ID = pm_prc.post_id AND pm_prc.field_name = "propsrch_price")';
			}
			if ((!empty($this->deactivatable['tenure'])) && (!empty($sql_select['tenure']))) {
				
			} else {
				$sql_select['tenure'] = 'pm_tnr.meta_value as tenure';
				$sql_join['tenure'] = 'left join '.$wpdb->postmeta.' pm_tnr on (p.ID = pm_tnr.post_id AND pm_tnr.meta_key = "propsrch_tenure")';
			}
			
			$sql_select['size'] = 'nv_siz.value as total_area';
			$sql_join['size'] = 'left join propsrch_numericvals nv_siz on (p.ID = nv_siz.post_id AND nv_siz.field_name = "total_area")';
			
			$sql_select['ppsrchcache'] = 'ppsc.data';
			$sql_join['ppsrchcache'] = 'left join propsrch_ppsrchcache ppsc on (p.ID = ppsc.post_id)';
			
			#$sql_select['address'] = 'pm_ad.meta_value as address';
			#$sql_join['address'] = 'left join '.$wpdb->postmeta.' pm_ad on (p.ID = pm_ad.post_id AND pm_ad.meta_key = "propsrch_address")';
			
			#$sql_select['latlng'] = 'pm_coords.meta_value as latlng';
			#$sql_join['latlng'] = 'left join '.$wpdb->postmeta.' pm_coords on (p.ID = pm_coords.post_id AND pm_coords.meta_key = "propsrch_latlng")';
			
			$sql_select = implode(" , ", $sql_select);
			if (!empty($sql_select)) {
				$sql_select = 'p.*, '.$sql_select;
			} else {
				$sql_select = 'p.* ';
			}
			$sql_where = implode(" AND ", $sql_where);
			if (!empty($sql_where)) {
				$sql_where = "\n\t AND ".$sql_where;
			}
			$sql_select = 'ID, ppsc.data';
			$q = 'select '.$sql_select.' from '.$wpdb->posts.' p 
		'.implode("\n\t ", $sql_join).'
					where p.post_type = "properties" and p.post_status = "publish"  '.$sql_where." group by p.ID";
			$sortby = (isset($_GET['sb'])) ? $_GET['sb'] : 'n';
			
			if (isset($sortby)) {
				switch ($sortby) {
					case 'n':
						$q .= " ORDER BY p.post_date DESC ";
					break;
					case 'o':
						$q .= " ORDER BY p.post_date ASC ";
					break;
					case 's':
						$q .= " ORDER BY total_area ASC ";
					break;
					case 'l':
						$q .= " ORDER BY total_area DESC";
					break;
				}
				$q .= '';
			}
			$wpdb->show_errors();
			$starttime = microtime(true);
			$search_result = $wpdb->get_results($q);
			$endtime = microtime(true);
// 			echo $endtime - $starttime;
// 			ppr($q);
			#exit;
//      		ppr($q);  		ppr($search_result); 		exit;
			
			/***** START RESULTS JSON ****/
			/*
			$NV = new NumericVals();
			
			$tenureopt = get_option('propsrch_tenureoption');
			$imagesize = get_option('propsrch_featured_image');
			$acffields = get_acf_fields();
			$template = get_option('propsrch_resulttemplate');
			$saved_heirachy = $this->propsrch_heiropt();
			$foundfields = array();
			#ppr($search_result);
			#exit;
			if (!empty($search_result)) {
				if (is_array($search_result)) {
					$x = 1;
					foreach ($search_result as $r) {
						// 					ppr($r);
						$resulthtml = stripslashes($template);
						$foundfields[$x][] = array('post_id',$r->ID);
						foreach ($acffields as $f) {
							$fn = '*|acf_'.$f['name'].'|*';
							if (strstr($resulthtml, $fn)) {
								$v = get_field($f['name'], $r->ID);
								$foundfields[$x][] = array($fn,$v);
							}
						}
						foreach ($this->result_variables as $k=>$v) {
							if (!empty($v['var'])) {
								if (!empty($v['gen'])) {
									$new = $this->generate_var($k, $v, $r->ID, $saved_heirachy);
								} else if (!empty($r->$v['var'])) {
									$new = $r->$v['var'];
								} else {
									$new = "";
								}
									
								if ($v['var'] == 'total_area') {
									$mult = $this->whatMult();
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
						$x++;
					}
			
					
				}
			}
			*/
			
			$reformed = [];
			$mult = $this->whatMult();
			foreach($search_result as $row) {
				$r = json_decode($row->data);
				foreach ($r as $x => $s) {
					if ($s[0] == 'total_area') {
						$n = preg_replace('/[^\d\.]/','',$s[1]);
						$new = number_format(ceil($n * $mult));
						if (($_GET['un'] == 'sqm') || (empty($_GET['un']))) 		{$new .= ' Sq M';}
						else if ($_GET['un'] == 'sqf') 								{$new .= ' Sq Ft';}
						else if ($_GET['un'] == 'acr') 								{$new .= ' AC';}
						else if ($_GET['un'] == 'hec') 								{$new .= ' ha';}
						$r->{$x}[1] = $new;
					}
				}
				$reformed[] = (array) $r;
			}
			/***** STOP RESULTS JSON ****/
			#$SC->insert($foundfields, $q);
			$PROPSRCH_results = $reformed;
		} else {
			//header('HTTP/1.0 304 Not modified');
			$PROPSRCH_results = $cachedresult;
		}
	}
	function whatMult () {
		if ($_GET['un'] == 'sqm') {$mult = 1;} 
		else if ($_GET['un'] == 'sqf') {$mult = RATIO_SQUARE_FEET_METRES;}
		else if ($_GET['un'] == 'acr') {$mult = 0.00024711;}
		else if ($_GET['un'] == 'hec') {$mult = 0.0001;}
		else {$mult = 1;}
		
		return($mult);
	}
	function makeResultsMapData ($jsonmode = false) {
		global $PROPSRCH_results;
		
		if (!empty($PROPSRCH_results)) {
			if (is_array($PROPSRCH_results)) {
				$markerjs = array();
				foreach ($PROPSRCH_results as $r) {
					$latlng = (!empty($r->latlng)) ? $r->latlng : '0,0';
					$markerjs[] = '["'.$r->post_title.'", '.$latlng.', "'.get_permalink($r->ID).'", "'.urlencode($r->address).'"]';
				}
				return('var mapmarkers = ['.implode(',', $markerjs).'];');
			}
		}
		
	}
	function getResultJSON() {
		global $PROPSRCH_results;
		return($PROPSRCH_results);
	}
	function makeResultsHTML () {
		global $PROPSRCH_results;
		if (!empty($PROPSRCH_results)) {
			if ((is_array($PROPSRCH_results)) || (is_object($PROPSRCH_results)))  {
				$template = get_option('propsrch_resulttemplate');
				$results = '';
				$x = 1;
				$return = '';
				
				$funcVals = getFuncVals();
				foreach ($PROPSRCH_results as $k => $r) {
					ppr($r);
					$resulthtml = stripslashes($template);
					
					foreach ($r as $k=>$replace) {
						if (($k == 'funcvals') && (!empty($replace[1]))) {
							foreach ($replace as $rplc) {
								$f =  (isset($rplc[0])) ? '*{'.$rplc[0].'}*' : '*{----}*';
								$w =  (isset($rplc[1])) ? $rplc[1] : ''; #?'.$rplc[0].'?';
								$resulthtml = str_replace($f,$w,$resulthtml);
							}
						} else {
							$f = '*|'.$replace[0].'|*';
							$resulthtml = str_replace($f,$replace[1],$resulthtml);
						}
					}
					$return .= $resulthtml;
				}
				return($return);
			}
		} else {
			//return('THERE ARE NO RESULTS');
			return(do_shortcode(stripslashes(get_option('propsrch_noresulttemplate'))));
		}
	}
	
	function generate_var ($variable, $v, $post_id, $saved_heirachy=array()) {
		/*
		image				- Done
		post_title			- NA
		post_content		- NA
		list_desc			- NA
		location			- Done
		no_bedrooms			- NA
		linktoproperty		- Done
		price				- Done	
		tenure				- Done
		total_area	
		address		
		*/
		
		
		if (empty($saved_heirachy)) {
			$saved_heirachy = $this->propsrch_heiropt();
		}
		
		$v = $this->result_variables[$variable];
		$new = $v['var'];
		$mk = "propsrch_".$variable;
		if ($variable == 'tenure') {
			$tenureopt = get_option('propsrch_tenureoption');
			$new = get_post_meta($post_id, $mk, true);
			$new = $this->swap_tenure($new, $tenureopt);
		} else if ($variable == 'linktoproperty') {
			$new = get_permalink($post_id);
		} else if ($variable == 'price') {
			$NV = new NumericVals();
			$new = $NV->get_value($post_id, $variable);
			$new = $new->value;
		} else if ($variable == 'location') {
			if (!empty($saved_heirachy['location'])) {
				// The meta value - will be a post ID - we join that too
				$new = get_post_meta($post_id, 'propsrch_location_post', true);
				$new = get_posts(array(
					'post_name'=>$new,
					'post_type'=>'locations',
					'post_status'=>'publish'
				));
				$new = (!empty($new)) ? $new->post_title : '';
			} else {
				// Use the meta value as is
				$new = get_post_meta($post_id, $mk, true);
			}			
		
		} else if ($variable == 'image') {
			$imagesize = get_option('propsrch_featured_image');
			$tnid = get_post_thumbnail_id($post_id);
			$new = wp_get_attachment_image_src(
					$tnid, $imagesize
					)[0];
		} else if ($variable == 'total_area') {
			$NV = new NumericVals();
			$new = $NV->get_value($post_id, 'total_area');
			$new = $new->value;
			$new = number_format($new,1);
		} else if ($variable == 'address') {
			$new = get_post_meta($post_id, $mk, true);
			$new = nl2br($new);
		}
//  		ppr(array($variable, $mk, $v, $post_id, $new));
		return($new);
	}
	function swap_tenure ($key, $mode) {
		if ($mode == 'friendly') {
			foreach ($this->tenureopts_friendly as $k => $v) {
				if (in_array($key, $v['inc'])) {
					return($v['label']);
				}
			}
		} else {
			if (isset($this->tenureopts[$key])) {
				return($this->tenureopts[$key]['label']);
			}
		}
	}
	
}