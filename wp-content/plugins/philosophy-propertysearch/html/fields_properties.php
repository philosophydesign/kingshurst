<?php
global $post;
$PS = new PropertySearch();
$PRO = new Properties();
$FC = new FieldCache();

$cached = $PRO->getCacheList();
$fields = $PRO->getFields();
$numeric = $PRO->getNumericVals();
$taxfields = $PRO->getTaxonomyFields();

$PS->ouput_admin_fields($fields, $cached, $numeric, $taxfields);

