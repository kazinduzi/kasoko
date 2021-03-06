<?php
namespace Helpers;

use Kazinduzi;

class Html
{
    public static $_docTypes = array(
        'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        'xhtml-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        'xhtml-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        'xhtml-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
        'html5' => '<!doctype html>',
    );

    /**
     * Returns a doctype string.
     *
     * Possible doctypes:
     *
     *  - html4-strict:  HTML4 Strict.
     *  - html4-trans:   HTML4 Transitional.
     *  - html4-frame:   HTML4 Frameset.
     *  - xhtml-strict:  XHTML1 Strict.
     *  - xhtml-trans:   XHTML1 Transitional.
     *  - xhtml-frame:   XHTML1 Frameset.
     *  - xhtml11:       XHTML1.1.
     *  - html5:         HTML5 DOCTYPE
     *
     * @param string $type Doctype to use.
     * @return string Doctype string
     * @access public
     */
    public static function docType($type = 'xhtml-trans')
    {
        if (isset(self::$_docTypes[$type])) {
            return self::$_docTypes[$type] . "\n";
        }
        return null;
    }

    /**
     * Returns a charset META-tag.
     *
     * @param string $charset The character set to be used in the meta tag. If empty,
     *  The App.encoding value will be used. Example: "utf-8".
     * @return string A meta tag containing the specified character set.
     * @access public
     */
    public static function charset($charset = 'utf-8')
    {
        if (empty($charset)) {
            $charset = strtolower(Kazinduzi::$encoding);
        }
        return sprintf('<meta http-equiv="Content-Type" content="text/html; charset=%s">', $charset) . "\n";
    }

    /**
     * Creates a link to an external resource and handles basic meta tags
     *
     * ### Options
     *
     * - `inline` Whether or not the link element should be output inline, or in scripts_for_layout.
     *
     * @param string $type The title of the external resource
     * @param mixed $url The address of the external resource or string for content attribute
     * @param array $options Other attributes for the generated tag. If the type attribute is html,
     *    rss, atom, or icon, the mime-type is returned.
     * @return string A completed `<link />` element.
     * @access public
     */
    public static function meta($type, $url = null, $options = array())
    {
        $out = "";
        $types = array(
            'rss' => array('type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => $type, 'link' => $url),
            'atom' => array('type' => 'application/atom+xml', 'title' => $type, 'link' => $url),
            'search' => array('type' => 'application/opensearchdescription+xml', 'title' => $type, 'link' => $url),
            'icon' => array('type' => 'image/x-icon', 'rel' => 'shortcut icon', 'link' => $url),
            'keywords' => array('name' => 'keywords', 'content' => $url),
            'description' => array('name' => 'description', 'content' => $url),
            'robots' => array('name' => 'robots', 'content' => $url),
            'generator' => array('name' => 'generator', 'content' => $url),
            'viewport' => array('name' => 'viewport', 'content' => $url),
            'Content-Type' => array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=' . Kazinduzi::getCharset() . ''),
            'X-UA-Compatible' => array('http-equiv' => 'X-UA-Compatible', 'content' => $url),
            'author' => array('name' => 'author', 'content' => $url),
            'copyright' => array('name' => 'copyright', 'content' => $url),
        );
        if ($type === 'icon' && $url === null) {
            $types['icon']['link'] = 'fav.ico';
        }
        if (is_array($types[$type]) && isset($types[$type]['link']) && $type !== 'icon') {
            $out .= sprintf('<link rel="%s" type="%s" title="%s" href="%s">', $types[$type]['rel'], $types[$type]['type'], $types[$type]['title'], $types[$type]['link']
            );
            return $out . "\n";
        } elseif (is_array($types[$type]) && !empty($types[$type]['name'])) {
            $out .= sprintf('<meta name="%s" content="%s">', $types[$type]['name'], $types[$type]['content']);
            return $out . "\n";
        } elseif (is_array($types[$type]) && $type === 'icon') {
            $out .= sprintf('<link rel="%s" type="%s" href="%s">', $types[$type]['rel'], $types[$type]['type'], $types[$type]['link']
            );
            return $out . "\n";
        } elseif (is_array($types[$type]) && $type === 'X-UA-Compatible') {
            $out .= sprintf('<meta http-equiv="%s" content="%s">', $types[$type]['http-equiv'], $types[$type]['content']);
            return $out . "\n";
        } elseif (is_array($types[$type]) && $type === 'Content-Type') {
            $out .= sprintf('<meta http-equiv="%s" content="%s">', $types[$type]['http-equiv'], $types[$type]['content']);
            return $out . "\n";
        }
    }

    /**
     * Create HTML link anchors.
     *
     * @param   string  URL or URI string
     * @param   string  link text
     * @param   array   HTML anchor attributes
     * @return  string
     */
    public static function anchor($uri, $title = null, $attributes = null)
    {
        if ($uri === '') {
            $siteUrl = '/';
        } else {
            $siteUrl = $uri;
        }
        return '<a href="' . static::specialchars($siteUrl, false) . '"'
                . (is_array($attributes) ? static::attributes($attributes) : '') . '>'
                . ($title === null ? $siteUrl : $title) . '</a>';
    }

    /**
     * Convert special characters to HTML entities
     *
     * @param   string   string to convert
     * @param   boolean  encode existing entities
     * @return  string
     */
    public static function specialchars($str, $double_encode = true)
    {
        $str = (string) $str;
        if ($double_encode === true) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        } else {
            if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
                $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8', false);
            } else {
                $str = preg_replace('/&(?!(?:#\d++|[a-z]++);)/ui', '&amp;', $str);
                $str = str_replace(array('<', '>', '\'', '"'), array('&lt;', '&gt;', '&#39;', '&quot;'), $str);
            }
        }
        return $str;
    }

    /**
     * Compiles an array of HTML attributes into an attribute string.
     *
     * @param   string|array array of attributes
     * @return  string
     */
    public static function attributes($attrs)
    {
        if (empty($attrs)) {
            return '';
        }
        if (is_string($attrs)) {
            return ' ' . $attrs;
        }
        $compiled = '';
        foreach ($attrs as $key => $val) {
            $compiled .= ' ' . $key . '="' . $val . '"';
        }
        return $compiled;
    }

    /**
     * Creates a stylesheet link.
     *
     * @param   string|array filename , or array of filenames to match to array of medias
     * @param   string|array media type of stylesheet, or array to match filenames
     * @return  string
     */
    public static function css($style, $media = false)
    {
        if (is_array($media)) {
            $media = implode(', ', $media);
        }
        return static::link($style, 'stylesheet', '.css', $media) . "\n";
    }

    /**
     * Creates a link tag.
     *
     * @param   string|array filename
     * @param   string|array relationship
     * @param   string|array mimetype
     * @param   string        specifies suffix of the file
     * @param   string|array specifies on what device the document will be displayed
     * @return  string
     */
    public static function link($href, $rel, $suffix = false, $media = false)
    {
        $compiled = '';
        if (is_array($href)) {
            foreach ($href as $_href) {
                $_rel = is_array($rel) ? array_shift($rel) : $rel;
                $_media = is_array($media) ? array_shift($media) : $media;
                $compiled .= static::ink($_href, $_rel, $suffix, $_media);
            }
        } else {
            $suffix = (!empty($suffix) AND strpos($href, $suffix) === false) ? $suffix : '';
            $media = empty($media) ? '' : ' media="' . $media . '"';
            $compiled = '<link rel="' . $rel . '" href="' . '/html/' . $href . $suffix . '"' . $media . '>';
        }
        return $compiled;
    }

    /**
     * Creates a script link.
     *
     * @param   string|array filename
     * @return  string
     */
    public static function js($script)
    {
        $compiled = '';
        if (is_array($script)) {
            foreach ($script as $name) {
                $compiled .= static::js($name);
            }
        } else {
            if (strpos($script, '//') === false) {
                $suffix = (substr($script, -3) !== '.js') ? '.js' : '';
                $script = '/html/js/' . $script . $suffix;
            }
            $compiled = '<script src="' . $script . '"></script>';
        }
        return $compiled . "\n";
    }

    /**
     * Creates a image link.
     *
     * @param   string        image source, or an array of attributes
     * @param   string|array image alt attribute, or an array of attributes
     * @return  string
     */
    public static function img($src = null, $alt = null)
    {
        $attributes = is_array($src) ? $src : array('src' => $src);
        if (is_array($alt)) {
            $attributes += $alt;
        } elseif (!empty($alt)) {
            $attributes['alt'] = $alt;
        }

        if (!isset($attributes['alt'])) {
            $attributes['alt'] = '';
        }
        if (strpos($attributes['src'], '://') === false) {
            $attributes['src'] = '/html/images/' . $attributes['src'];
        }
        return '<img ' . static::attributes($attributes) . ' />';
    }

}

function h($data, $encode_entities = true)
{
    return Html::specialchars($data, $encode_entities);
}
