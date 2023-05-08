<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('is_localhost')) {
	function is_localhost(){
		return $_SERVER["HTTP_HOST"] === 'localhost';
	}
}

if(!function_exists('versionAsset')) {
  function versionAsset($asset)
  {
    return base_url($asset)."?ver=".filemtime(FCPATH.$asset);
  }
}

if(!function_exists('defaultSet')) {
  function defaultSet($var,$defaultVal = false)
  {
	$default = $defaultVal ? false : $defaultVal;
    return isset($var) ? $var : $default;
  }
}

//////////////////////////////////////////////////////////////////////
//PARA: Date Should In YYYY-MM-DD Format
//RESULT FORMAT:
// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
// '%m Month %d Day'                                            =>  3 Month 14 Day
// '%d Day %h Hours'                                            =>  14 Day 11 Hours
// '%d Day'                                                        =>  14 Days
// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
// '%h Hours                                                    =>  11 Hours
// '%a Days                                                        =>  468 Days
//////////////////////////////////////////////////////////////////////
if(!function_exists('dateDifference')){
  function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
  {
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);
  }
}

/* End of file general_helper.php */
/* Location: ./application/helpers/general_helper.php */
