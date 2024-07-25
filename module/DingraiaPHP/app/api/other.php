<?php
if ($bot_run_as) {
    if ($_GET["type"] == "yiyan") {
        $bot_run_as['config']['index_not_load'] = 1;
        
        $jsonData = file_get_contents('asset/apiAsset/sentences/version.json');
        $data = json_decode($jsonData, true);
        $sentences = $data['sentences'];
        $randomSentence = $sentences[array_rand($sentences)];
        $jsonPath = $randomSentence['path'];
        $jsonData = file_get_contents("asset/apiAsset".ltrim($jsonPath, "."));
        $jsonArray = json_decode($jsonData, true);
        $randomHitokoto = $jsonArray[array_rand($jsonArray)];
        
        $apiResponse["result"] = $randomHitokoto;
        $apiResponse["code"] = 0;
    }
}