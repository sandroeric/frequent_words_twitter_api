<?php

$posts = 2000;
set_time_limit(120);
    require_once "vendor/autoload.php";
    
    $loops = (int) $posts/100;
    
    //credentials for twitter api
    $settings = array(
        'oauth_access_token' => "",
        'oauth_access_token_secret' => "",
        'consumer_key' => "",
        'consumer_secret' => ""
    );

    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $requestMethod = 'GET';
    $max_id = '';
    $tweets = array();
    do {
        $getfield = '?q='.$_POST['search'].'&result_type=recent&count=100'.$max_id;
        $twitter = new TwitterAPIExchange($settings);
        $response = $twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest();
        
        $tweets_temp = json_decode($response);
        $count = count($tweets_temp->statuses);
        $tweets = array_merge($tweets, $tweets_temp->statuses); 
        
        if ($count < 100) {
            break;
        }
        $loops--;
        $max_id = '&max_id='.$tweets_temp->statuses[$count-1]->id_str;
    } while ($loops > 0);
    
    $text = '';

    foreach($tweets as $tweet) {
        $text .= $tweet->text;
    }
    $ranking = array_count_values(explode(' ', $text));
    $data = [];
    foreach($ranking as $word => $quantity) {
        $data[] = [ $word, $quantity ];
    }
    echo json_encode($data);
?>