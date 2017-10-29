<?php

/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 19-03-17
 * Time: 06:14 PM
 */
class SearchCard
{
    private $sTitle = '';
    private $sDescription = '';
    private $sCrumb = '';
    private $sUrl = '';
    private $bNeedsRating = false;
    private $sHtml = '';
    private $sID = '';
    private $cosine = '';

    /**
     * SearchCard constructor.
     * @param string $sTitle
     * @param string $sDescription
     * @param string $sCrumb
     * @param string $sUrl
     * @param bool $bNeedsRating
     * @param $docID
     * @param $cosine
     * @internal param string $sHtml
     *
     */
    public function __construct($sTitle, $sDescription, $sCrumb, $sUrl, $bNeedsRating, $docID, $cosine = NULL)
    {
        $this->sTitle = $sTitle;
        $this->sDescription = $sDescription;
        $this->sCrumb = $sCrumb;
        $this->sUrl = $sUrl;
        $this->bNeedsRating = $bNeedsRating;
        $this->sID = $docID;
        $this->cosine = $cosine;
    }

    function setDetails($title,$description,$crumb,$Url,$rating){
        $this->sTitle = $title;
        $this->sCrumb = $crumb;
        $this->sUrl = $Url;
        $this->sDescription = $description;
        $this->bNeedsRating = $rating;
    }

    function getHtmlCard()
    {
        if ($this->sTitle == '' && $this->sDescription == '') {
            echo 'Details Not Set';
            die();
        }
        $rating='';
        if($this->bNeedsRating){
            $rating = "Is Relevant? <input type='checkbox' id='rank' name='doc_".$this->sID."'>";
        }

        $cosineString = $this->cosine ? "<span class='extra'>Cosine: ".round($this->cosine,3)."</span>" : '';
        $this->sHtml =  "<div class='container' >"
                                ."<p name='title' class='clickable' onclick='window.location=\"$this->sUrl\"'>"
                                   ."<span class='name'>".$this->sTitle."</span>"
                                   .$this->sCrumb.$cosineString.
                                "</p>"
                                ."<p>".$this->sDescription."</p>"
                                ."<p>".$rating."</p>"
                            ."</div>";
        return $this->sHtml;

    }


}