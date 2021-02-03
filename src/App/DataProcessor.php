<?php

namespace App;

include __ROOT_DIR__.'whois.php';

use App\Statistic\Item;
use App\Statistic\StatsList;
use libphonenumber\PhoneNumberUtil;
use Storage\CSV\Reader;

class DataProcessor
{
    protected static $csv;
    protected static $first;

    // ### UTILITIES ####
    private static function dd($data, string $desc = "Print data")
    {
        print "<br><br>###[" . $desc . "]###<br>";
        print "<br><pre>";
        print_r($data);
        print "</pre><br>###[ END ]###<br>";
    }

    private static function getRegionCodeForIp(string $ip)
    {
        return getIPRegionCode($ip);
    }

    private static function phoneNumberUtil()
    {
        return PhoneNumberUtil::getInstance();
    }

    public static function testGetRegion(string $phoneNumber)
    {
        $util =  self::phoneNumberUtil();
        $phoneNumberObject = $util->parse('+'.$phoneNumber, 'UA');

        return $util->getRegionCodeForNumber($phoneNumberObject);
    }

    private static function checking(int $phoneNumber, string $ip)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneNumberObject = $phoneUtil->parse('+'.$phoneNumber, 'UA');

        $ipRegionCode = self::getRegionCodeForIp($ip);
        $phoneRegionCode = $phoneUtil->getRegionCodeForNumber($phoneNumberObject);


        return [
            'phone' => $phoneRegionCode,
            'ip' => $ipRegionCode
        ];
    }

    // ### DATA PROCESSING HANDLER ###

    /**
     * @param string $filePath
     * @return array[]
     * @throws \Exception
     */
    public static function handle(string $filePath): array
    {
//        $ipUtils = self::getIpUtils();
//        $phoneUtils = self::phoneNumberUtil();

        try {
            $objects = Reader::getInstance($filePath)
                ->toObject(['customerId', 'createdAt', 'duration', 'phone', 'ip'])
                ->filter(function ($line, $lineNumber) {
                    $line->duration = intval($line->duration);
                    return $lineNumber >= 1 && $line->duration > 0;
                })
                ->groupBy(function ($line) {
                    return $line->customerId;
                })
                ->parse();
        } catch (\Exception $e) {
            throw new \Exception('Oops! Something went wrong :(');
        }

        // TODO: This place ready to call methods for implement required statistic data from provided rows in this CSV.

        $collection = new CallList();
        $totalCalls = [];
        foreach ($objects as $object => $values) {
            foreach ($values as $item) {
                $totalCalls[$item->customerId] = count($values);
                // Create new Call object
                $collection->add(new Call(
                    $item->customerId,
                    $item->createdAt,
                    $item->duration,
                    $item->phone,
                    $item->ip
                ));
            }
        }

//        $test = self::regionEqualityProcess($collection);

        $stats = new StatsList();
        foreach ($objects as $object => $val) {
            $customerId = $object;
            $callsList = $val;


            $totalDuration = $collection->totalCallsDurationsBy($callsList);

            $item = new Item();
            $item->customerId = $customerId;
            $item->totalDurationOfAllCustomersCalls = $totalDuration;
            $item->totalNumberOfAllCustomersCalls = $totalCalls[$customerId];

            $proccess = self::regionEqualityProcess($callsList);

            $stats->add($item);
        }

        self::dd($stats, 'CUSTOMERS CALLS STATS:');

        return [
            'data' => [
                'csv' => $objects,
                'success' => true,
                'message' => 'CSV file was processed without caused errors.',
            ],
        ];
    }

    /**
     * Execute process of checking equality region codes
     * for ip addresses and phone numbers
     *
     * @param array $calls
     * @return array
     */
    private static function regionEqualityProcess(array $calls)
    {
        // TODO: Create array of stacked statistic data
        $hasEqualRegions = [];

        // Getting `customerId`, `ip` and `phoneNumber` for call on current iteration
        foreach ($calls as $item => $call) {
            $customerId = $call->customerId;
            $duration = $call->duration;

            $ip = $call->ip;
            $phoneNumber = $call->phone;

            $chkResult = self::checking($phoneNumber, $ip);
        }

        return [];
    }
}
