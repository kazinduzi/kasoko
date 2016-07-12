<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of News
 *
 * @author Emmanuel_Leonie
 */
class News extends Model
{

    protected static $url = 'http://news.yahoo.com/rss/open-source';
    public $table = 'news';

    /**
     *
     * @param type $url
     * @return type
     */
    public static function fetchAll($url = '')
    {
        if (!empty($url)) self::$url = $url;
        $news = array();
        // Using cache for the RSS Feed
        try {
            $Cache = Cache::getInstance();
            if (($data = $Cache->get('allItems')) == false) {
                $xml = simplexml_load_file(self::$url);
                foreach ($xml->channel->item as $rss) {
                    $news[] = (array)$rss;
                }
                $data = array(
                    'channel_title' => (string)$xml->channel->title,
                    'items' => $news
                );
                $Cache->set('allItems', $data, 3600);
            }
        } catch (Exception $e) {
            print_r($e);
        }
        return $data;
    }

    /**
     *
     * @param type $url
     * @param type $count
     * @return type
     */
    public static function fetch($url = null, $count = 10)
    {
        if (!empty($url)) self::$url = $url;
        $news = array();
        // Using cache for the RSS Feed
        try {
            $Cache = Cache::getInstance();
            // $Cache->clean();
            if (($data = $Cache->get('limitedItems')) == false) {
                $xml = simplexml_load_file(self::$url);
                for ($i = 0; $i < $count; $i++) {
                    $news[] = (array)$xml->channel->item[$i];
                }

                $data = array(
                    'channel_title' => (string)$xml->channel->title,
                    'items' => $news
                );
                $Cache->set('limitedItems', $data, 3600);
            }
        } catch (Exception $e) {
            print_r($e);
        }
        return $data;
    }

    /**
     *
     * @staticvar self $Instance
     * @param type $class
     * @param type $opts
     * @return \self
     */
    public static function getInstance($class = __CLASS__, $opts = array())
    {
        static $Instance;
        if ($Instance === null) {
            $Instance = new self;
        }
        return $Instance;

    }
}
