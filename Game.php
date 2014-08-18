<?php

require_once(dirname(__FILE__) . "/lib/simple_html_dom.php");

define('HAND_BLACK', 0);
define('HAND_WHITE', 1);
define('HAND_DROW', 2);
class Game {

    public $black_level;
    public $white_level;

    public $win;
    public $record;

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
        $this->_install_record($html);
        $this->_install_player_level($html);
//        var_dump($this->record);
    }

    /**
     * htmlから棋譜を取得し,セットする
     * @param simple_html_dom 棋譜ページのsimple_dom
     */
    private function _install_record($html) {
        // JSのため正規表現で棋譜コード文字列部分を抽出
        if (!preg_match('#receiveMove\("(?<reco>.*)"\)#U', $html, $m)) {
            throw new Exception('棋譜を取ってくることが出来ません');
        }
        $this->record = $this->_to_moves($m['reco']);
    }

    private function _install_player_level($html) {
        if (!preg_match_all('#dan.:\s"(?<dan>.*?)",#u', $html, $m, PREG_SET_ORDER)) {
            throw new Exception('プレイヤーの段位を取得できません');
        }
        $this->black_level = $this->_levelstr_to_num($m[0]['dan']);
        $this->white_level = $this->_levelstr_to_num($m[1]['dan']);
    }

    /**
     * 棋譜リスト文字列をMoveオブジェクトに変換
     * @param simple_html_dom 棋譜ページのsimple_dom
     * @return array Moveオブジェクトの配列
     */
    private function _to_moves($text) {
        // コードの文字列はタブ区切りになっているので分割
        $record = array();
        $codes = explode("\t", $text);
        $last_code = array_pop($codes);
        $lib = ['S' => HAND_BLACK, 'G' => HAND_WHITE, 'D' => HAND_DROW];
        $this->win = $lib[substr($last_code, 0, 1)];
        foreach ($codes as $code) {
            $record[] = new Move($code);
        }
        return $record;
    }

    /**
     * 段位文字列を数値化する
     */
    public static function _levelstr_to_num($str) {
        if (preg_match("#(?<n>\d+)級#u", $str, $m)) {
            return 31 - $m['n'];
        }
        preg_match("#(?<n>.)段#u", $str, $m);
        // 段位以上だった場合
        $char = $m['n'];
        $lib = ['初' => 1, '二' => 2, '三' => 3, '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9, '十'=> 10];
        return 31 + $lib[$char];
    }

}

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
