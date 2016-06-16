<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Country {

    private static $dbo;

    /**
     *
     * @return type
     */
    public static function getAll() {
        static::$dbo = Kazinduzi::db();
        static::$dbo->setQuery("SELECT * FROM `country`");
        return static::$dbo->fetchAssocList();
    }

    /**
     *
     * @param type $country_id
     * @return type
     */
    public static function getZonesByCountryId($country_id) {
        static::$dbo = Kazinduzi::db();
        static::$dbo->setQuery("SELECT * FROM `country_zone` WHERE `country_id` LIKE '".$country_id."' ORDER BY `name` ASC;");
        return static::$dbo->fetchAssocList();
    }

    /**
     *
     * @param type $countryid
     * @return type
     */
    public static function getCountry($countryid) {
        static::$dbo = Kazinduzi::db();
        static::$dbo->setQuery("SELECT * FROM `country` WHERE `id` = ".(int)$countryid);
        return static::$dbo->fetchObjectRow();
    }

    /**
     *
     * @param type $countryid
     * @param type $zoneid
     * @return type
     */
    public static function getZone($countryid, $zoneid) {
        static::$dbo = Kazinduzi::db();
        static::$dbo->setQuery("SELECT * FROM `country_zone` WHERE `id` = ".(int)$zoneid." AND `country_id` = ".(int)$countryid);
        return static::$dbo->fetchObjectRow();
    }

    /**
     * get Html zonnes options by country
     * @param int $country_id
     * @param int|null $zone_id
     * @return string
     */
    public static function getZonesByCountry($country_id, $zone_id=null) {
        echo $country_id;
        $output = '<option value="">Select a zone</option>';
    	$results = Country::getZonesByCountryId($country_id);
        if (!$results) {
		  	return $output .= '<option value="0">&nbsp;</option>';
		}
      	foreach ($results as $result) {
        	$output .= '<option value="' . $result['id'] . '"';
	    	$output .= (isset($zone_id) && ($zone_id==$result['id'])) ? ' selected="selected"' : '';
	    	$output .= '>' . $result['name'] . '</option>';
    	}
		return $output;
    }

}