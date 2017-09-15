<?php
global $wpdb;
$q = 'show tables like propsrch_postlinks';
$r = $wpdb->get_row($q);
if (empty($r)) {
	// Post Links
	$q= '
	CREATE TABLE IF NOT EXISTS `propsrch_postlinks` (
  `property_post_id` int(11) NOT NULL,
  `linked_post_id` int(11) NOT NULL,
  `post_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_postlinks`
			ADD KEY `property_post_id` (`property_post_id`),
			ADD KEY `linked_post_id` (`linked_post_id`),
			ADD KEY `post_type` (`post_type`);';
	$wpdb->query($q);
	
	
	// Field Cache
	$q= 'CREATE TABLE `propsrch_fieldcache` (
  `fieldcache_id` int(11) NOT NULL,
  `post_type` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_fieldcache`
  ADD PRIMARY KEY (`fieldcache_id`),
  ADD KEY `post_type` (`post_type`),
  ADD KEY `field_name` (`field_name`);';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_fieldcache`
  MODIFY `fieldcache_id` int(11) NOT NULL AUTO_INCREMENT;';
	$wpdb->query($q);
	
	// Numeric Values
	$q= 'CREATE TABLE `propsrch_numericvals` (
  `numericvals_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `value` float(11,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_numericvals`
  ADD PRIMARY KEY (`numericvals_id`),
  ADD KEY `field_name` (`field_name`),
  ADD KEY `post_id` (`post_id`);';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_numericvals`
  MODIFY `numericvals_id` int(11) NOT NULL AUTO_INCREMENT;';
	$wpdb->query($q);
	
	
	
	// Taxonomy 
	$q= 'CREATE TABLE `propsrch_taxonomy_terms` (
  `term_id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  `term_value` varchar(100) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_taxonomy_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD KEY `term_value` (`term_value`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `category` (`category`);';
	$wpdb->query($q);
	
	$q= 'ALTER TABLE `propsrch_taxonomy_terms`
  MODIFY `term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;';
	$wpdb->query($q);
	
	
	$q= 'CREATE TABLE `propsrch_taxonomy_links` (
  `post_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
	';
	$wpdb->query($q);
	$q= 'ALTER TABLE `propsrch_taxonomy_links`
  ADD KEY `post_id` (`post_id`),
  ADD KEY `term_id` (`term_id`);';
	$wpdb->query($q);
	/*
	$q= '
CREATE TABLE IF NOT EXISTS `propsrch_srchcache` (
  `id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `query` varchar(32) NOT NULL,
  `srcsql` text NOT NULL,
  `results` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$wpdb->query($q);
$q= 'ALTER TABLE `propsrch_srchcache`
  ADD PRIMARY KEY (`id`),
  ADD KEY `query` (`query`);';
	$wpdb->query($q);

$q= 'ALTER TABLE `propsrch_srchcache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
	$wpdb->query($q);
	*/
	

	$q= 'CREATE TABLE IF NOT EXISTS `propsrch_ppsrchcache` (
			`ppsc_id` int(11) NOT NULL,
			`post_id` int(11) NOT NULL,
			`data` text NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;';
	$wpdb->query($q);
	$q= 'ALTER TABLE `propsrch_ppsrchcache`
			ADD PRIMARY KEY (`ppsc_id`),
			ADD UNIQUE KEY `post_id` (`post_id`);';
	$wpdb->query($q);
	$q = 'ALTER TABLE `propsrch_ppsrchcache`
			MODIFY `ppsc_id` int(11) NOT NULL AUTO_INCREMENT;';
	$wpdb->query($q);
	
}