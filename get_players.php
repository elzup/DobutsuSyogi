<?php
define('REPLACE_NUM', '{NUM}');
define('PERSON_PAR_PAGE', 25);
define('URL_CONFERENCE', 'http://shogiwars.heroz.jp/a/events/hiyoko2?rank_criteria=max_rating&start=' . REPLACE_NUM . '&user=byeordie');

require_once('./lib/simple_html_dom.php');

//echo implode(',', $names);
/**
 * 大会の上位にランクインしているプレイヤー名を取得する
 *
 * @param string $url_conference コンテストページのURLフォーマット
 * 表示する順位のはじめの値に置き換える位置にREPLACE_NUMの接続して定義しておく
 * 'http://shogiwars.heroz.jp/a/events/hiyoko2?rank_criteria=max_rating&start=' . REPLACE_NUM . '&user=byeordie'
 * @param integer $max プレイヤー取得数の上限
 * @return array プレイヤー名の配列
 */
function get_players($url_conference, $max = 1000) {
    $names = array();
    for ($i = 1; $i <= $max; $i += PERSON_PAR_PAGE) {
        $call_url = str_replace(REPLACE_NUM, $i, URL_CONFERENCE);
        $html = file_get_html($call_url);


        if ($table = $html->firstChild('.stripe_ranking')) {
            foreach ($table->find('tr') as $tr) {
                if ($as = $tr->find('a')) {
                    $href = $as[0]->href;
                    if (preg_match("#^.*/(?<name>.*)$#", $href, $m)) {
                        $names[] = $m['name'];
                    }
                }
            }
        }
    }
    return $names;
}
