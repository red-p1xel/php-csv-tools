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

    private static function csv(string $filePath)
    {
        return new Reader($filePath);
    }

    // ### DATA PROCESSING HANDLER ###

    /**
     * @param string $filePath
     * @return array[]
     * @throws \Exception
     */
    public static function handle(string $filePath): array
    {
        // array(['customerId', 'createdAt', 'duration', 'phone', 'ip']);

        try {
            $csv = self::csv($filePath);
            $rows = $csv->all();
        } catch (\Exception $e) {
            throw new \Exception('Oops! Something went wrong :(');
        }

        // TODO: This place ready to call methods for implement required statistic data from provided rows in this CSV.

        // $csv->sort_by = 'customerId';
        // $csv->seek(5)

        self::dd($rows, "Rows");

        return [
            'data' => [
                'csv' => $csv,
                'success' => true,
                'message' => 'CSV file was processed without caused errors.',
            ],
        ];
    }
}
