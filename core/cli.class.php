<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Helper functions for working in a command-line environment.
 *
 */
class Cli
{

    /**
     * Returns one or more command-line options. Options are specified using
     * standard CLI syntax:
     * php index.php --username=john.smith --password=secret --var="some value with spaces"
     * // Get the values of "username" and "password"
     * $auth = Cli::options('username', 'password');
     * @param   string  option name
     * @param   ...
     * @return  array
     */
    public static function options($options)
    {
        // Get all of the requested options
        $options = func_get_args();
        // Found option values
        $values = array();
        // Skip the first option, it is always the file executed
        for ($i = 1; $i < $_SERVER['argc']; $i++) {
            if (!isset($_SERVER['argv'][$i])) {
                break; // No more args left
            }
            // Get the option
            $opt = $_SERVER['argv'][$i];
            if (substr($opt, 0, 2) !== '--') {
                continue; // This is not an option argument
            }
            // Remove the "--" prefix
            $opt = substr($opt, 2);
            if (strpos($opt, '=')) {
                list ($opt, $value) = explode('=', $opt, 2); // Separate the name and value
            } else {
                $value = null;
            }
            if (in_array($opt, $options)) {
                $values[$opt] = $value; // Set the given value
            }
        }
        return $values;
    }

}
