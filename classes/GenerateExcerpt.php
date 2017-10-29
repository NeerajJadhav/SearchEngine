<?php

/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 16-04-17
 * Time: 03:51 PM
 */
class GenerateExcerpt
{
    function excerpt($text, $phrase, $radius = 100, $ending = "...") {
        $phraseLen = strlen($phrase);
        if ($radius < $phraseLen) {
            $radius = $phraseLen;
        }

        $pos = strpos(strtolower($text), strtolower($phrase));

        $startPos = 0;
        if ($pos > $radius) {
            $startPos = $pos - $radius;
        }

        $textLen = strlen($text);

        $endPos = $pos + $phraseLen + $radius;
        if ($endPos >= $textLen) {
            $endPos = $textLen;
        }

        $excerpt = substr($text, $startPos, $endPos - $startPos);
        if ($startPos != 0) {
            $excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
        }

        if ($endPos != $textLen) {
            $excerpt = substr_replace($excerpt, $ending, -$phraseLen);
        }

        $phraseArr = '';
        if(strstr($phrase,','))
            $phraseArr = explode(',',$phrase);
        elseif (strstr($phrase,' '))
            $phraseArr = explode(' ',$phrase);

        if(!empty($phraseArr)) {
            foreach ($phraseArr as $eachKey) {
                $excerpt = str_replace($eachKey, " <strong>" . $eachKey . "</strong> ", $excerpt);
            }
        }else{
            $excerpt = str_replace($phrase, " <strong>".$phrase."</strong> ", $excerpt);
        }

        return $excerpt;
    }
}