<?php


define('REPLACE_NUM', '{NUM}');
define('PAGE_PAR_PERSON', 25);
define('URL_CONFERENCE', 'http://shogiwars.heroz.jp/a/events/hiyoko2?rank_criteria=max_rating&start=' . REPLACE_NUM . '&user=byeordie');

require_once('./lib/simple_html_dom.php');

$names = array();
for ($i = 1; $i <= 1000; $i += PAGE_PAR_PERSON) {
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

echo implode(',', $names);
