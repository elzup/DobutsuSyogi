<?php
require_once('./Game.php');
require_once("./model.php");
require_once('./keys.php');
define('BASE_URL', 'http://localhost/dobutsu_syogi/');

try {
    $dm = new Dobutushogi_model(new PDO(DB_DSN, DB_USER, DB_PASS));
} catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$map_str = @$_GET['m'] ?: '75806002041399:0';
$flip = @$_GET['f'] ?: '0';
$flip_url = get_flip_url($flip);

list($map_str, $hand) = explode(':', $map_str);
$map = Game::str_to_map($map_str);
$moves = $dm->select_moves($map_str, $hand);
$moves = Game::install_moves($moves, $map);
foreach ($moves as $move) {
    echo $move->get_str();
    echo ',';
}

function get_flip_url($flip) {
    preg_match('#(?<base>.*[&?]f=)(.*)#', $_SERVER["REQUEST_URI"], $m);
    if ($m) {
        $url = $m['base'] . (1 ^ $flip);
    } else {
        $deli = (strpos($_SERVER["REQUEST_URI"], '?') === FALSE ? '?' : '&');
        $url = $_SERVER["REQUEST_URI"] . $deli . 'f=' . (1 ^ $flip);
    }
    return $url;
}

function print_map_table($map, $hand = 1, Move $move = NULL) {
    global $flip;
    $hand_head = '◯' . ($hand ? '★' : '');
    $hand_foot = (!$hand ? '★' : '') . '●';
    if (!$move) {
        echo '<span>元盤面</span>';
    } else {
        echo '<span>' . $move->point . 'pt</span>';
        $map = Game::next_map($map, $move);
        echo '<a href="' . BASE_URL . '?m=' . Game::map_to_str($map) . ':' . (1 ^ $hand) . '">選択</a>';
        $move_str = $move->get_str();
    }
    $class = $flip ? 'flip' : '';
    echo '<table class="' . $class .'">';

    if (!$move) {
        echo '<tr><td>' . $hand_head . '</td></td>';
    }
    for ($j = 0; $j < 4; $j++) {
        echo '<tr>';
        for ($i = 0; $i < 3; $i++) {
            $hand = HAND_BLACK;
            $a = $map[$j][$i];
            if ($a > 4) {
                $hand = HAND_WHITE;
                $a = -($a - HAND_SHIFT);
            }
            if ($a == 0) {
                $hand = 3;
            }
            echo '<td class="math hand-' . $hand . ' animal-' . Move::to_animal_str($a, TYPE_ASTR_ENG) . '">';
            echo '<div class="a-char">' . Move::to_animal_str($a) . '</div>';
            echo '</td>' . PHP_EOL;
        }
        echo '</tr>' . PHP_EOL;
    }
    echo '<tr><td colspan="3">' . Game::map_to_str($map) . '</td></tr>';
    if (!$move) {
        echo '<tr><td colspan="2"></td><td>' . $hand_foot . '</td></td>';
    } else {
        echo '<tr><td colspan="3">' . $move_str . '</td></tr>';
    }
    echo '</table>';
}

?>


<meta charset="utf-8" />
<link rel="stylesheet" href="./style/main.css">

<p>
    <a href="<?= BASE_URL ?>">最初から</a>
    <a href="<?= $flip_url ?>">反転</a>
</p>
<?php 
print_map_table($map, $hand);

echo '<span>候補</span>';
echo '<div class="list">';
foreach ($moves as $m) {
    echo '<div class="item">';
    print_map_table($map, $hand, $m);
    echo '</div>';
}
echo '</div>';

