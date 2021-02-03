<?php

/**
 * Get the whois content of an ip by selecting the correct server
 *
 * @param string $ip
 * @return false|string
 */
function getIPRegionCode(string $ip)
{
    $w = get_whois_from_server('whois.iana.org', $ip);
    preg_match("#whois:\s*([\w.]*)#si", $w, $data);

    if (is_array($data[1])) {
        var_dump('<b style="color: fuchsia">$dbg</b>');
    }

    // now get actual whois data
    return get_whois_from_server($data[1], $ip);
}

/**
 * Get the whois result from a whois server
 * return text
 *
 * @param $server
 * @param string $ip
 * @return false|string
 */
function get_whois_from_server($server, string $ip)
{
    $data = '';

    // Before connecting lets check whether server alive or not
    $server = trim($server);
    if (!strlen($server)) {
        print "Blank string provided for server" . PHP_EOL;
        die();
    }
    // Create the socket and connect
    $f = fsockopen($server, 43, $errno, $errstr, 3);    //Open a new connection
    if (!$f) {
        print " Failed ";
        return false;
    }
    // Set the timeout limit for read
    if (!stream_set_timeout($f, 3)) {
        die('Unable to set set_timeout');    #Did this solve the problem ?
    }
    // Send the IP to the whois server
    if ($f) {
        $message = $ip . "\r\n";
        fputs($f, $message);
    }
    // Set the timeout limit for read
    if (!stream_set_timeout($f, 3)) {
        die('Unable to stream_set_timeout');    #Did this solve the problem ?
    }
    // Set socket in non-blocking mode
    stream_set_blocking($f, 0);
    // If connection still valid
    if ($f) {
        while (!feof($f)) {
            $data .= fread($f, 128);
        }
    }

    // Find data
    $needle = 'Country:';
    // escape special characters in the query
    $pattern = preg_quote($needle, '/');
    // finalise the regular expression, matching the whole line
    $pattern = "/^.*$pattern.*\$/m";
    // search, and store all matching occurences in $matches
    if (preg_match("#Country:\s*([\w.]*)#si", $data, $matches)) {
        echo " Found matches:\n ";
        $foundCountry = $matches[0];
        $countryCode = substr($foundCountry, -2);
        return $countryCode;
    }

    // Now return the data
    return $data;
}
