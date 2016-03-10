<?php
/**
 * A library for connecting to and retrieving information from an Active Directory server using the LDAP protocol.
 */
class Active_Directory {

	private $CI;
	private $server;
	private $base_dn;
	private $user_key;
	private $login;
	private $pass;
	private $countries;
	private $countries_regions;

	public function __construct() {		
		$this->CI =& get_instance();
		
		$this->server				= 'CHDC01.opus.global';		// barnz: We need a host that determines the correct local AD server to use (similar to Opussvof)
		$this->base_dn				= 'DC=opus,DC=global';
		$this->user_key				= 'sAMAccountName';			// Attribute key used to identify users by their usercode.
		
		// Active Directory - LDAP Binding credentials.
		$this->login				= 'CN=nzdev,OU=CH,OU=Users,OU=SR,OU=NZ,DC=opus,DC=global';
		$this->pass					= 'Opu5nzd3v';

		$this->countries 			= array('NZ', 'AU', 'CA', 'CM', 'UK', 'US');
		$this->countries_regions 	= array('NZ'=>array('CR', 'NR', 'SR', 'WR'), 'AU'=>array('NS', 'QU', 'WA'), 'CA'=>array('CE', 'CW', 'SW'), 'CM'=>array('GR'), 'UK'=>array('NO','SO'), 'US'=>array('CW'));
	}

	//******************//
	// PUBLIC INTERFACE	//
	//******************//

	/**
	 * Fetches a user record by their usercode.
	 * @return array An array of user data.
	 */
	public function get_user_details($usercode) {
		$con = $this->connect();
		$ldap_data = ldap_get_entries($con, $this->find($con, $this->user_key, $usercode));
		$this->disconnect($con);
		
		return isset($ldap_data[0]) ? array_shift($this->map_users($ldap_data)) : null;
	}

	/**
	 * This function does the same thing as get_user but this should be used only for the staff image purposes
	 * @return image
	 */
	public function get_user_image($usercode) {
		$con = $this->connect();
		$ldap_data = ldap_get_entries($con, $this->find($con, $this->user_key, $usercode));
		$this->disconnect($con);

		if (isset($ldap_data) && isset($ldap_data[0]['thumbnailphoto'][0])) {
			$imgdata = imagecreatefromstring($ldap_data[0]['thumbnailphoto'][0]);
			if ($imgdata) {
				// Send JPEG headers
				header("Content-type: image/jpeg");
				// Output the image data
				$image = imagejpeg($imgdata);

				// Clean up the resource
				imagedestroy($imgdata);
				return $image;
				exit();
			}
		} else {
			$this->get_blank_image();
		}
	}
	
	/**
	 * This function returns an no image avaiable image
	 * @return image
	 */
	public function get_blank_image() {
		$im = imagecreate(143, 200);
		$y = imagesy($im)/2;
		$bg = imagecolorallocate($im, 255,255,255);
		$textcolor = imagecolorallocate($im, 0,0,0);

		imagestring($im, 5, 30, $y-20, 'No Image', $textcolor);
		imagestring($im, 5, 30, $y+10, 'Available', $textcolor);

		// Output the image
		header('Content-type: image/png');

		$image = imagepng($im);
		imagedestroy($im);
		return $image;
		exit();
	}

	/**
	 * Find user(s) by a specific user attribute and value.
	 * @param type $attribute An attribute of the user i.e name, title etc. see user_attribute_map()
	 * @param type $value A value to look for.
	 * @return array An array of matching users.
	 */
	public function find_users($attribute, $value) {
		$con = $this->connect();
		$ldap_data = ldap_get_entries($con, $this->find($con, $attribute, $value));
		$this->disconnect($con);

		return isset($ldap_data[0]) ? $this->map_users($ldap_data) : null;
	}

	/**
	 * Find user(s) by common searchable fields.
	 */
	public function search_users($search) {
		$con = $this->connect();
		if ($con && $search) {
			$search = preg_replace("/[\(\)\'\"]/", "", $search);
			$str_user = trim(strtolower($search));
			$values_to_check = array("cn", 'mail', 'samaccountname', 'telephonenumber', 'homephone', 'mobile');
			$keywords = explode(" ", $str_user);

			$filter = "(|";
			foreach ($values_to_check as $value) {
				$filter .= "(&";
				foreach ($keywords as $keyword) {
					$filter .= "(" . $value . "=*" . $keyword . "*)";
				}
				$filter .= ")";
			}
			$filter .= ")";

			if ($search == "") {
				$filter = 'samaccountname=*';
			}
			$ldap_data = @ldap_get_entries($con, @ldap_search($con, $this->base_dn, $filter));
			$this->disconnect($con);

			return isset($ldap_data[0]) ? $this->map_users($ldap_data) : array();
		}
	}
	
	/**
	 * Allows you to get all users for the given office name
	 * @param type $search, office name. eg. Christchurch
	 */
	public function get_users_for_office($search) {
		$people = array();
		$con = $this->connect();
		if ($con) {
			$str_office = trim(strtolower($search));
			$returnThese = array('cn', 'sn', 'mail','l','c','co','extensionattribute1','samaccountname','telephonenumber', 'physicaldeliveryofficename','givenname','employeenumber','cfmsregiondivision','mobile','title','telephonenumber');
			$ldap_data = ldap_get_entries($con, @ldap_search($con, $this->base_dn, "physicaldeliveryofficename=".$str_office, $returnThese));
			$this->disconnect($con);
			
			if(isset($ldap_data[0])){
				$people = $this->map_users($ldap_data);
				function build_sorter($a, $b) {
					return strnatcmp($a['cn'], $b['cn']);
				}
				usort($people, "build_sorter");
			}
			
			return $people;
		}
	}
		
	/**
	 * Allows you to get all offices from active directory
	 * 
	 */
	public function get_all_offices() {
		$offices = array();
		$con = $this->connect();
		foreach($this->countries_regions as $country => $regions){
			foreach($regions as $region){
				$dn = "OU=Groups,OU=$region,OU=$country," . $this->base_dn;
				$ldap_data = ldap_get_entries($con, @ldap_search($con, $dn, "description=*"));				
				if(!empty($ldap_data)){
					foreach($ldap_data as $ad){
						if(isset($ad['objectclass'][1]) && ($ad['objectclass'][1] == 'organizationalUnit')){
							$offices[str_replace(' Office','',$ad['description'][0])] = $region;
						}
					}
				}
			}
		}
		$this->disconnect($con);
		ksort($offices);
		return $offices;
	}
	
	/**
	 * Allows you to get all countries from active directory
	 * 
	 */
	public function get_all_countries() {
		$result = array();

		foreach($this->country_name as $country => $countries){
			foreach($countries as $country){
				$result[$country] = $country;
			}
		}
		
		sort($result);
		return $result;
	}

	function get_bola_users()
	{		
		$users = array();
		
		$con = $this->connect();

		$admin_dn = "OU=Admin Accounts,OU=Services," . $this->base_dn;
		$regional_admin_dn = "OU=Regional Admins,OU=Services," . $this->base_dn;
		$returnThese = array('samaccountname');
		
		$admin_data = ldap_get_entries($con, @ldap_search($con, $admin_dn, "cn=*",$returnThese));
		$regional_admin_data = ldap_get_entries($con, @ldap_search($con, $regional_admin_dn, "cn=*",$returnThese));
		
		$this->disconnect($con);
		
		if(isset($regional_admin_data[0])){
			unset($regional_admin_data['count']);
			foreach ($regional_admin_data as $user_row) {
				$user = '';
				// Pull out only the data we want.
				if (isset($user_row["samaccountname"]) && isset($user_row["samaccountname"][0])) {
					$user = trim(preg_replace('/\s+/', ' ', $user_row["samaccountname"][0]));
					$users[] = str_replace('_a', '', $user);
				}
			}
		}
		
		if(isset($admin_data[0])){
			unset($admin_data['count']);
			foreach ($admin_data as $user_row) {
				$user = '';
				// Pull out only the data we want.
				if (isset($user_row["samaccountname"]) && isset($user_row["samaccountname"][0])) {
					$user = trim(preg_replace('/\s+/', ' ', $user_row["samaccountname"][0]));
					$users[] = str_replace('_a', '', $user);
				}
			}
		}
			
		//to be sorted as this should come from "operatives"
		$users[] = 'chkrw0';
		$users[] = 'chalb0';
		$users[] = 'chkxr0';
		$users[] = 'auajm0';
		$users[] = 'hagww0';
		$users[] = 'wemnw0';
		$users[] = 'chmdb0';
		$users[] = 'chnyh0';
		$users[] = 'natjw0';
		$users[] = 'habrc0';
		
		//hard code these users for now.
		$users[] = 'aupab0';
		$users[] = 'grpjw0';
		$users[] = 'wemdl0';
		$users[] = 'ayrlc0';
		$users[] = 'chcag0';
		$users[] = 'hagda0';
		$users[] = 'natra0';
		$users[] = 'vcfts0';	
		
		sort($users);
		return $users;
	}
	
	/**
	 * Allows you to get all regions from active directory
	 * 
	 */
	public function get_all_regions() {
		$result = array();

		foreach($this->countries_regions as $country => $regions){
			foreach($regions as $region){
				$result[$region] = $region;
			}
		}
		
		sort($result);
		return $result;
	}

	/**
	 * Allows you to get all users for the given region or office.
	 * @param type $country_code The top level country code.
	 * @param type $region_code The region code.
	 * @param type $office_code (optional) An office code to only get users for a specific office.
	 */
	public function get_users($country_code, $region_code, $office_code = null) {
		$con = $this->connect();

		$dn = "OU=Users,OU=$region_code,OU=$country_code," . $this->base_dn;
		if ($office_code) {
			$dn = "OU=$office_code," . $dn;
		}

		$ldap_data = ldap_get_entries($con, @ldap_search($con, $dn, "cn=*"));
		$this->disconnect($con);

		return isset($ldap_data[0]) ? $this->map_users($ldap_data) : null;
	}

	/**
	 * Attempts to authenticate the usercode and pass using the Active Directory server.
	 * Basically if we can bind to AD using the users credentials, they are valid.
	 */
	public function authenticate_user($usercode, $pass) {
		// Get full user data.
		$user = $this->get_user_details($usercode);
		$bind = null;

		if ($user && $pass) {
			// Grab the users full DN.
			$dn = $user['distinguished_name'];

			$con = $this->connect();
			$bind = @ldap_bind($con, $dn, $pass);
			$this->disconnect($con);
		}

		return $bind ? $user : false;
	}

	//********************//
	// PRIVATE INTERFACE	//
	//********************//

	/**
	 * Attempt a connection and binding to Active Directory.
	 * @return boolean The connection handle or null if unsuccessful.
	 */
	private function connect() {
		// Connect to the directory server.
		$con = ldap_connect($this->server);
		// Settings required by our Active Directory.
		ldap_set_option($con, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);

		// Attempt to bind using the provided credentials.
		return ldap_bind($con, $this->login, $this->pass) ? $con : null;
	}

	/**
	 * Wrapper/alias for closing the open connection.
	 * @param resource $con A ldap connection handle.
	 */
	private function disconnect($con) {
		ldap_close($con);
	}

	/**
	 * Internal function for looking through the active directory records, finding records that match the given attribute and its value.
	 * @param resource A connection to the directory server.
	 * @param string $attribute The attribute to look through.
	 * @param string $val A attribute value to look for.
	 * @param string $dn (optional) The distinguished name to start the search from.
	 * @return array An LDAP record.
	 */
	private function find($con, $attribute, $val, $dn = null) {
		return @ldap_search($con, $dn ? $dn : $this->base_dn, $attribute . '=' . $val);
	}

	/**
	 * Maps ldap user rows to a format used by all our apps. 
	 * Basically pulls out only the information we need and maps into a simple flat array.
	 * @param array $user_rows User rows returned from any user related ldap queries.
	 * @return array A simple array of users and their data.
	 */
	private function map_users($user_rows) {
		// Remove the result count array value that gets added by LDAP.
		unset($user_rows['count']);
		// Holds the mapped users.
		$users = array();

		foreach ($user_rows as $user_row) {
			$user = array();
			// Pull out only the data we want.
			foreach ($this->user_attribute_map() as $attribute => $name) {
				$name = is_array($name) ? $name : array($name);
				
				foreach($name as $mapped_name) {
					if (isset($user_row[$attribute]) && isset($user_row[$attribute][0])) {
						if ($mapped_name == 'photo') { # Don't break the encoding for the photo!
							//$user[$mapped_name] = $user_row[$attribute][0];
						} else {
							$user[$mapped_name] = trim(preg_replace('/\s+/', ' ', $user_row[$attribute][0]));
						}
					} else {
						$user[$mapped_name] = null;
					}
				} 
			}
			//as group and server has been removed from active directory, we set it to null by default for now.
			$user['group'] = '';
			$user['server'] = '';
			$users[] = $user;
		}

		return $users;
	}

	/**
	 * Maps the AD user attributes to more meaningful names.
	 * @return array An array of mapped attribute names.
	 */
	private function user_attribute_map() {
		return array(
		  'employeenumber' => 'employeenumber',
		  'samaccountname' => array('uid', 'usercode'),
		  'givenname' => 'first_name',
		  'sn' => array('sn', 'last_name'),
		  'cn' => array('cn', 'full_name'),
		  'mail' => array('mail', 'email'),
		  'telephonenumber' => array('phone', 'telephonenumber'),
		  'homephone' => 'personal_phone',
		  'mobile' => 'mobile',
		  'title' => 'jobtitle',
		  'physicaldeliveryofficename' => array('physicaldeliveryofficename', 'office'),
		  //'l' => 'office',
		  'extensionattribute2' => 'officeCode',
		  'extensionattribute1' => array('region', 'cfmsregiondivision'),
		  'c' => 'country',
		  'co' => 'country_name',
		  'company' => 'company',
		  'streetaddress' => 'work_address',
		  'profilepath' => 'user_directory',
		  'lastlogon' => 'last_login',
		  'logoncount' => 'login_count',
		  'homedrive' => 'home_drive',
		  'distinguishedname' => 'distinguished_name',
		  'thumbnailphoto' => 'photo'
		);
	}

}
