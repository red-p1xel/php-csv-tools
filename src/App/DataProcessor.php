<?php

namespace App;

use App\Statistic\Item;
use App\Statistic\StatsList;
use libphonenumber\PhoneNumberUtil;
use Storage\CSV\Collection;
use Storage\CSV\Reader;
use App\Call;

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


        $stats = new StatsList();
        foreach ($objects as $object => $val) {
            $customerId = $object;
            $callsList = $val;

            $totalDuration = $collection->totalCallsDurationsBy($callsList);

            $item = new Item();
            $item->customerId = $customerId;
            $item->totalDurationOfAllCustomersCalls = $totalDuration;
            $item->totalNumberOfAllCustomersCalls = $totalCalls[$customerId];

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


}
