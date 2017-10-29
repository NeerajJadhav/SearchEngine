<?php

/**
 * Created by PhpUser: niraj
 * Date: 16-03-17
 * Time: 12:32 AM
 */
include_once 'core/init.php';


class LocalSearch
{

    private $sanitizedQuery = '';
    private $useRelevanceFeedback = false;
    private $useStemming = false;
    private $indexName = "musiclibrary";
    private $stemmedIndex = "stemmusiclibrary";
    private $type = "lyricsdata";

    function __construct()
    {
        if(isset($_POST['r_feedback']))
            $this->useRelevanceFeedback = true;

        if($_POST['searchQuery'] == ""){
            header('Location:index.php');
            exit();
        }else{
            $this->sanitizedQuery = htmlentities( $_POST['searchQuery'], ENT_QUOTES, 'UTF-8');
            if(isset($_POST['stem'])) {
                $this->sanitizedQuery = $this->getStemmedString($this->sanitizedQuery);
                $this->useStemming = true;
            }
        }
    }

    function execute(){
        $oDatabase = new Database();
        if(!$this->useStemming)
            $result = $oDatabase->searchOnIndex($this->indexName,$this->type,$this->sanitizedQuery);
        else
            $result = $oDatabase->searchOnIndex($this->stemmedIndex,$this->type,$this->sanitizedQuery);
        $count = $result['document_count'];
        $documents = $result['documents'];
        $ranking = $this->useRelevanceFeedback ? true : false;

        $html = '<html><body><link rel="stylesheet" type="text/css" href="css/SearchCard.css">';
        $html .= '<h4>Total '.$result['occurrences'].' occurrences 
        in '.$count.' documents. Result took '.$result['time_taken'].' milliseconds.</h4>';
        $html .= $this->useRelevanceFeedback ?
            '<form method="post" action="ReRank.php"> <input type="submit" value="Re-Rank">':'';

        $html .= '<input type="hidden" name="searchQuery" value="'.htmlentities($this->sanitizedQuery).'">';

        if($this->useStemming){
            $index = $this->stemmedIndex;
            $html .= '<input type="hidden" name="stemming" value="on">';
        }else{
            $index = $this->indexName;
        }

        $oGetExcerpt = new GenerateExcerpt();

        foreach ($documents as $document){
            if(isset( $document['highlight']['Lyrics']))
                $description = implode('...',$document['highlight']['Lyrics']);
            else
                $description = $oGetExcerpt->excerpt($document['_source']['Lyrics'],$this->sanitizedQuery);

            if(isset($document['highlight']['Song']))
                $title = $document['highlight']['Song'][0];
            else
                $title = $oGetExcerpt->excerpt($document['_source']['Song'],$this->sanitizedQuery);

            if(isset($document['highlight']['Artist']))
                $crumb = $document['highlight']['Artist'][0];
            else
                $crumb = $oGetExcerpt->excerpt($document['_source']['Artist'],$this->sanitizedQuery);

            $url = "ShowDetails.php?id=".$document['_id']."&index=".$index.'&type='.$this->type;

            $card = new SearchCard($title,$description,$crumb,$url,$ranking,$document['_id']);
            $html .= $card->getHtmlCard();
        }
        $html .= '</input>';
        $html .= $ranking?'</form>':'';
        $html .= '</body></html>';
        echo $html;
    }


    function getStemmedString($string){

        $stem_arr = [];
        $str_arr = explode(' ',$string);
        foreach($str_arr as $awrd){
            $stem_arr[] = PorterStemmer::Stem($awrd);
        }

        return implode(' ',$stem_arr);

    }
}

$lSearch = new LocalSearch();
$lSearch->execute();