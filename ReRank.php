<?php
/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 23-04-17
 * Time: 01:43 AM
 */

include_once "core/init.php";

class ReRank{

    private $stem = false;
    private $query = false;
    private $docIDs = [];
    private $indexName = '';
    private $typeName = 'lyricsdata';
    private $relevantDoc = '';
    private $documents;

    function __construct()
    {
        if(isset($_POST['stemming'])){
            $this->stem = true;
        }
        if(isset($_POST['searchQuery'])){
            $this->query = $_POST['searchQuery'];
        }
    }

    function execute(){
        $this->getDocIDs();
        if($this->stem){
            $this->indexName = 'stemmusiclibrary';
        }else{
            $this->indexName = 'musiclibrary';
        }
        if(empty($this->docIDs)){
            echo "<script>alert('NO DOCUMENT SELECTED');history.go(-1);</script>";
        }
        $this->generateRelevantDocument();

        //get all words from relevant documents
        $relevantDocTerms = array_unique(explode(' ',$this->relevantDoc));
        $relevantDocTerms = array_diff($relevantDocTerms,array(""));
        $documentTFIDF=[];

        //we want to calculate all normalized tf for each term in relevant document
        foreach ($relevantDocTerms as $eachTerm) {
            $feedBackDocTFIDF[$eachTerm] = substr_count($this->relevantDoc, $eachTerm) / str_word_count($this->relevantDoc);
        }

        // gets ||relevant|| part of formula: cosine = (document . relevantDocument / ||document|| ||relevantDoc||)
        $feedBackSquareRoot = abs($this->getDocumentVectorDenominator($feedBackDocTFIDF));

        //calculate tf for each word present in relevant which is present in each search result
        foreach ($this->docIDs as $docID=>$userRelevance){
            $fullDocString = $this->getFullDocumentString($docID);
            $docWordCount = str_word_count($fullDocString);
            foreach ($relevantDocTerms as $eachTerm){
                $documentTFIDF[$docID][$eachTerm] = substr_count($fullDocString,$eachTerm)/$docWordCount;//get normalized tf
            }

            $cosineSimilarity[$docID] = $this->getCosineSimilarity($documentTFIDF[$docID], $feedBackDocTFIDF, $feedBackSquareRoot);

        }
        arsort($cosineSimilarity);

        $this->generateResult($cosineSimilarity);
    }

    /** Gets cosine similarity between a document and relevanceFeedback document
     *
     * @param $documentTerms array: of all terms in a document and their tf
     * @param $relevantDocTerms array: of all terms in second document and their tf
     * @param $feedBackSquareRoot float: Provide pre-calculated second document denominator vector
     * @return float|int returns cosine of 2 vectors.
     */
    function getCosineSimilarity($documentTerms,$relevantDocTerms,$feedBackSquareRoot){

        return $this->getDocumentsDotProduct($documentTerms,$relevantDocTerms)/
            (abs($this->getDocumentVectorDenominator($documentTerms))*$feedBackSquareRoot);
    }

    /**
     * @param $documentTerms
     * @param $relevantDocTerms
     * @return int
     */
    function getDocumentsDotProduct($documentTerms,$relevantDocTerms){
        $totalFrequency =0;

        foreach($documentTerms as $terms=>$normalizoedFreq){
            $totalFrequency += $normalizoedFreq * $relevantDocTerms[$terms];
        }
        return $totalFrequency;
    }

    /**
     * @param $documentTerms
     * @return float
     */
    function getDocumentVectorDenominator($documentTerms){

        $totalFrequency =0;

        foreach($documentTerms as $terms=>$normalizedFreq){
            $totalFrequency += pow($normalizedFreq,2);
        }
        return sqrt($totalFrequency);
    }

    /**
     *
     * @param $docs
     */
    function generateResult($docs){

        $html = '<html><body><link rel="stylesheet" type="text/css" href="css/SearchCard.css">';
        $oGetExcerpt = new GenerateExcerpt();
        $db = new Database();
        foreach ($docs as $id=>$cos){
            $doc = $db->get(array('index' => $this->indexName,
                'type' => $this->typeName,
                'id' => $id,));
            $description = $oGetExcerpt->excerpt($doc['_source']['Lyrics'],$this->query);
            $title = $oGetExcerpt->excerpt($doc['_source']['Song'],$this->query);
            $crumb = $oGetExcerpt->excerpt($doc['_source']['Artist'],$this->query);

            $url = "ShowDetails.php?id=".$doc['_id']."&index=".$this->indexName.'&type='.$this->typeName;

            $card = new SearchCard($title,$description,$crumb,$url,false,$doc['_id'],$cos);
            $html .= $card->getHtmlCard();
        }
        $html .= '</body></html>';
        echo $html;
    }

    /**
     * @return array
     */
    function getTotalTermFrequency(){
        $freq = [];
        $queryArr = explode(' ',$this->query);
        foreach ($queryArr as $word){
            $count = 0;
            foreach ($this->documents as $document) {
                $string = $this->getFullDocumentString($document['_id']);
                 $count += substr_count($string,$word);
            }
            $freq[$word] = $count;
        }

        return $freq;
    }

    /**
     *
     *
     */
    function generateRelevantDocument()
    {
        $db = new Database();
        $this->documents = $db->searchOnIndex($this->indexName, $this->typeName,$this->query )['documents'];

        foreach ($this->documents as $document) {
            if(in_array($document['_id'],array_keys($this->docIDs))){
                $this->relevantDoc .= ' '.$document['_source']['Lyrics'];
                $this->relevantDoc .= ' '.$document['_source']['Song'];
                $this->relevantDoc .= ' '.$document['_source']['Artist'];
            }
        }

    }

    /**
     *
     */
    function getDocIDs(){
        foreach ($_POST as $key=>$value){
            if(strstr($key,'doc_')){
                $docid = str_replace('doc_','',$key);
                $this->docIDs[$docid] = $_POST[$key];
            }
        }
    }

    /**
     *
     *
     */
    function getFullDocumentString($id){

        $params = [
            'index' => $this->indexName,
            'type' => $this->typeName,
            'id' => $id,
        ];
        $db = new Database();
        $doc = $db->get($params);
        return implode(' ',array($doc['_source']['Song'],$doc['_source']['Artist'],$doc['_source']['Lyrics']));
    }

}

$obj = new ReRank();
$obj->execute();