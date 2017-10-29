<?php
/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 16-03-17
 * Time: 12:32 AM
 */

require_once "core/init.php";

?>
<html>
<title>Assignment 3</title>
<link rel="stylesheet" href="css/init.css">


<body>
<div id="logoDiv">
    <img id="logo" src="resources/bing-logo-with-google-colors-question-mark.jpg" >
</div>
<!--<script>-->
<!--    (function() {-->
<!--        var cx = '006240103926255976195:7pmx5lnjd2o';-->
<!--        var gcse = document.createElement('script');-->
<!--        gcse.type = 'text/javascript';-->
<!--        gcse.async = true;-->
<!--        gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;-->
<!--        var s = document.getElementsByTagName('script')[0];-->
<!--        s.parentNode.insertBefore(gcse, s);-->
<!--    })();-->
<!--</script>-->
<!--<gcse:search></gcse:search>-->

<div id="localDiv">
    <form method="post" action="LocalSearch.php">
        <input type="text" name="searchQuery" id="searchBox" placeholder="Local Search">
        <input type="submit" value="Search" id="localSearchBtn" >
        <br/>
        <br/>
        <br/>
        <input type="checkbox" id="stemmer" name="stem" >Porter's Stemming
        <br/>
        <br/>
        <input type="checkbox" id="relevanceFeedback" name="r_feedback" onclick="activateStemmer()">Relevance Feedback

    </form>
</div>

<script type="application/javascript">

    function activateStemmer(){
        var portStem = document.getElementById("stemmer");
        var relFeed = document.getElementById("relevanceFeedback");
        if(!portStem.checked && relFeed.checked)
            portStem.checked  = true;
        else
            portStem.checked = false;
    }


</script>
</body>
</html>