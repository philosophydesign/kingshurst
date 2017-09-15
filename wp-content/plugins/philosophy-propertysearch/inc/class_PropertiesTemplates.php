<?php
class PropertiesTemplates {
	function __construct() {
		
	}
	function getFields () {
		$PS = new PropertySearch();
		$saved_heirachy = $PS->propsrch_heiropt();
		$fields =  array();
		if (!empty($saved_heirachy['development'])) {
			$fields[] = ['Development',			'development',		0,	'select',	development_list(false)];
		}
		$fields[] = ['Total Area (Square Metres)',			'total_area',		0];
		$fields[] = ['Property Type',			'property_type',		0,	'select',	property_type_list(true)];
		$fields[] = ['Bedrooms',			'no_bedrooms',		0,	'select',	number_bedrooms_list()];
		$fields[] = ['LatLng Coordinates',			'latlng',		0];
		
		
 		return($fields);
		
	}
	
}