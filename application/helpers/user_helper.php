<?php

/*
 * Helper for user-related functions.
 */


/**
 * Convenience function for getting user data from session.
 * @param string $key (Optional) Retrieve a specific piece of userdata instead of all user data.
 * @return array The user data.
 */
function get_user($key = '') {
  if ($key) {
	return get_instance()->session->userdata($key);
  } else {
	return get_instance()->session->all_userdata();
  }
}

/**
 * Convenience for getting the users first name.
 */
function get_first_name() {
  return get_user('last_name');
}

function get_user_code() {
  return get_user('usercode');
}

//gets the user details of supplied usercode form the local DB
function get_user_details($user)
{
	if($user)
	{
		$CI =& get_instance();
		$details = array_shift($CI->users_model->get_users("user_id = '{$user}'", '', '', 'array'));
		
		if($details)
		{
			return $details;
		}else{
			return false;
		}
	}else{
		return false;
	}
}