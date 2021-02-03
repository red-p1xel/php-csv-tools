<?php

namespace App;

use libphonenumber\PhoneNumberUtil;
use Storage\CSV\Reader;

class DataProcessor
{
    protected static $csv;
    protected static $first;

    // ### UTILITIES ####
    private static function dd($data, string $desc = "Print data")
    {
        print "<br>###[" . $desc . "]###<br>";
        print "<br><pre>";
        print_r($data);
        print "</pre><br>###[ END ]###<br>";
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


    // ### DATA PROCESSING HANDLER ###

    /**
     * @param string $filePath
     * @return array[]
     * @throws \Exception
     */
    public static function handle(string $filePath): array
    {
        try {
            $objects = Reader::getInstance($filePath)
                ->toObject(['customerId', 'createdAt', 'duration', 'phone', 'ip'])
                ->groupBy(function ($line) {
                    return $line->customerId;
                })
                ->parse();
        } catch (\Exception $e) {
            throw new \Exception('Oops! Something went wrong :(');
        }

        // TODO: This place ready to call methods for implement required statistic data from provided rows in this CSV.

        self::dd($objects, "Rows");


        return [
            'data' => [
                'csv' => $objects,
                'success' => true,
                'message' => 'CSV file was processed without caused errors.',
            ],
        ];
    }
}
