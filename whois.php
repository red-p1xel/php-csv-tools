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
    print "Response: " . PHP_EOL;
    print $w;
    print PHP_EOL;

    preg_match("#whois:\s*([\w.]*)#si", $w, $data);
    $whois_server = $data[1];
    print "Whois Server: $whois_server " . PHP_EOL;

    // now get actual whois data
    return get_whois_from_server($whois_server, $ip);
}

/**
 * Get the whois result from a whois server
 * return text
 *
 * @param string $server
 * @param string $ip
 * @return false|string
 */
function get_whois_from_server(string $server, string $ip)
{
    $data = '';

    // Before connecting lets check whether server alive or not
    $server = trim($server);
    if (!strlen($server)) {
        print "Blank string provided for server" . PHP_EOL;
        die();
    }
    // Create the socket and connect
    print "Connecting to server $server ...";
    $f = fsockopen($server, 43, $errno, $errstr, 3);    //Open a new connection
    if (!$f) {
        print "Failed";
        return false;
    }
    print "Done" . PHP_EOL;
    // Set the timeout limit for read
    if (!stream_set_timeout($f, 3)) {
        die('Unable to set set_timeout');    #Did this solve the problem ?
    }
    // Send the IP to the whois server
    if ($f) {
        print "Sending request for ip: $ip" . PHP_EOL;
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
        print "Starting to read socket" . PHP_EOL;
        while (!feof($f)) {
            //print "Read attempt...".PHP_EOL;
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
        echo "Found matches:\n";
        $foundCountry = $matches[0];
        $countryCode = substr($foundCountry, -2);
        return $countryCode;
    } else {
        echo "No matches found";
    }

    // Now return the data
    return $data;
}

/**
 * @param $needle
 * @param $data
 * @return false|string
 */
function fisearch($needle, $data)
{
    // escape special characters in the query
    $pattern = preg_quote("country:", '/');

    // finalise the regular expression, matching the whole line
    $pattern = "/^.*$pattern.*\$/m";
//    $pattern_i = "/^.*$pattern.*\$/m/i";

    // search, and store all matching occurences in $matches
    if (preg_match_all($pattern, $data, $matches)) {
        echo "Found matches:\n";
        $foundCountry = implode("\n", $matches[0]);
        $countryCode = substr($foundCountry, -2);
        return $countryCode;
    } else {
        echo "No matches found";
    }
}
