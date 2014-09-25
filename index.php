<?php
require_once('./Game.php');
require_once("./model.php");
require_once('./keys.php');
require_once('./funcs.php');
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
usort($moves, "cmp_point");

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="./style/main.css">
<title>どうぶつしょうぎ解析</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="./js/script.coffee"></script>
</head>
<body>
<h1>どうぶつしょうぎ解析</h1><span>v1.0</span>
<p>
    <a href="<?= BASE_URL ?>">最初から</a>
    <a href="<?= $flip_url ?>">反転</a>
</p>
<div class="left-column">
<?php print_map_table_main($map, $hand, $flip, $moves); ?>
</div>

<div class="right-column">
<span>候補</span>
<div class="list">
<?php foreach ($moves as $m) { ?>
    <div class="item">
<?php print_map_table($map, $hand, $flip, $m); ?>
    </div>
<?php } ?>
</div>
</div>

<a class="pull-right" href="//elzup.com">elzup.com</a>
</body>
</html>
