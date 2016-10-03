<?php
/**
 * Created by PhpStorm.
 * User: Szvitek
 * Date: 2016. 10. 03.
 * Time: 1:56
 */

namespace AppBundle\Services;


class CSVManager
{
    private $dataSource;

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function read()
    {
        //$source = $this-> getParameter('data_source');
        if (($file = fopen($this->dataSource, "r")) !== FALSE) {

            $headers = fgetcsv($file,1000,';');
            $movies = array();

            while( $row = fgetcsv($file,1000, ';') )
            {
                $movies[$row[0]] = array_combine($headers,$row);
            }

            fclose($file);
        }

        return $movies;
    }
}