<?php

require_once('./get_game_urls.php');
require_once('./get_players.php');

$users = get_players('http://shogiwars.heroz.jp/a/events/zou2');

$from = 101;
$to = 299;

echo implode(',', get_game_urls($users[$from]));
for ($i = $from + 1; $i < $to + 1; $i++) {
    echo ',';
    echo implode(',', get_game_urls($users[$i]));
}
