<?php

/**
 * Show a 404 error if the system is not running in the development environment.
 */
function requires_dev() {
	if (!is_dev_server()) {
		show_404();
	}
}

/**
 * Show a 404 error if the request is not an ajax one.
 */
function requires_ajax() {
	if (!is_ajax()) {
		show_404();
	}
}

/**
 * Show a 404 error if required permission not met
 */
function requires_admin() {
	if (!is_admin()) {
		show_404();
	}
}

function requires_super_admin() {
	if (!is_super_admin()) {
		show_404();
	}
}

function is_super_admin() {
	return get_user('is_super_admin') == 't';
}

function requires_hr() {
	if (!is_hr() && !is_super_admin()) {
		show_404();
	}
}

function is_hr() {
	return get_user('is_hr') == 't';
}

function requires_it() {
	if (!is_it()) {
		show_404();
	}
}

function is_it() {
	return get_user('is_it') == 't';
}

function is_user($created_by) {
	return get_user('usercode') == $created_by;
}

function requires_payroll() {
	if (!is_payroll()) {
		show_404();
	}
}

function is_payroll() {
	return get_user('is_payroll') == 't';
}

function requires_admin_team() {
	if (!is_admin_team()) {
		show_404();
	}
}

function is_admin_team() {
	return get_user('is_admin_team') == 't';
}

function requires_it_hr() {
	if (!is_it() && !is_hr() && !is_super_admin()){
		show_404();
	}
}

/**
 * Checks whether the user is logged in. Redirects to login if check fails.
 */
function requires_login() {
  $CI =& get_instance();
  
  if (!$CI->authentication->is_logged_in()) {
	$CI->authentication->login();
  }
}

function is_mobile() {
	$CI =& get_instance();
	$CI->load->library('user_agent');
	return $CI->agent->is_mobile();
}

function is_current() {
	return get_user('is_current') == 't';
}

/* return the first parameter that is not null or empty */

function uncamelize($string, $underscore = ' ') {
	return preg_replace('/([a-z])([A-Z])/', '$1' . $underscore . '$2', $string);
}

function set_nav_mode($mode = 'admin') {
	if ($mode == "admin") {
		get_instance()->session->set_userdata('nav_mode','admin');
	} else {
		get_instance()->session->set_userdata('nav_mode','inspector');
	}
}

/**
 * Converts a string to a format that is suitable for a url. Will need additions later on.
 * @param string $string A raw string.
 * @return string A url string.
 */
function safe_url($string) {
	return str_replace(array('/', '\\'), '-', $string);
}

function url_to_words($url) {
	return ucwords(str_replace('-', '/', str_replace(array('_', '/', '\\'), ' ', $url)));
}

/**
 * Adjusts the brightness of a colour by the given percentage.
 * @param string $hex_colour The color to adjust.
 * @param int $percent How much to brighten or darken the colour.
 * @return string The new hex colour.
 */
function alter_colour($hex_colour, $percent) {
	$parts = array();
	if (!preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex_colour, $parts)) {
		// Invalid colour.
		return $hex_colour;
	}

	$out = "#"; // Prepare to fill with the results
	for ($i = 1; $i <= 3; $i++) {
		$parts[$i] = hexdec($parts[$i]);
		$parts[$i] = round($parts[$i]) * $percent / 100;
		$out .= str_pad(dechex($parts[$i]), 2, '0', STR_PAD_LEFT);
	}
	return $out;
}

function coalesce() {
	$options = func_get_args();
	foreach ($options as $option) {
		if (!empty($option))
			return $option;
	}
	return "";
}

function is_dev_server() {
	switch(ENVIRONMENT) {
		case 'development':
		case 'local':
		case 'testing':
			return true;
		default:
			return false;
	}
}

function is_localhost() {
	return ENVIRONMENT == 'local';
}

function is_live_server() {
	return !is_dev_server();
}

function is_admin() {
  return (get_user('is_admin') == 't' ? true : false);
}

function is_logged_in() {
	return get_instance()->authentication->is_logged_in();
}

function is_ajax() {
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? true : false;
}

function is_post() {
	return get_instance()->input->post() !== false;
}

function output_json_error($input = "unknown error") {
	$input = Array("result" => 'error', 'message' => $input);
	output_json($input);
}

function output_json($input = NULL, $content = "application/json") {
	if (!$input)
		$input = Array("result" => 'error', 'message' => 'unknown error');

	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: ' . $content);
	echo json_encode($input);
	die();
}

/* log a variable to firephp */

function fb($var = null, $title = null) {
	$CI = & get_instance();
	if (!$var)
		$CI->firephp->log(var_export($var, true), $title);
	else
		$CI->firephp->log($var, $title);

	return $var;
}

/**
 * Safely tries to read a value from the given array using the given key. If the value is falsy, unset, or null then returns the provided default value.
 */
function array_value($array, $key, $default = null) {
	if (isset($array[$key]) && $array[$key]) {
		return $array[$key];
	} else {
		return $default;
	}
}

/**
 * Alias for loading a CodeIgniter view, includes the application header & footers
 * @param string $view The name of the view to show.
 * @param array $data An array of data to be passed into the views.
 */
function render($view, $data = null, $show_debug_output = false) {
	$CI = & get_instance();

	if (is_dev_server() && $show_debug_output)
		$CI->output->enable_profiler();
	
	// Try an load the view model for this view.
	$view_model = 'includes/js/view_models/' . $view . '.js';
	if (file_exists($view_model)) {
		$data['view_model'] = $view_model . '?' . rand();
	}
	$CI->load->view('common/header', $data);
	$CI->load->view($view, $data);
	$CI->load->view('common/footer', $data);
} 

function insert_row($table = null, $data = null, $fields = null) {
	if (!$table || !is_array($data))
		return false;

	$CI = & get_instance();

	if (is_array($fields)) {
		$row = array();
		foreach ($fields as $field) {
			if (isset($data[$field])) {
				$row[$field] = ($data[$field] != '') ? $data[$field] : null;
			}
		}
	} else {
		$row = $data;
	}
	
	/* at this point we have an array called $row containing only fields from the fields array */
	$success = $CI->db->insert($table, $row);

	if ($success) {
		return $CI->db->insert_id();
	} else {
		return false;
	}
}

function insert_rows($table = null, $row_data_array = null, $fields = null) {
	$result = false;
	$row_data = array();
	
	if (!$table || !is_array($row_data_array))
		return $result;

	$CI = & get_instance();
	foreach($row_data_array as $row) {
		$new_row = array();
		if (is_array($row)) {
			if ($fields) {
				// Only pull out the allowed fields from the provided row data.
				foreach($fields as $allowed_field) {
					if (isset($row[$allowed_field])) {
						$new_row[$allowed_field] = ($row[$allowed_field] != '') ?$row[$allowed_field] : null;
					}
				}
			} else {
				$new_row = $row;
			}
		} else {
			// Data not formatted correctly.
			$row = array();
			break;
		}
		
		$row_data[] = $new_row;
	}

	if (count($row_data) > 0) {
		return $CI->db->insert_batch($table, $row_data);
	} else {
		return false;
	}
}

/* this function is using in the model to updating  records to the database */

function update_row($table = null, $id = null, $data = null, $fields = null) {
	if (!$table || !is_array($data) || !$id)
		return false;
//fb($data);	
	$CI = & get_instance();

	if (is_array($fields)) {
		$row = array();
		foreach ($fields as $field) {
			if (isset($data[$field])) {
				$row[$field] = ($data[$field] != '') ? $data[$field] : null;
			}
		}
	} else {
		$row = $data;
	}
//fb($row);
	/* at this point we have an array called $row containing only fields from the fields array */
	$success = $CI->db->update($table, $row, "id = $id");

	if ($success) {
		return $id;
	} else {
		return false;
	}
}

/* this function is uding for convert nz date to  postgrs date to insert or update in the database */

function db_date($date) {
	if ($date == 'Not Recorded' || $date == null) {
		return null;
	}
//sort out the date value
	list ($day, $month, $year) = explode('/', $date);
	if ($month != '') {
		$date = $year . "-" . $month . "-" . $day;
	}
//now sort out 2-digit years
	return date('Y-m-d', strtotime($date));
}

/* this function is uding for convert nz date to  postgrs date to insert or update in the database */

function db_date_time($date) {
	if ($date == 'Not Recorded') {
		return null;
	}
//sort out the date value
	list ($day, $month, $year_time) = split('[/]', $date);
	list ($year, $time) = split(' ', $year_time);
	if ($month != '') {
		$date = $year . "-" . $month . "-" . $day . ' ' . $time;
	}
//now sort out 2-digit years
	return date('Y-m-d H:i', strtotime($date));
}

function db_dates(&$data = null, $index = null, $keys = null) {
	if (is_array($index)) //if we are not using array_walk
		$keys = $index;

	if (!is_array($data) || !is_array($keys))
		return false;
	/* checking if the fields exit in the data array, then convert it to a pg date */
	foreach ($keys as $key) {
		if (isset($data[$key]) && ($data[$key] != '')) {

			$data[$key] = db_date($data[$key]);
		}
	}
	return $data;
}

function display_date($date = null, $format = 'd/m/Y', $blank_date = "Not Recorded") {
  if ($date == $blank_date || $date == null) {
		return null;
	}
  
	return date($format, strtotime($date));
}

function display_dates(&$data = null, $index = null, $keys = null, $format = 'd/m/Y', $blank_date = "Not Recorded") {
	if (is_array($index)) { //if we are not using array_walk then move variables over
		$format = ($keys) ? $keys : $format;
		$keys = $index;
	}

	if (!is_array($data) || !is_array($keys)) {
		if (is_object($data)) {
			foreach ($keys as $key) {
				if (isset($data->$key) && $data->$key != "0000-00-00 00:00:00")
					$data->$key = display_date($data->$key, $format, $blank_date);
				else
					$data->$key = $blank_date;
			}
		}
		else
			return false;
	}
	else {
		foreach ($keys as $key) {
			if (isset($data[$key]) && $data[$key] != "0000-00-00 00:00:00") {
				$data[$key] = display_date($data[$key], $format, $blank_date);
			}
			else
				$data[$key] = $blank_date;
		}
	}
	return $data;
}

function hex_to_rgb($hex) {
	$hex = ltrim($hex, '# ');
	$rgb['R'] = hexdec(substr($hex, 0, 2));
	$rgb['G'] = hexdec(substr($hex, 2, 2));
	$rgb['B'] = hexdec(substr($hex, 4, 2));

	return $rgb;
}

/**
 * Inserts the given value to the beginning of the array.
 * @param type $array The array.
 * @param type $value The value to insert.
 * @param type $count Number of times to insert.
 */
function pad_array(&$array, $value, $count = 1) {
	if (!is_array($array)) {
		return;
	}

	for ($i = 0; $i < $count; $i++) {
		array_unshift($array, $value);
	}
}

/**
 * Pulls out an array of field values from an array of objects.
 * 
 * The best way to describe this function: if you have an array of user objects from the database and you want
 * an array of all their firstnames only, then this function will do that for you.
 * E.g. Pass in the array of user objects, specify $field as 'full_name' and it will return an array of all their first names.
 * 
 * @param array $results An array of objects or array of arrays.
 * @param string $field The field name to pull out of each object.
 * @param string $key An optional field to be used as value for the index for the returned array. Useful if you want to return an array in the format: user_id => full_name
 * @return array An array.
 */
function sub_array($results, $field, $key = null) {
	if(empty($results)){
		return array();
	}
	$is_array = is_array($results[0]) ? true : false;
  $out = array();
	
  foreach ($results as $row) {
    $i = ($key) ? ($is_array ? $row[$key] : $row->$key) : null;
		$val = $is_array ? $row[$field] : $row->$field;
		
		if ($i) {
			$out[$i] = $val;
		} else {
			$out[] = $val;
		}
  }
	
  return $out;
}

function dbg($array = array(), $should_die = true) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	if ($should_die) {
		die();
	}
}

#-------------------------------------------------------------------------------
# Send a debug message to a log instead of to screen:
function dbglog($var = array(), $varname = "", $should_die=false) {
#-------------------------------------------------------------------------------
	$text = "";
	if (is_object($var)) {
		$text .= (strlen($varname) > 0 ? "$varname:" : "") . print_r((array) $var, true);
	} elseif (is_array($var))
		$text .= (strlen($varname) > 0 ? "$varname:" : "") . print_r($var, true);
	else
		$text .= (strlen($varname) > 0 ? "$varname:" : "") . $var;
	$text .= "\n";
	file_put_contents("/office/branchlib/open/log.txt",$text,FILE_APPEND);
	if ($should_die) {
		die();
	}
}

/*
 * Alternative method to print the contents of an object, array or string for debugging purposes
 */
function debugr($var = array(), $varname = "", $should_die=false) {
	print "<pre>";
	if (is_object($var)) {
		print (strlen($varname) > 0 ? "$varname:" : "") . htmlentities(print_r((array) $var, true));
	} elseif (is_array($var))
		print (strlen($varname) > 0 ? "$varname:" : "") . htmlentities(print_r($var, true));
	else
		print (strlen($varname) > 0 ? "$varname:" : "") . htmlentities($var);
	print "</pre>\n";
	if ($should_die) {
		die();
	}
}

/*
 * convert integer that less that 676 to A-Z or AA-ZZ that could be used for excel column headers
 */
function int_to_alphabetic($int){
	// it does not handle AAA~ZZZ and greater
	if($int > 702 or $int < 1)
		return '';
	else{
		$arr_letter = array("1"=>"A","2"=>"B","3"=>"C","4"=>"D","5"=>"E","6"=>"F","7"=>"G","8"=>"H","9"=>"I","10"=>"J","11"=>"K","12"=>"L","13"=>"M","14"=>"N","15"=>"O","16"=>"P","17"=>"Q","18"=>"R","19"=>"S","20"=>"T","21"=>"U","22"=>"V","23"=>"W","24"=>"X","25"=>"Y","26"=>"Z");
		if($int <= count($arr_letter))
			return $arr_letter[$int];
		else
			if($int%26 == 0)
				return int_to_alphabetic((int)($int/26) -1)."Z";
			else
				return int_to_alphabetic((int)($int/26)).int_to_alphabetic($int%26);
	}
}

/*
 * Wrapper for office_model function get_office_options
 */
function get_office_options($where=null, $blank_label='All Offices') {
	$CI =& get_instance();
	$CI->load->model('office_model');
	$office_options = $CI->office_model->get_office_options($where, $blank_label);
	return $office_options;
}

/*
 * Get the office that the user is filtering by. This is the user's office by default, or the filter_office_id if the user has changed the dropdown in the page header.
 * A blank office_id code means don't apply a filter.
 */
function get_filter_office() {
	$office_options = get_office_options();
	$CI =& get_instance();
	$session_data = $CI->session->all_userdata();
	
	if (isset($session_data['filter_office_id'])) {
		$filter_office_id = $session_data['filter_office_id'];
	} else {
		$filter_office_id = null;
	}
	return (object)array(
		'id'=>$filter_office_id,
		'office_name'=>$office_options[$filter_office_id]
	);
}

/**
 * generate word document based on TBS templates
 * @param type string. The TBS template name.
 * @param type string. The TBS template DIR.
 * @param type string. Where do you want the new file to be saved to. if null, use the default one in constants file.
 * @param type string. The new file name, with file type '.docx'.
 * @param type bool. If true, this would download the file directly. otherwise, just save to the folder.
 */
function generate_individual_tbs_doc($template = null, $template_path = null, $upload_path = null, $new_file_name = null, $is_downloading = false) {
  if (!$template) {
    die('error, no template selected');
  }
  
  $CI =& get_instance();
  $CI->load->library('tbs/tbs');

  $CI->TBS = new clsTinyButStrong;
  // OpenTBS Plugin for working with document files.
  $CI->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
    
  if(!$template_path)
    $template_path = TEMPLATES_DIR;
    
  if(!$upload_path)
    $upload_path = MAILDROPS_TEMP_PATH;		
  
  if(file_exists($template_path.$template)){
    $CI->TBS->LoadTemplate($template_path.$template);
    
    if(!is_dir($upload_path)){
      mkdir($upload_path, 0777, true);
    }
    
    $file_name = (!$new_file_name) ? $template : $new_file_name;
    
    if($is_downloading){
      //dbg($upload_path.$file_name);
      $CI->TBS->Show(OPENTBS_DOWNLOAD, $upload_path.$file_name);
    }else
      $CI->TBS->Show(OPENTBS_FILE, $upload_path.$file_name);	
  }
}

/**
 * Merge docx files into one docx file.
 * @param type string. Name of the file that you would like the other files to merge into. if null, it would use the first file in the $merge_files array.
 * @param type $array. The array of file names you would like to merge into the parent file. if parent file is null, the parent file would be the first value of this array.
 * @param type string. The new file name, with file type '.docx'.
 * @param type string. Where to load the parent file and the other files.
 */
function merge_tbs_docs($main_file, $merge_files_array, $filename,$upload_path = null) {
  $CI =& get_instance();
  $CI->load->library('tbs/tbs');

  $CI->TBS = new clsTinyButStrong;
  // OpenTBS Plugin for working with document files.
  $CI->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
  
  if(!$upload_path)
    $upload_path = MAILDROPS_TEMP_PATH;
      
  //open the main file
  if($main_file){
    $start = 0;
    $main_file_name = $main_file;
    $load_from = MAILDROPS_TEMP_PATH;	
  }else{			
    $start = 1;
    $main_file_name = $merge_files_array[0];		
    $load_from = $upload_path;			
  }  
    
  $main_doc = $CI->tbs->open_document($load_from.$main_file_name);    
  $document = $CI->tbs->read_document($main_doc);	
      
  $no_of_files = count($merge_files_array);
  
  for($i=$start;$i<$no_of_files;$i++){
    if(file_exists($load_from.$merge_files_array[$i])){
      $body_to_merge = $CI->tbs->get_document_body($CI->tbs->get_document($load_from.$merge_files_array[$i]));
      $document = $CI->tbs->merge($document, $body_to_merge);	
    }
  }	
  
  $CI->tbs->replace($main_doc, $document);
              
  if(!is_dir($upload_path)){
    mkdir($upload_path, 0777, true);
  }		
  
  $CI->tbs->save_document($main_doc, $upload_path.$filename);			
  $CI->tbs->close_document($main_doc);
}

/**
 * Download docx file by using TBS
 * @param type string. Where to load the file.
 * @param type string. Name of the file that you would like load and download.
 */
function download_tbs_document($file_path, $file_name){
  $CI =& get_instance();
  $CI->load->library('tbs/tbs');

  $CI->TBS = new clsTinyButStrong;
  // OpenTBS Plugin for working with document files.
  $CI->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
  if(file_exists($file_path.$file_name)){
    $CI->TBS->LoadTemplate($file_path.$file_name);
    $CI->TBS->Show(OPENTBS_DOWNLOAD,$file_name);
  }
}

function format_appointment_time($app){
	if(date("H:i:s",strtotime($app)) == MORNING)
		return 'Morning';
	elseif(date("H:i:s",strtotime($app)) == AFTERNOON)
		return 'Afternoon';
	elseif(date("H:i:s",strtotime($app)) == ANYTIME)
		return 'Anytime';
	else
		return date("H:i",strtotime($app));
}

function pdf_create($html, $filename = '', $stream = TRUE, $paper = 'landscape') {
  require_once(APPPATH . "third_party/dompdf/dompdf_config.inc.php");

  $dompdf = new DOMPDF();
  $dompdf->load_html($html);
  $dompdf->set_paper('A4', $paper);
  $dompdf->render();
  if ($stream) {
    $dompdf->stream($filename . ".pdf");
  } else {
    return $dompdf->output();
  }
}

function float_value($value){
	return floatval(str_replace(',', '', $value));
}

if (!function_exists('lcfirst')) {
	function lcfirst($str)
	{
		$str = is_string($str) ? $str : '';
		if(mb_strlen($str) > 0) {
			$str[0] = mb_strtolower($str[0]);
		}
		return $str;
	}
}


/************************************************ DiRECT HELPERS **********************************************/


