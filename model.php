<?php

class Dobutushogi_model {

    private $dbh;

    public function __construct(PDO $dbh) {
        $this->dbh = $dbh;
    }

    public function regist_moves($moves) {
        foreach ($moves as $move) {
            $this->regist_move($move);
        }
    }

    public function regist_move(Move $move) {
        $map_id = $this->regist_map($move->map);
        $move_id = $this->select_id_move($map_id, $move);
        if (!$move_id) {
            $move_id = $this->insert_move($map_id, $move);
        } else {
            $this->update_move_point($move_id, $move->point);
        }
    }

    public function insert_move($map_id, Move $move) {
        $sql = 'insert into ds_move (`map_id`, `move_from`, `move_to`, `move_hand`, `point`) values (:MAP_ID, :MOVE_FROM, :MOVE_TO, :MOVE_HAND, :POINT)';
        $stmt = $this->dbh->prepare($sql);
        $params = array(
            ':MAP_ID' => $map_id,
            ':MOVE_FROM' => $move->from,
            ':MOVE_TO'   => $move->to,
            ':MOVE_HAND' => $move->hand,
            ':POINT' => $move->point,
        );
        $stmt->execute($params);
        return $this->dbh->lastInsertId();
    }

    public function update_move_point($move_id, $point) {
        $sql = 'update ds_move set `point` = point + :POINT where `move_id` = :MOVE_ID';
        $stmt = $this->dbh->prepare($sql);
        $params = array(
            ':MOVE_ID' => $move_id,
            ':POINT' => $point
        );
        $stmt->execute($params);
    }

    public function select_id_move($map_id, Move $move) {
        $sql = 'select * from ds_move where `map_id` = :MAP_ID and `move_from` = :MOVE_FROM and `move_to` = :MOVE_TO and `move_hand` = :MOVE_HAND';
        $stmt = $this->dbh->prepare($sql);
        $params = array(
            ':MAP_ID' => $map_id,
            ':MOVE_FROM' => $move->from,
            ':MOVE_TO'   => $move->to,
            ':MOVE_HAND' => $move->hand,
        );
        $stmt->execute($params);
        if ($res = $stmt->fetch()) {
            return $res['move_id'];
        } else {
            return FALSE;
        }
    }

    public function regist_map($map) {
        $id = $this->select_id_map($map);
        if (!$id) {
            $id = $this->insert_map($map);
        }
        return $id;
    }

    public function insert_map($map) {
        $sql = 'insert into ds_map (`map_info`) values(:MAP_INFO)';
        $stmt = $this->dbh->prepare($sql);
        $params = array(
            ':MAP_INFO' => $map,
        );
        $stmt->execute($params);
        return $this->dbh->lastInsertId();
    }

    public function select_id_map($map) {
        $sql = 'select * from ds_map where `map_info` = :MAP_INFO';
        $stmt = $this->dbh->prepare($sql);
        $params = array(
            ':MAP_INFO' => $map,
        );
        $stmt->execute($params);
        if ($res = $stmt->fetch()) {
            return $res['map_id'];
        } else {
            return FALSE;
        }
    }

    public function select_moves($map) {
        $sql = 'select * from ds_move where (`map_id`) in (select `map_id` from ds_map where `map_info` = :MAP_INFO)';
        $stmt = $this->dbh->prepare($sql);
        $params = array(
            ':MAP_INFO' => $map,
        );
        $stmt->execute($params);
        $moves = array();
        while($res = $stmt->fetch()) {
            $move = new Move();
            $move->to = $res['move_to'];
            $move->from = $res['move_from'];
            $move->point = $res['point'];
            $moves[] = $move;
        }
        return $moves;
    }

}

