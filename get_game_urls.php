<?php
define('REPLACE_NUM', '{NUM}');
define('REPLACE_PLAYER', '{PLAYER}');
define('GAME_PAR_PAGE', 10);
define('URL_USER_HISTORY_PAGE', 'http://shogiwars.heroz.jp/a/events/zou2' . REPLACE_PLAYER . '?gtype=a&start=' . REPLACE_NUM);
// http://shogiwars.heroz.jp/users/history/elzup?gtype=a&start=1

require_once(dirname(__FILE__) . "/../lib/simple_html_dom.php");

echo implode(',', get_game_urls('elzup'));

/**
 * プレイヤー名から,そのプレイヤーへのリンクリストを取得する
 *
 * @param string $player_name 取得するプレイヤー名
 * @param integer $max ゲームリンク取得数の上限
 * @return array リンク(URL)の配列
 */
function get_game_urls($player_name, $max = 100) {
    $urls = array();
    for ($i = 1; $i < $max; $i += GAME_PAR_PAGE) {
        $call_url = str_replace([REPLACE_NUM, REPLACE_PLAYER], [$i, $player_name], URL_USER_HISTORY_PAGE);
        $html = @file_get_html($call_url);
        if (empty($html)) {
            throw new Exception('ユーザ名が正しくない可能性があります');
        }
        $divs = $html->find('.short_btn1');
        if (!$divs) {
            break;
        }
        foreach ($divs as $div) {
            if ($as = $div->find('a')) {
                $urls[] = $as[0]->href;
            }
        }
    }
    return $urls;
}
