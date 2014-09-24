<?php

require_once('./get_game_urls.php');
require_once('./get_players.php');

$users = get_players('http://shogiwars.heroz.jp/a/events/zou2');
echo implode(',', get_game_urls(array_shift($users)));
foreach ($users as $i => $user) {
    if ($i > 100) {
        break;
    }
    echo ',';
    echo implode(',', get_game_urls($user));
}
