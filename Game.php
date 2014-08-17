<?php

require_once(dirname(__FILE__) . "/lib/simple_html_dom.php");

class Game {

    public $black_level;
    public $white_level;

    public $records;

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
        $this->records = $this->_get_records($html);
    }

    /**
     * htmlから棋譜を取得する
     * @param simple_html_dom 棋譜ページのsimple_dom
     * @return array Moveオブジェクトの配列
     */
    private function _get_records($html) {
        if (!preg_match('#receiveMove\((?<reco>.*)\)#U', $html, $m)) {
            throw new Exception('棋譜を取ってくることが出来ません');
        }
        return $this->_get_records($m['reco']);
    }

    /**
     * 棋譜リスト文字列をMoveオブジェクトに変換
     * @param simple_html_dom 棋譜ページのsimple_dom
     * @return array Moveオブジェクトの配列
     */
    private function _to_moves($text) {
        return ;
    }
}

class Move {
    public $animal;
    public $from;
    public $to;

    public function __construct($code) {
    }
}
