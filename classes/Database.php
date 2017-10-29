<?php

/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 19-03-17
 * Time: 06:14 PM
 */

require_once __DIR__ . '/../core/init.php';

class Database
{
    private $_client;

    public function __construct()
    {
        $this->createClient();
    }

    private function createClient()
    {

        $this->_client = Elasticsearch\ClientBuilder::create()->allowBadJSONSerialization()->build();
    }

    public function getSearch($index, $type, $searchType, $fieldName, $queryString)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                'query' => [
                    $searchType => [
                        $fieldName => $queryString
                    ]
                ]
                ]
            ];

        $aRawResult = $this->_client->search($params);

        $result = [
            'totalHits' => $aRawResult['hits']['total'],
            'hits' => $aRawResult['hits']['hits']
        ];

        return $result;
    }

    public function searchOnIndex($index, $type, $queryString)
    {

        if ($index == '' || $type == '') {
            throw new Exception('Index and Type missing');
        }

        if (!$this->_client->indices()->exists(array('index' => $index))) {
            throw new Exception('Index not found');
        }

        if ($queryString == '') {
            return null;
        }

/*
        $fieldNames = $this->getFieldNames($index, $type);

        $splitString = [];
        if (strstr($queryString, ',')) {
            $splitString = $this->split($queryString, ',');
        }

        if (strstr($queryString, '\s')) {
            $splitString = $this->split($queryString, '\s');
        }

        $splitString = $this->getWildcardStringArray($splitString);*/

        $params = [
            'index' => $index,
            'type' => $type,
            "body" => [
                "query" => [
                    "match" => [
                        "_all" => [
                            "query" => $queryString,
                            "operator" => "OR"
                        ]
                    ]
                ],
                'highlight' =>[
                    "fields"=> [
                        "*"=> [ "require_field_match" => false]
                    ]
                ]
            ]
        ];

        $aSearchResult = $this->_client->search($params);

        $aRequiredResult['time_taken'] = (int)$aSearchResult['took'];
        $aRequiredResult['occurrences'] = $aSearchResult['hits']['total'];
        $aRequiredResult['documents'] = $aSearchResult['hits']['hits'];
        $aRequiredResult['document_count'] = count($aSearchResult['hits']['hits']);

        return $aRequiredResult;
    }

    public function get($params){
        return $this->_client->get($params);
    }

  /*  public function getFieldNames($index, $type)
    {
        $aResult = $this->_client->indices()->getMapping(array('index' => $index, 'type' => $type));

        return array_keys($aResult[$index]['mappings'][$type]['properties']);
    }

    public function split($string, $delimiter)
    {
        return explode($string, $delimiter);
    }

    public function getWildcardStringArray($queryString)
    {
        foreach ($queryString as $index => $string) {
            $queryString[$index] = '*' . $string . '*';
        }
        return $queryString;
    }*/

  /*
   *         "fields" : {
            "Lyrics" : {"type":"plain"},
            "Artist" : {"type":"plain"},
            "Song" : {"type":"plain"}
        }*/


}