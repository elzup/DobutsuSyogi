<?php
require_once('./Game.php');
require_once("./model.php");
require_once('./keys.php');
define('BASE_URL', 'http://localhost/dobutsu_syogi/');

try{
    $dm = new Dobutushogi_model(new PDO(DB_DSN, DB_USER, DB_PASS));
} catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$map_str = @$_GET['m'] ?: '75806002041399:0';
list($map_str, $hand) = explode(':', $map_str);
$map = Game::str_to_map($map_str);
$moves = $dm->select_moves($map_str);
$moves = Game::install_moves($moves, $map, $hand);

function print_map_table($map, $hand = 1, Move $move = NULL) {
    if (!$move) {
        echo '<span>元盤面</span>';
    } else {
        echo '<span>' . $move->point . 'pt</span>';
        $map = Game::next_map($map, $move);
        echo '<a href="' . BASE_URL . '?m=' . Game::map_to_str($map) . ':' . (1 ^ $hand) . '">選択</a>';
    }
    echo '<table>';
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
            echo Move::to_animal_str($a);
            echo '</td>' . PHP_EOL;
        }
        echo '</tr>' . PHP_EOL;
    }
    echo '</table>';
}

?>


<meta charset="utf-8" />
<style type="text/css">
.math {
    width: 50px;
    height: 50px;
    border: black solid 2px;
    text-align: center;
    border-radius: 1em 1em 0 0;
    font-weight: bold;
}
.hand-3 {
    border: solid gray 1px;
    border-radius: 0;
}

.hand-0 {
    background: blue;
}

.hand-1 {
    background: green;
    transform: rotateX(180deg);
}

.animal-k {
    background: #ffcccc;
    color: orange;
}
.animal-c {
    background: #ffffaa;
}
.animal-g {
    background: #dbd;
    color: yellow;
}
.animal-e {
    background: #dbd;
    color: gray;
}
.animal-h {
    background: #ffffaa;
}

.list {
    overflow: auto;
}
.list > div {
    float: left;
}
</style>

    <a href="<?= BASE_URL ?>">最初から</a>
<?php 
print_map_table($map);

echo '<span>候補</span>';
echo '<div class="list">';
foreach ($moves as $m) {
    echo '<div class="item">';
    print_map_table($map, $hand, $m);
    echo '</div>';
}
echo '</div>';

