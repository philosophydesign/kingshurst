<?php
if (empty($_SESSION)) {
	session_start();
}
class RegisterInterest {
	var $list_utils = Array();
	var $fb;
	var $phpmailer;
	var $mandatory_marker = '<span class="mandatory">*</span>';
	function __construct() {
		/*
		 * 0 | Label
		 * 1 | Description
		 * 2 | Admin Only
		 * 3 | Skip Navigation
		 */
		$this->list_utils = array(
// 				'viewsubmissions'=>array(
// 						'View submissions',
// 						'View all the people who have registered their interest'
// 				),
				'viewforms'=>array(
						'View/Edit forms',
						'View/Edit all the forms used'
				),
				'addform'=>array(
						'Add form',
						'Add a new form',
						true
				),
				'editform'=>array(
						'Edit form',
						'Edit an exsting form',
						true,
						true,
				),
				
					
		);
		if (class_exists('GesForms')) {
			$this->fb = new GesForms();
		} else {
			//die('No form builder available');
		}
		if (class_exists('PHPMailer')) {
			$this->phpmailer = new PHPMailer();
		} else {
			die('No mailer available');
		}
	}
	/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
	##### The General Functions
	/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
	function dependency_check () {
		RIDB( 'Running Dependency CHECK');
		if (!is_plugin_active('Form Builder (DEVG.ES)')) {
			RIDB('No FORM Builder');
			if (!activate_plugins('Form Builder (DEVG.ES)')) {
				if (is_plugin_active("Philosophy's Register Interest Plugin")) {
					deactivate_plugins("Philosophy's Register Interest Plugin");
				}
				add_action( 'admin_notices', 'REGINT_notice_noformbuilder' );
				return false;
			} else {
				RIDB('Activated');
				return true;
			}
		} else {
			return true;
		}
	}
	function database_check () {
		global $wpdb;
		RIDB( 'Running Databse CHECK');
		$table_name = 'regint_fields';
		$check = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if($check != $table_name) {
			include(REGINT_LOC."registerinterest.sql.php");
			foreach ($q_registerinterest as $s) {
				RIDB( '<pre>'.$s.'</pre>');
				$wpdb->query($s);
			}
		} else {
			RIDB( 'Table should exist');
		}
		#exit;
	}
	
	function list_submissions () {
		global $wpdb;
		$q = 'select * from regint_submissions';
		$r = $wpdb->get_results($q);
		return($r);
	}
	function list_fields() {
		global $wpdb;
		$q = 'select * from regint_fields';
		$r = $wpdb->get_results($q);
		return($r);
	}
	function list_forms() {
		global $wpdb;
		$q = 'select * from regint_forms';
		$r = $wpdb->get_results($q);
		return($r);
	}
	function get_complete_form($form_id) {
		global $wpdb;
		if (is_numeric($form_id)) {
			$q = 'select * from regint_forms WHERE id = '.$form_id;
		} else {
			$q = 'select * from regint_forms WHERE reference = "'.$form_id.'"';
		}
		$r = $wpdb->get_row($q);
		if (!empty($r)) {
			$r->fields = $this->get_form_fields($r->id);
		} else {
			$r = new stdClass();
			$r->fields = [];
		}
		
		return($r);
	}
	function get_form_fields($form_id) {
		if (!empty($form_id)) {
			global $wpdb;
			$q = 'select * from regint_fields WHERE form_id = '.$form_id.' ORDER by rowindex ASC, id ASC';
			$fields = $wpdb->get_results($q);
// 			ppr($fields);
			return($fields);
		}
	}
	function get_email_settings ($form_id) {
		global $wpdb;
		if (!empty($form_id)) {
			$q = 'select * from  regint_mailer_settings WHERE form_id = '.$form_id;
			$r = $wpdb->get_row($q);
		} else {
			$r = new stdClass();
		}
		return($r);
	}
	function get_form_submissions ($form_id, $ascdesc = 'DESC') {
		global $wpdb;
		
		$q = 'select id, form_id from regint_fields where ref = "email" and form_id = '.$form_id;
		$r = $wpdb->get_row($q);
		if (!empty($r)) {
			$email_field_id = $r->id;
		} else {
			$email_field_id = '-1';
		}
		
 		$wpdb->show_errors(1);
		$q = 'select s.*, p.post_title, p.post_type, a.`HTTP_USER_AGENT` as ua, d.`value` as email , e.has_read 
				from regint_submissions s
				left join '.$wpdb->posts.' p ON post_id = p.ID 
				left join regint_useragents a ON a.id = useragent_id 
				left join regint_emails e ON (e.submission_id = s.id AND `type` = "client") 
				left join regint_submissions_data d ON (s.id = d.submission_id AND d.field_id = '.$email_field_id .')
				where form_id = '.$form_id.' ORDER by `date` '.$ascdesc;
		$r = $wpdb->get_results($q);
		return($r);
	}
	function get_form_submission ($form_id, $submission_id) {
		global $wpdb;
// 		$wpdb->show_errors(1);
		$q = 'select s.*, p.post_title, p.post_type, a.`HTTP_USER_AGENT` as ua 
				from regint_submissions s
				left join '.$wpdb->posts.' p ON post_id = p.ID 
				left join regint_useragents a ON a.id = useragent_id 
				where form_id = '.$form_id.' and s.id = '.$submission_id.' LIMIT 1';
// 		ppr($q);
		$r = $wpdb->get_row($q);
		return($r);
	}
	function get_submission_data ($form_id, $submission_id) {
		global $wpdb;
		$q = 'SELECT d.*, f.field_group, f.label, f.ref, f.mandatory, f.type, f.options
				FROM regint_submissions_data d
				LEFT JOIN regint_submissions s on s.id = d.submission_id
				LEFT JOIN regint_fields f on f.id = d.field_id
				WHERE submission_id = '.esc_sql($submission_id).' and s.form_id = '.esc_sql($form_id);
		$r = $wpdb->get_results($q);
		return($r);
		
	}
	function get_submission_data_row ($form_id, $submission_id, $field_ref) {
		global $wpdb;
		$q = 'SELECT d.*, f.field_group, f.label, f.ref, f.mandatory, f.type, f.options
				FROM regint_submissions_data d
				LEFT JOIN regint_submissions s on s.id = d.submission_id
				LEFT JOIN regint_fields f on f.id = d.field_id
				WHERE 
					f.ref = "'.esc_sql($field_ref).'" and
					submission_id = '.esc_sql($submission_id).' and 
					s.form_id = '.esc_sql($form_id);
		$r = $wpdb->get_row($q);
		return($r);
		
	}
	function list_data() {
		global $wpdb;
		$q = 'select * from regint_submissions_data';
		$r = $wpdb->get_results($q);
		return($r);
	}
	function insert_form ($ref, $title) {
		global $wpdb;
		$q = 'insert into regint_forms (
				reference,
				title) VALUES (
				"'.$ref.'",
				"'.$title.'"
				)
				';
		$wpdb->query($q);
		return($wpdb->insert_id);
	}
	function insert_field($form_id, $s, $field_group='', $rowindex = 0) {
		$s = (array) $s;
		
		if (empty($s['type'])) {
			$s['type'] = 'textfield';
		}
		/*
		if (!empty($s['options'])) {
			$s['options'] = json_encode($s['options']); 
		}
		*/
		
		global $wpdb;
		$wpdb->show_errors(0);
		$q = "insert into regint_fields	(
				`form_id`,
				`field_group`,
				`label`,
				`ref`,
				`mandatory`,
				`val`,
				`type`,
				`options`,
				`readonly`,
				`rowindex`
				) VALUES (
				$form_id,
				'$field_group',
				'$s[label]',
				'$s[ref]',
				'$s[mandatory]',
				'$s[val]',
				'$s[type]',
				'$s[options]',
				$s[readonly],
				$rowindex
				)";
		//RIDB($q);
		$wpdb->query($q);
		return($wpdb->insert_id);
	}
	function insert_mailer_setting ($settings) {
		global $wpdb;
		$q = "INSERT INTO `regint_mailer_settings` 
				(
					`form_id`, 
					`email_to`, 
					`email_cc`,
					`email_bcc`, 
					`email_from_email`, 
					`email_from_name`, 
					`email_replyto`, 
					`email_subject`, 
					`email_body_html`, 
					`email_body_alt`, 
					`phpm_Host`, 
					`phpm_Username`, 
					`phpm_Password`, 
					`phpm_SMTPAuth`, 
					`phpm_SMTPSecure`, 
					`phpm_mailer`, 
					`phpm_Port`, 
					`phpm_CharSet`
				)
				VALUES
				(
					'$settings[form_id]', 
					'$settings[email_to]', 
					'$settings[email_cc]',
					'$settings[email_bcc]', 
					'$settings[email_from_email]', 
					'$settings[email_from_name]', 
					'$settings[email_replyto]', 
					'$settings[email_subject]', 
					'$settings[email_body_html]', 
					'$settings[email_body_alt]', 
					'$settings[phpm_Host]', 
					'$settings[phpm_Username]', 
					'$settings[phpm_Password]', 
					'$settings[phpm_SMTPAuth]', 
					'$settings[phpm_SMTPSecure]', 
					'$settings[phpm_mailer]', 
					'$settings[phpm_Port]', 
					'$settings[phpm_CharSet]'
				)";
		$wpdb->query($q);
		return($wpdb->insert_id);
	}
	function update_mailer_setting ($settings) {
		global $wpdb;
		$q = "UPDATE `regint_mailer_settings` 
				SET 
					`email_to` = '$settings[email_to]', 
					`email_cc` = '$settings[email_cc]',
					`email_bcc` = '$settings[email_bcc]', 
					`email_from_email` = '$settings[email_from_email]', 
					`email_from_name` = '$settings[email_from_name]', 
					`email_replyto` = '$settings[email_replyto]', 
					`email_subject` = '$settings[email_subject]', 
					`email_body_html` = '$settings[email_body_html]', 
					`email_body_alt` = '$settings[email_body_alt]', 
					`phpm_Host` = '$settings[phpm_Host]', 
					`phpm_Username` = '$settings[phpm_Username]', 
					`phpm_Password` = '$settings[phpm_Password]', 
					`phpm_SMTPAuth` = '$settings[phpm_SMTPAuth]', 
					`phpm_SMTPSecure` = '$settings[phpm_SMTPSecure]', 
					`phpm_mailer` = '$settings[phpm_mailer]', 
					`phpm_Port` = '$settings[phpm_Port]', 
					`phpm_CharSet` = '$settings[phpm_CharSet]'
			WHERE form_id = $settings[form_id]";
		$wpdb->query($q);
		return;
	}
	function update_field($field_id, $s, $field_group='', $rowindex = 0) {
		$s = (array) $s;
		
		if (empty($s['type'])) {
			$s['type'] = 'textfield';
		}
		global $wpdb;
		$wpdb->show_errors(0);
		$q = "UPDATE regint_fields 
			SET 
				`field_group` = '$field_group',
				`label` = '$s[label]',
				`ref` = '$s[ref]',
				`mandatory` = $s[mandatory],
				`val` = '$s[val]',
				`type` = '$s[type]',
				`options` = '$s[options]',
				`readonly` = $s[readonly],
				`rowindex` = $rowindex
			WHERE id = $field_id";
		#echo '<pre>'.$q.'</pre><br>';
		//RIDB($q);
		if ($wpdb->query($q)) {
			return(true);
		} else {
			return(false);
		}
	}
	function delete_field($field_id) {
		
		global $wpdb;
		$wpdb->show_errors(0);
		$q = "DELETE FROM regint_fields	WHERE id = $field_id";
		if ($wpdb->query($q)) {
			return(true);
		} else {
			return(false);
		}
	}
	function insert_submission ($form_id, $email_address='', $post_id=0) {
		global $wpdb;
		$wpdb->show_errors(0);
		if (empty($form_id)) { $form_id = 0; }
		$q = 'insert into regint_submissions 
					(form_id, date, post_id, email, ip_address, useragent_id) 
				VALUES
					('.$form_id.', "'.date('Y-m-d H:i').'", '.$post_id.', "'.$email_address.'", "'.$this->get_ip().'", '.$this->get_useragent_id().') 
				';
		if ($wpdb->query($q)) {
			return($wpdb->insert_id);
		} else {
			return(false);
		}
	}
	function insert_submission_data_single ($submission_id, $field_id, $value) {
		global $wpdb;
		$wpdb->show_errors(0);
		$q = 'insert into regint_submissions_data 
					(submission_id, field_id, value) 
				VALUES
					('.$submission_id.', '.$field_id.', "'.esc_sql($value).'") 
				';
// 		echo $q.'<br>';
		if ($wpdb->query($q)) {
			return($wpdb->insert_id);
		} else {
			return(false);
		}
	}
	
	
	function handle_submission($form_ref) {
		if ($_POST['philosri_nonce'] == $_SESSION['philosri_nonce'][$form_ref]) {
			//$this->nonce();
			$form = $this->get_complete_form($_POST['philosri_formref']);
			foreach ($form->fields as $f) {
				if (($f->type == 'email') && (!empty($_POST[$f->ref]))) {
					$email_address = $_POST[$f->ref];					
				}
			}
			
			if (!empty($_POST['philosri_post'])) {
				$args = explode('/',$_POST['philosri_post']);
				
				$args = array(
					'name'=>$args[1],
					'post_type'=>$args[0],
					'post_status'=>'publish'
				);
				$post = get_posts($args);
				if (!empty($post)) {
					$submission_id = $this->insert_submission($form->id, $email_address, $post[0]->ID);
				} else {
					$submission_id = $this->insert_submission($form->id, $email_address);
				}
			} else {
				$submission_id = $this->insert_submission($form->id, $email_address);
			}
// 			ppr($form);
			foreach ($form->fields as $f) {
// 				echo $f->ref.'<Br>';
				if (isset($_POST[$f->ref])) {
					if (in_array($f->type, $this->fb->fieldsAreArray)) {
						$value = json_encode($_POST[$f->ref]);
					} else {
						$value = $_POST[$f->ref];
					}
					$this->insert_submission_data_single($submission_id, $f->id, $value);
				}
			}
			$this->send_client_email($form->id, $submission_id);
			if (isset($_POST['email'])) {
				$this->send_user_email($form->id, $_POST['email'], $submission_id);
			}
			
		}
	}
	function get_useragent_id() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			global $wpdb;
			$wpdb->show_errors(1);
			$q = 'select * from regint_useragents WHERE HTTP_USER_AGENT = "'.$_SERVER['HTTP_USER_AGENT'].'"';
			$r = $wpdb->get_row($q);
			if (empty($r)) {
				$q = 'insert into regint_useragents (HTTP_USER_AGENT) VALUES ("'.$_SERVER['HTTP_USER_AGENT'].'")';
				if ($wpdb->query($q)) {
					return($wpdb->insert_id);
				} else {
					return(false);
				}
			}
			return($r->id);
		}
	}
	function get_ip() {
		//Just get the headers if we can or else use the SERVER global
		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
		} else {
			$headers = $_SERVER;
		}
		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$the_ip = $headers['X-Forwarded-For'];
		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
				) {
					$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
		} else {
				
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		return $the_ip;
	}
	/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
	##### The Hook Functions
	/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */

	function hook_checks () {
		RIDB( 'hook_CHECKS');
		if ($this->dependency_check()) {
			$this->database_check();
		}
	}
	
	function hook_activate () {
		if ($this->dependency_check()) {
			$this->database_check();
		}
	}
	function hook_menu () {
		add_menu_page(
				'Register Interest',
				'Register Interest',
				'edit_posts',
				'register-interest',
				'REGINT_admin_page',
				'dashicons-id',
				5
		);
		$x = 1;
		foreach ($this->list_utils  as $p => $m) {
			if ($x > 1) {
				if (RIDB_shownav($m))  {
					add_submenu_page(
							'register-interest',
							$p,
							$m[0],
							'edit_posts',
							'?page=register-interest&ri-action='.$p
					);
				}
			}
			$x++;
		}
		
	}
	
	/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
	##### The Admin Page Functions
	/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
	function adminpage_common ($data) {
		RIDB('function adminpage_common');
		$data['list_utils'] = $this->list_utils;
		return($data);
	}
	function adminpage_viewsubmissions ($data) {
		if (!isset($_GET['form'])) {
			$data['all_forms'] = $this->list_forms();
		} else {
			$data['all_submissions'] = $this->get_form_submissions($_GET['form']);
		}
		return($data);
	}
	function adminpage_viewforms ($data) {
		$data['all_forms'] = $this->list_forms();
		return($data);
	}
	function adminpage_addform ($data) {
		RIDB('function adminpage_addform');
		
		return($data);
	}
	function make_form_html ($form, $args=array()) {
		if (is_string($form)) {
			$form = $this->get_complete_form($form);
		} 
		
		$GF = new GesForms();
		$html = $GF->make_form_html($form, $args);
		
		return($html);
	}
	function adminpage_editform ($data) {
		RIDB('function adminpage_editform');
		$form = $this->get_complete_form($_GET['form']);
		$data['preview'] = $this->make_form_html($form);
		
		
		/* . ~ . ~ . ~ . ~ | EDIT FORM NOW DONE IN REACT-JS | ~ . ~ . ~ . ~ . */
		
		/* Make the edit form - form ----------------------------------------------------------------------------*/
		/*
		$this->fb->fieldClasses = array(
			'field_group'=>'mult-field_group',
			'label'=>'mult-label',
			'ref'=>'mult-ref',
			'mandatory'=>'mult-mandatory',
			'type'=>'mult-type',
			'options'=>'mult-options',
			'readonly'=>'mult-readonly',
			'rowindex'=>'mult-rowindex lastingroup',
		);
		
		$structure = [
				['field_id',	'field_id',		0,	'hidden'],
				['Field Group',	'field_group',	0],
				['Label',		'label',		1],
				['Reference',	'ref',	1],
				['Mandatory',	'mandatory',	0,	'checkbox'],
				['Type',		'type',			0,	'select', $this->fb->availableFields],
				['Options',		'options',		0,	'body'],
				['Read Only',	'readonly',		0,	'checkbox'],
				['Row Index',	'rowindex',	0]
		
		];
		foreach ($form->fields as $k=>$f) {
			$this->fb->unique = $k;
			$this->fb->manyMode = true;
			
			
			
			$this->fb->counter++;
			$this->fb->values = array(
				'field_id'=>$f->id,
				'field_group'=>$f->field_group,
				'label'=>$f->label,
				'ref'=>$f->ref,
				'mandatory'=>$f->mandatory,
				'type'=>$f->type,
				'options'=>$this->fb->opt2str($f->options),
				'readonly'=>$f->readonly,
				'rowindex'=>$f->rowindex,
			);
			$data['editform'][$f->field_group] .= $this->fb->outputFields($this->fb->makeStructObj($structure));
		}
		$data['editform']  = (!empty($data['editform'])) ? implode('<br>',$data['editform']) : '';
		*/
		
		/* Make the edit settings - form ----------------------------------------------------------------------------*/
		$this->fb->unique = '';
		$this->fb->manyMode = false;
		$this->fb->fieldClasses = array(
				
		);
		$structure = [
				'Message Details'=>[
					['To',					'email_to',			1,	'body'],
					['CC',					'email_cc',			0,	'body'],
					['BCC',					'email_bcc',		0,	'body'],
					['From (email)',		'email_from_email',	1,	'textfield'],
					['From (name)',			'email_from_name',	1,	'textfield'],
					['Reply To',			'email_replyto',	0,	'textfield'],
					['Subject',				'email_subject',	0,	'textfield'],
					['HTML Body',			'email_body_html',	0,	'body'],
					['Body (Alternative)',	'email_body_alt',	0,	'body']
				],
				'SMTP Details'=>[
					['Host',				'phpm_Host',		0],
					['Username',			'phpm_Username',		0],
					['Password',			'phpm_Password',		0],
					['SMTPAuth',			'phpm_SMTPAuth',		0,	'checkbox'],
					['SMTPSecure',			'phpm_SMTPSecure',		0],
					['Mailer (eg. SMTP',	'phpm_mailer',			0],
					['Port',				'phpm_Port',			0],
					['CharSet',				'phpm_CharSet',			0],
				]
		];
		$values = (array) $this->get_email_settings($_GET['form']);
		if (empty($values)) {
			$values = array(
				'phpm_SMTPAuth'		=>	'true',
				'phpm_SMTPSecure'	=>	'true',
				'phpm_mailer'		=>	'UTF-8',
				'phpm_Port'			=>	'25',
				'phpm_CharSet'		=>	'UTF-8',
			);
		}
		$this->fb->values = $values; 
		foreach ($structure as $k => $s) {
			$s = $this->fb->makeStructObj($s);
			$data['settingsform'][$k] = '<h4>'.$k.'</h4>'.$this->fb->outputFields($s);
		}
		$data['settingsform'] = implode('<br>',$data['settingsform']);
		return($data);
	}
	function runearly_admin_addform () {
		//RIDB('function REGINT_runearly_addform');
		$form_id = $this->insert_form($_POST['ref'], $_POST['title']);
		
		if (!empty($_POST['config'])) {
			
			// $this->fb->values = $values;
			$this->fb->fieldClasses = array();
			$structure = '$arr = '.stripslashes($_POST['config']).'; return ($arr);';
			$structure = eval($structure);
			foreach ($structure as $formgroup=>$s) {
				$s = $this->fb->makeStructObj($s);
				foreach ($s as $index => $f) {
					$this->insert_field($form_id, $f, $formgroup, $index);
				}
			}
		}
		
		return $form_id;
	}
	function runearly_editsettings () {
		if (!empty($_GET['form'])) {
			$existing = $this->get_email_settings($_GET['form']);
			if (empty($existing)) {
				$data = $_POST;
				$data['form_id'] = $_GET['form'];
				$this->insert_mailer_setting($data);			
			} else {
				$data = $_POST;
				$data['form_id'] = $_GET['form'];
				$this->update_mailer_setting($data);			
			}
		}
	}
	function runearly_admin_editform () {
		//RIDB('function REGINT_runearly_addform');
		if (isset($_GET['save'])) {
			$structure = array();
			
			$fields = $this->get_form_fields($_GET['form']);
			
			if (isset($_POST['deleted'])) {
				foreach ($_POST['deleted'] as $d) {
					$this->delete_field($d);
				}
			}
			
			
			$GF = new GesForms();
			
			foreach ($_POST['label'] as $x => $l) {
				$mandatory = ($_POST['mandatory'][$x]) ? 1 : 0;
				$readonly = ($_POST['readonly'][$x]) ? 1 : 0;
				$rowindex = ($_POST['sortorder'][$x]) ? $_POST['sortorder'][$x] : 0;
				$options = $GF->opt2json($_POST['options'][$x]);
				//$structure[$_POST['field_group'][$x]][]
 				$s = array(
					'label'		=>	$_POST['label'][$x], 						
					'ref'		=>	$_POST['ref'][$x], 						
					'mandatory'	=>	$mandatory, 						
					'type'		=>	$_POST['type'][$x], 						
					'options'	=>	$options, 						
					'readonly'	=>	$readonly, 						
					'rowindex'	=>	$rowindex, 						
 				);
 				#ppr($s);
 				
 				if (empty($_POST['field_id'][$x])) {
 					$this->insert_field($_GET['form'], $s, $_POST['group'][$x], $rowindex).'<Br>'; 					
 				} else {
 					$this->update_field($_POST['field_id'][$x], $s, $_POST['group'][$x], $rowindex).'<Br>';
 				}
 				#exit;
			}
		}
	}
	function makeImgTrckr () {
		return(md5(microtime().rand(10000,99999)));
	}
	function send_client_email ($form_id, $submission_id) {
		
		$this->phpmailer = new PHPMailer();
		
		$emailsettings = $this->get_email_settings($form_id);
// 		ppr($emailsettings);
		#$mail->SMTPDebug = false;
		#$mail->do_debug = 0;
		$this->phpmailer->do_debug = 1;
		if (empty($emailsettings->email_subject)) {
			$subject = get_bloginfo('name').' - someone has registered their interest';
		} else {
			$subject = $emailsettings->email_subject;
		}
		
		$body_html = stripslashes($emailsettings->email_body_html)."<br><br><table>";
		$body_text = stripslashes($emailsettings->email_body_html)."\n\n";
		$imgtrck = $this->makeImgTrckr();
		$recipients = [];
		if (!empty($_GET['philosrisendatest'])) {
				
			$data = $this->get_complete_form($form_id);
				
			foreach ($data->fields as $d) {
				$body_html .= '<tr><td style="font-weight: bold; background-color: #F0F0F0;">'.$d->label."</td><td>TEST</td></tr>";
				$body_text .= $d->label.": ".$d->value."\n";
			}
			$body_html .= "</table>";
		} else {
			$userdata = $this->get_submission_data($form_id, $submission_id);
			foreach ($userdata as $d) {
				$body_html .= '<tr><td style="font-weight: bold; background-color: #F0F0F0;">'.$d->label."</td><td>".$d->value."</td></tr>";
				$body_text .= $d->label.": ".$d->value."\n";
			}
			$body_html .= "</table>";
		}
		$domain = $_SERVER['HTTP_HOST'];
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			$domain = 'https://'.$domain;
		} else {
			$domain = 'http://'.$domain;
		}
		$body_html .= '<img src="'.$domain.'/philosritrk/'.$imgtrck.'.gif"/>';
		
		
		
		if (!empty($emailsettings->phpm_Host)) {
			$this->phpmailer->isSMTP();
			$this->phpmailer->SMTPDebug = true;
			
			$this->phpmailer->Host = $emailsettings->phpm_Host;
			$this->phpmailer->Username = $emailsettings->phpm_Username;
			$this->phpmailer->Password = $emailsettings->phpm_Password;
			$this->phpmailer->SMTPAuth = ($emailsettings->phpm_SMTPAuth) ? true : false;
			$this->phpmailer->SMTPSecure = $emailsettings->phpm_Password;
			$this->phpmailer->mailer = 'smtp';
			$this->phpmailer->Port = $emailsettings->phpm_Port;;
			
			
		} 
		
		$this->phpmailer->CharSet = 'UTF-8';
		$this->phpmailer->From = $emailsettings->email_from_email;
		$this->phpmailer->FromName = $emailsettings->email_from_name;
		
		if (!empty($emailsettings->email_repltto)) {
			$this->phpmailer->AddReplyTo($emailsettings->email_replyto);
		}
		$sento = explode(';', $emailsettings->email_to);
		foreach ($sento as $s) {
			$this->phpmailer->addAddress(trim($s));
			$recipients[] = $s;
		}
		
		$this->phpmailer->isHTML(true);
		
		$this->phpmailer->Subject = $subject;
		$this->phpmailer->Body = $body_html;
		$this->phpmailer->AltBody = $body_text;
		
		if (!empty($emailsettings->email_cc)) {
			$this->phpmailer->addCC($emailsettings->email_cc);
			$recipients[] = $emailsettings->email_cc;
		}
		if (!empty($emailsettings->email_bcc)) {
			$this->phpmailer->addCC($emailsettings->email_bcc);
			$recipients[] = $emailsettings->email_bcc;
		}
// 		ini_set('display_errors', 'On');
// 		error_reporting(E_ALL);
		$success = $this->phpmailer->send();
// 		ppr($this->phpmailer);
// 		exit;
		$this->insert_email(
			$submission_id, 
			json_encode($recipients),  
			$subject,
			$body_html,
			$success,
			$imgtrck,
			'client'
		);
				
		
		#echo 'To: '.$emailsettings->email_to.' <br>Subject: '.$emailsettings->email_subject.'<hr>'.$emailsettings->email_body_html.'<hr>'; 
		#var_dump($this->phpmailer);
		#exit;
		return ($success);
	}
	function send_user_email ($form_id, $email_address, $submission_id) {
		
		$this->phpmailer = new PHPMailer();
		
		$emailsettings = $this->get_email_settings($form_id);
// 		ppr($emailsettings);
		#$mail->SMTPDebug = false;
		#$mail->do_debug = 0;
		$this->phpmailer->do_debug = 1;
		
		$subject = 'Thank you for submitting your interest in '.get_bloginfo('name');
		$messsage = 'Thank you for submitting your interest in '.get_bloginfo('name').'.';
		
		$body_html = $messsage;
		$body_text = $messsage;
		
		$imgtrck = $this->makeImgTrckr();
		$recipients = [];
		
		$domain = $_SERVER['HTTP_HOST'];
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			$domain = 'https://'.$domain;
		} else {
			$domain = 'http://'.$domain;
		}
		$body_html .= '<img src="'.$domain.'/philosritrk/'.$imgtrck.'.gif"/>';
		
		
		
		if (!empty($emailsettings->phpm_Host)) {
			$this->phpmailer->isSMTP();
			$this->phpmailer->SMTPDebug = true;
			
			$this->phpmailer->Host = $emailsettings->phpm_Host;
			$this->phpmailer->Username = $emailsettings->phpm_Username;
			$this->phpmailer->Password = $emailsettings->phpm_Password;
			$this->phpmailer->SMTPAuth = ($emailsettings->phpm_SMTPAuth) ? true : false;
			$this->phpmailer->SMTPSecure = $emailsettings->phpm_Password;
			$this->phpmailer->mailer = 'smtp';
			$this->phpmailer->Port = $emailsettings->phpm_Port;;
			
			
		} 
		
		$this->phpmailer->CharSet = 'UTF-8';
		$this->phpmailer->From = $emailsettings->email_from_email;
		$this->phpmailer->FromName = $emailsettings->email_from_name;
		
		
		$this->phpmailer->addAddress($email_address);
		$recipients[] = $email_address;
		
		$this->phpmailer->isHTML(true);
		
		$this->phpmailer->Subject = $subject;
		$this->phpmailer->Body = $body_html;
		$this->phpmailer->AltBody = $body_text;
		
	
		$success = $this->phpmailer->send();
		$this->insert_email(
			$submission_id, 
			json_encode($recipients),  
			$subject,
			$body_html,
			$success,
			$imgtrck,
			'user'
		);

		
		#echo 'To: '.$emailsettings->email_to.' <br>Subject: '.$emailsettings->email_subject.'<hr>'.$emailsettings->email_body_html.'<hr>'; 
		#var_dump($this->phpmailer);
		#exit;
		return ($success);
	}
	function insert_email ($submission_id, $recipients, $subject, $body, $success, $img_trck, $type='na') {
		global $wpdb;
		$q = 'insert into regint_emails
					(`submission_id`, `datetime`, `recipients`, `subject`, `body`, `success`, `img_trck`, `type`)
				VALUES
					('.$submission_id.', "'.date('Y-m-d H:i').'", "'.esc_sql($recipients).'", "'.esc_sql($subject).'", "'.esc_sql($body).'", '.$success.', "'.$img_trck.'", "'.$type.'")
				';
// 		echo $q;
		$wpdb->show_errors(1);
		if ($wpdb->query($q)) {
			return($wpdb->insert_id);
		} else {
			return(false);
		}
	}
	function get_email_id_from_tracking($track) {
		global $wpdb;
		$q = 'select email_id from regint_emails where img_trck = "'.$track.'"';
		$e = $wpdb->get_row($q);
		$email_id = (isset($e->email_id)) ? $e->email_id : 0;
		return($email_id);
	}
	function get_email_id_from_submission($submission_id) {
		global $wpdb;
		$q = 'select email_id from regint_emails where submission_id = "'.$submission_id.'"';
		$e = $wpdb->get_row($q);
		$email_id = (isset($e->email_id)) ? $e->email_id : 0;
		return($email_id);
	}
	function use_philosritrk ($track) {
		global $wpdb;
		$wpdb->show_errors(true);
		if (empty($status)) {
			$status = 0;
		}
		$q = 'update `regint_emails` set has_read = 1 where img_trck = "'.$track.'"';
// 		echo $q.'<br>';
		$wpdb->query($q);
		#echo 'HAS READ<br>';
		$email_id = $this->get_email_id_from_tracking($track);
		#echo $email_id.'<br>';
		$q = 'insert into regint_emails_readlog
				(datetime, ipaddress, email_id)
				VALUES
				("'.date('Y-m-d H:i').'", "'.$this->get_ip().'", "'.$email_id.'");';
// 		echo $q;
		$wpdb->query($q);
// 		exit;
	}
	function get_emails_readlog ($email_id) {
		global $wpdb;
		$q = 'select * from regint_emails_readlog where email_id = '.$email_id;
		$r = $wpdb->get_results($q);
		return($r);
	}
		
}