<?php
function cmp_point(Move $a, Move $b) {
    return $a->point == $b->point ? 0 : ($a->point < $b->point) ? 1 : -1;
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

function print_map_table($map, $hand, $flip, Move $move) {
    $hand_head = '◯' . ($hand ? '★' : '');
    $hand_foot = (!$hand ? '★' : '') . '●';
    $class = '';
    echo '<span>' . $move->point . 'pt</span>';
    $map = Game::next_map($map, $move);
    echo '<a class="select" href="' . BASE_URL . '?m=' . Game::map_to_str($map) . ':' . (1 ^ $hand) . '&f="' . $flip . '>';
    $move_str = $move->get_str();
    $class .= ' sub';
    $class .= $flip ? ' flip' : '';
    echo '<table class="' . $class .'">';

    for ($j = 0; $j < 4; $j++) {
        echo '<tr>';
        for ($i = 0; $i < 3; $i++) {
            $math_hand = HAND_BLACK;
            $a = $map[$j][$i];
            if ($a > 4) {
                $math_hand = HAND_WHITE;
                $a = -($a - HAND_SHIFT);
            }
            if ($a == 0) {
                $math_hand = $hand + 2;
            }
            $td_classes = array(
                'math',
                'hand-' . $math_hand,
                'animal-' . Move::to_animal_str($a, TYPE_ASTR_ENG)
            );
            if ($move->from - 11 == ($i . $j)) {
                $td_classes[] = 'from';
            }
            if ($move->to - 11 == ($i . $j)) {
                $td_classes[] = 'to';
            }
            $td_class = implode(' ', $td_classes);
?>
            <td colspan="2" class="<?= $td_class ?>">
            <div class="a-char"><?= Move::to_animal_str($a) ?></div>
            </td>
<?php
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '</a>';
}

function moves_to_poslist($moves) {
    $list = array();
    foreach ($moves as $move) {
        // TODO:
    }
}

function print_map_table_main ($map, $hand, $flip, array $moves) {
    $hand_head = '◯' . ($hand ? '★' : '');
    $hand_foot = (!$hand ? '★' : '') . '●';
    $class = ' main' . ($flip ? ' flip' : '');
?>
    <span>元盤面</span>
    <table class="<?= $class ?>">
    <tr><td colspan="2"><?= $hand_head ?></td></tr>
<?php
    for ($j = 0; $j < 4; $j++) {
        echo '<tr>';
        for ($i = 0; $i < 3; $i++) {
            $math_hand = HAND_BLACK;
            $a = $map[$j][$i];
            if ($a > 4) {
                $math_hand= HAND_WHITE;
                $a = -($a - HAND_SHIFT);
            }
            if ($a == 0) {
                $math_hand = $hand + 2;
            }
            $td_classes = array(
                'math',
                'hand-' . $math_hand,
                'animal-' . Move::to_animal_str($a, TYPE_ASTR_ENG)
            );
            $td_class = implode(' ', $td_classes);
?>
            <td colspan="2" class="<?= $td_class ?>">
            <div class="a-char"><?= Move::to_animal_str($a) ?></div>
            </td>
<?php
        }
        echo '</tr>' . PHP_EOL;
    }
?>
    <tr><td colspan="4"></td><td colspan="2"><?= $hand_foot ?></td></td>
    </table>
<?php
}
