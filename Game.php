<?php

require_once(dirname(__FILE__) . "/lib/simple_html_dom.php");

class Game {

    public $black_level;
    public $white_level;

    public $win;
    public $record;

    public $win;

    public function __construct($url)
    {
        $this->_initialize($url);
    }

    private function _initialize($url)
    {
        $html = @file_get_html($url);
        if (empty($html)) {
            throw new Exception('urlが正しくない可能性があります');
        }
        $this->record = $this->_get_record($html);
//        var_dump($this->record);
    }

    /**
     * htmlから棋譜を取得する
     * @param simple_html_dom 棋譜ページのsimple_dom
     * @return array Moveオブジェクトの配列
     */
    private function _get_record($html) {
        // JSのため正規表現で棋譜コード文字列部分を抽出
        if (!preg_match('#receiveMove\("(?<reco>.*)"\)#U', $html, $m)) {
            throw new Exception('棋譜を取ってくることが出来ません');
        }
        return $this->_to_moves($m['reco']);
    }

    /**
     * 棋譜リスト文字列をMoveオブジェクトに変換
     * @param simple_html_dom 棋譜ページのsimple_dom
     * @return array Moveオブジェクトの配列
     */
    private function _to_moves($text) {
        // コードの文字列はタブ区切りになっているので分割
        $record = array();
        foreach (explode("\t", $text) as $code) {
            try {
                $record[] = new Move($code);
            } catch (Exception $e) {
                echo $code;
            }
        }
        return $record;
    }
}

define('HAND_BLACK', 0);
define('HAND_WHITE', 1);
class Move {
    public $hand;
    public $from;
    public $to;
    public $animal;
    public $time;

    public function __construct($code) {
        if (!preg_match("#(?P<hand>[+-])(?<from>\d{2})(?<to>\d{2})(?<animal>.{2}),L(?<time>\d+)#", $code, $m)) {
            throw new Exception("コードのフォーマットが正しくないです");
        }
        $this->hand = ($m['hand'] == '+') ? HAND_BLACK : HAND_WHITE;
        $this->from = $m['from'];
        $this->to = $m['to'];
        $this->animal = $m['animal'];
        $this->time = $m['time'];
    }
}
