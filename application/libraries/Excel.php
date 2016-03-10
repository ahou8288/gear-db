<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 //die(dirname(__FILE__));
 //die(APPPATH);
require_once (APPPATH."/third_party/PHPExcel.php");
class Excel extends PHPExcel {
    public function __construct() {
        parent::__construct();
    }
}
