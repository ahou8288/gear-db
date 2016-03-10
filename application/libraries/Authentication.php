<?php


/**
 * An authentication library that supports NTLM with an Active Directory fallback.
 */
class Authentication {
	var $ci;

	function __construct() {
		$this->ci = & get_instance();
		$this->ci->load->library('session');
		$this->ci->load->library('user_agent');

	}
	
	public function login() {
		// Holds the users data.
		$user = null;
		$error_message = null;

		// Break out if logged in already.
		if ($this->is_logged_in()) {
			return true;
		}

		// Auto-authenticate the using ntlm.
		$user_code = $this->_ntlm_authenticate('user_code');
		if ($user_code) {
			$user = $this->ci->active_directory->get_user_details($user_code);
		} else {
			$error_message = 'NTLM authentication failed for ' . $user_code;
		}

		// Setup the user session.
		if (!$this->set_session($user)) {
			$error_message = 'Could not set the users session. No data came back from active directory.';
		}
		
		

		return $error_message;
	}

	/**
	 * Login as a specific user. A user name and password is required, however admins can login simply by providing a user code.
	 */
	public function login_as($user_code, $password = null) {
		// Holds the users data.
		$user = null;
		$error_message = null;
		
		if ($user_code) {
				if ($password) {
					// Verify the fallback username & pass using Active Directory.
					if (!$user = $this->ci->active_directory->authenticate_user($user_code, $password)) {
						$error_message = 'Your usercode and password combination were incorrect. Please try again.';
					}
				} else {
					// Simply find a matching active directory user to login as.
					if (!$user = $this->ci->active_directory->get_user_details($user_code)) {
						$error_message = 'No active Opus user was found by ' . $user_code;
					}
				}
		} else {
			$error_message = 'Please enter an Opus user code';
		}
		
		// Setup the user session.
		if (!$error_message) {
			$this->set_session($user, true);
		}
		
		return $error_message;
	}

	public function logout() {
		$this->ci->session->sess_destroy();
	}

	public function is_logged_in() {
		//return false;
		return $this->ci->session->userdata('usercode') != false;
	}

	 /**
	 * Authenticate using the users opus usercode. NTLM is handled by an apache module installed
	 * on the server, so all we need to do is read out the REMOTE_USER variable under the $_SERVER globals.
	 * In order for this to work you need to make sure that the line: Require valid-user exists in the htaccess
	 */
	function _ntlm_authenticate($key) {
	        $remote_user = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : null;
	        if ($remote_user) {
                // Strip the domain part if it exists.
                $remote_user = array_pop(explode('\\', $remote_user));
                return $remote_user;
	        } else {
	        	$headers = apache_request_headers();

	        	if (!isset($headers['Authorization'])) {
	        		header('HTTP/1.1 401 Unauthorized');
	        		header('WWW-Authenticate: NTLM');
	        		exit;
	        	}

	        	$auth = $headers['Authorization'];

	        	if (substr($auth, 0, 5) == 'NTLM ') {
	        		$msg = base64_decode(substr($auth, 5));
	        		if (substr($msg, 0, 8) != "NTLMSSP\x00")
	        			die('error header not recognised');

	        		if ($msg[8] == "\x01") {
	        			$msg2 = "NTLMSSP\x00\x02\x00\x00\x00" .
	        					"\x00\x00\x00\x00" . // target name len/alloc
	        					"\x00\x00\x00\x00" . // target name offset
	        					"\x01\x02\x81\x00" . // flags
	        					"\x00\x00\x00\x00\x00\x00\x00\x00" . // challenge
	        					"\x00\x00\x00\x00\x00\x00\x00\x00" . // context
	        					"\x00\x00\x00\x00\x00\x00\x00\x00"; // target info len/alloc/offset

	        			header('HTTP/1.1 401 Unauthorized');
	        			header('WWW-Authenticate: NTLM ' . trim(base64_encode($msg2)));
	        			exit;
	        		} else if ($msg[8] == "\x03") {
	        			$user_details = array(
	        			  'user_code' => $this->_get_msg_str($msg, 36),
	        			  'domain' => $this->_get_msg_str($msg, 28),
	        			  'workstation' => $this->_get_msg_str($msg, 44)
	        			);
	        		}
	        	}

	        	//if we have a return key then just return the array value, else return the array
	        	return ($key && isset($user_details[$key])) ? $user_details[$key] : $user_details;
	        }
	}

	private function _get_msg_str($msg, $start, $unicode = true) {
		$len = (ord($msg[$start + 1]) * 256) + ord($msg[$start]);
		$off = (ord($msg[$start + 5]) * 256) + ord($msg[$start + 4]);
		if ($unicode)
			return str_replace("\0", '', substr($msg, $off, $len));
		else
			return substr($msg, $off, $len);
	}

	/**
	 * Sets the user session for this site.
	 */
	private function set_session($userdata) {
		if (!empty($userdata)) {
			//we dont really need the user photo to be in the session
			if(isset($userdata['photo'])){
				unset($userdata['photo']);
			}

			$this->ci->load->model('application_model');
			
			//get user from staff changes database
			$user = array_shift($this->ci->application_model->get_users(array('opus_user_code'=>$userdata['usercode'])));
			//Different levels of authentication
			if($user){
				$userdata['is_super_admin'] = $user->is_super_admin == 't' ? 't' : 'f';
				$userdata['is_it'] = $user->is_it == 't' ? 't' : 'f';
				$userdata['is_hr'] = $user->is_hr == 't' ? 't' : 'f';
				$userdata['is_admin_team'] = $user->is_admin_team == 't' ? 't' : 'f';
				$userdata['is_payroll'] = $user->is_payroll == 't' ? 't' : 'f';
				$userdata['user_id'] = $user->id;
			}

			$this->ci->session->set_userdata($userdata);

			return true;
		} else {
			return false;
		}
	}
}
