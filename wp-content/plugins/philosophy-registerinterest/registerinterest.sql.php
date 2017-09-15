<?php
$q_registerinterest = array();
$q_registerinterest[] = "
CREATE TABLE `regint_fields` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `field_group` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `mandatory` tinyint(1) NOT NULL,
  `val` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `options` tinytext NOT NULL,
  `readonly` tinyint(1) NOT NULL,
  `rowindex` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$q_registerinterest[] = "
CREATE TABLE `regint_forms` (
  `id` int(11) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$q_registerinterest[] = "
CREATE TABLE IF NOT EXISTS `regint_submissions` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `post_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(39) NOT NULL,
  `useragent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$q_registerinterest[] = "
CREATE TABLE `regint_submissions_data` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$q_registerinterest[] = "
ALTER TABLE `regint_fields`
  ADD PRIMARY KEY (`id`);";
$q_registerinterest[] = "ALTER TABLE `regint_forms`
  ADD PRIMARY KEY (`id`);";
$q_registerinterest[] = "ALTER TABLE `regint_submissions`
  ADD PRIMARY KEY (`id`);";
$q_registerinterest[] = "ALTER TABLE `regint_submissions_data`
  ADD PRIMARY KEY (`id`);";
$q_registerinterest[] = "
ALTER TABLE `regint_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$q_registerinterest[] = "ALTER TABLE `regint_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$q_registerinterest[] = "ALTER TABLE `regint_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$q_registerinterest[] = "ALTER TABLE `regint_submissions_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$q_registerinterest[] = "
CREATE TABLE IF NOT EXISTS `regint_useragents` (
  `id` int(11) NOT NULL,
  `HTTP_USER_AGENT` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$q_registerinterest[] = "ALTER TABLE `regint_useragents`
  ADD PRIMARY KEY (`id`);";
$q_registerinterest[] = "
ALTER TABLE `regint_useragents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
$q_registerinterest[] = "CREATE TABLE IF NOT EXISTS `regint_emails` (
  `email_id` int(12) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `recipients` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `success` tinyint(4) NOT NULL,
  `img_trck` varchar(255) NOT NULL,
  `has_read` int(1) NOT NULL,
  `type` varchar(10) NOT NULL
)";
$q_registerinterest[] = "ALTER TABLE `regint_emails`
  ADD PRIMARY KEY (`email_id`);";
$q_registerinterest[] = "CREATE TABLE IF NOT EXISTS `regint_emails_readlog` (
  `readlog_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `ipaddress` varchar(39) NOT NULL,
  `email_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$q_registerinterest[] = "ALTER TABLE `regint_emails_readlog`
  ADD PRIMARY KEY (`readlog_id`);";
$q_registerinterest[] = "ALTER TABLE `regint_emails_readlog`
  MODIFY `readlog_id` int(11) NOT NULL AUTO_INCREMENT;";
$q_registerinterest[] = "
CREATE TABLE IF NOT EXISTS `regint_mailer_settings` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `email_to` text NOT NULL,
  `email_cc` text NOT NULL,
  `email_bcc` text NOT NULL,
  `email_from_email` varchar(255) NOT NULL,
  `email_from_name` varchar(255) NOT NULL,
  `email_replyto` varchar(255) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_body_html` text NOT NULL,
  `email_body_alt` text NOT NULL,
  `usesmtp` tinyint(4) NOT NULL,
  `phpm_Host` varchar(255) NOT NULL,
  `phpm_Username` varchar(255) NOT NULL,
  `phpm_Password` varchar(255) NOT NULL,
  `phpm_SMTPAuth` tinyint(1) NOT NULL,
  `phpm_SMTPSecure` varchar(10) NOT NULL,
  `phpm_mailer` varchar(10) NOT NULL,
  `phpm_Port` int(5) NOT NULL,
  `phpm_CharSet` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
$q_registerinterest[] = "ALTER TABLE `regint_mailer_settings`
  ADD PRIMARY KEY (`id`);";
$q_registerinterest[] = "ALTER TABLE `regint_mailer_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";