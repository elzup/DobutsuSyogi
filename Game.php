<?php

require_once(dirname(__FILE__) . "/lib/simple_html_dom.php");

define('HAND_BLACK', 0);
define('HAND_WHITE', 1);
define('HAND_DRAW', 2);
define('ANIMAL_KING', 0);
define('ANIMAL_CHICK', 1);
define('ANIMAL_GIRAFFE', 2);
define('ANIMAL_ELEPHANT', 3);
define('ANIMAL_CHICKEN', 4);

define('TYPE_ASTR_FULL', 0);
define('TYPE_ASTR_CHAR', 1);
define('TYPE_ASTR_KANJI', 2);
define('TYPE_ASTR_ENG', 3);

class Game {

    /**
     * 先手後手プレイヤーそれぞれのレベル
     */
    public $black_level;
    public $white_level;

    /**
     * どちらが勝ったか HAND_BLACK|HAND_WHITE|HAND_DRAW
     * @var integer
     */
    public $win;

    /**
     * 棋譜
     * @var Move[]
     */
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
        $this->_install_player_level($html);
        $this->_install_record($html);
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
        $this->black_level = Game::_levelstr_to_num($m[0]['dan']);
        $this->white_level = Game::_levelstr_to_num($m[1]['dan']);
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
        $lib = ['S' => HAND_BLACK, 'G' => HAND_WHITE, 'D' => HAND_DRAW];
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

    /**
     * それぞれの手(棋譜)に評価をつける
     */
    public function grading()
    {
        // プレイヤーのレベルと勝敗から評価する
        
    }

}

class Move {
    public $hand;
    public $from;
    public $to;
    public $animal;
    public $time;

    public static $ANIMAL_STR = [
            ANIMAL_KING     => ['らいおん', 'ら', '王', 'KING'],
            ANIMAL_CHICK    => ['ひよこ', 'ひ', '歩', 'CHICK'],
            ANIMAL_GIRAFFE  => ['きりん', 'き', '飛', 'GIRAFFE'],
            ANIMAL_ELEPHANT => ['ぞう', 'ぞ', '角', 'ELEPHANT'],
            ANIMAL_CHICKEN  => ['にわとり', 'に', '金', 'CHICKEN'],
        ];


    public function __construct($code) {
        if (!preg_match("#(?P<hand>[+-])(?<from>\d{2})(?<to>\d{2})(?<animal>.{2}),L(?<time>\d+)#", $code, $m)) {
            throw new Exception("コードのフォーマットが正しくないです");
        }
        $this->hand = ($m['hand'] == '+') ? HAND_BLACK : HAND_WHITE;
        $this->from = $m['from'];
        $this->to = $m['to'];
        $lib = ['OU' => ANIMAL_KING, 'FU' => ANIMAL_CHICK, 'HI' => ANIMAL_GIRAFFE, 'KA' => ANIMAL_ELEPHANT, 'TO' => ANIMAL_CHICKEN];
        $this->animal = $lib[$m['animal']];
        $this->time = $m['time'];
    }

    /**
     * 動物の文字列を漢字で返します
     * @return 変換した文字列
     */
    public function get_animal_str_kanji()
    {
        return $this->get_animal_str(TYPE_ASTR_KANJI);
    }

    /**
     * 動物の文字列を返します
     * @var $type 文字のタイプ
     * TYPE_ASTR_* で指定
     * TYPE_ASTR_CHAR(default) ひらがな一文字
     * TYPE_ASTR_FULL ひらがな
     * TYPE_ASTR_KANJI 漢字
     * TYPE_ASTR_ENG 英名
     * @return 変換した文字列
     */
    public function get_animal_str($type = TYPE_ASTR_CHAR)
    {
        return Move::$ANIMAL_STR[$this->animal][$type];
    }
}
