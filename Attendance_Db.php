<?php
namespace MyApp;

class AttendanceDb{
    private $_db;

    public function __construct() {
        try {
            $this->_db = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);
            $this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }


    public function getMembers() {
        $stmt = $this->_db->query("SELECT * FROM members ORDER BY id");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }


    public function getTypes() {
        $stmt = $this->_db->query("SELECT * FROM types ORDER BY id");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }


    public function getHistories() {
        // $stmt = $this->_db->query("TRUNCATE TABLE histories");
        // $stmt->execute();
        // 上2行はテスト用
        // 結合しちゃおう
        $histories_query = "
        SELECT
            H.id,
            M.name as member_name,
            T.name as type_name,
            H.apply_date,
            H.arrive_time,
            H.reason
        FROM
            histories H
            INNER JOIN members M
                ON H.member_id = M.id
            INNER JOIN types T
                ON H.type_id = T.id
        ORDER BY
            H.id
            ";
        $stmt = $this->_db->query($histories_query);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }


    public function post() {
        if (!isset($_POST['mode'])) {
            throw new \Exception('mode not set!');
        }
        switch ($_POST['mode']) {
            case 'leave':
                return $this->_leaveInHistories();
            case 'serach':
                return $this->_searchFromHistories();
        }
        //return $this->_leaveInHistories();
    }


    private function _leaveInHistories(){
        //ここでは入力データが存在するかどうかのみチェック
        if(!isset($_POST['input_data'])) {
            throw new \Exception('[leave] input not set!');
        }
        parse_str($_POST['input_data']);//inputsのクエリ文字列を変数に

        $sql_query = "
        INSERT INTO
            histories
        (
            member_id,
            type_id,
            apply_date,
            arrive_time,
            reason
        )
        VALUES
        (
            :member_id,
            :type_id,
            :apply_date,
            :arrive_time,
            :reason
        )
        ";

        $stmt = $this->_db->prepare($sql_query);
        $record_insert = array(':member_id' => $member_id,  ':type_id'     => $type_id,
                               ':apply_date'=> $apply_date, ':arrive_time' => $arrive_time,
                               ':reason'    => $comment);

        $stmt->execute($record_insert);
        //以下戻り値の整備
        $id = $this->_db->lastInsertId();
        $query_member_name = sprintf("SELECT name FROM members where id = %d", $member_id);
        $stmt = $this->_db->query($query_member_name);
        $member_name = $stmt->fetchColumn();
        $query_type_name = sprintf("SELECT name FROM types where id = %d", $type_id);
        $stmt = $this->_db->query($query_type_name);
        $type_name = $stmt->fetchColumn();

        return [
            'id' => $id,
            'member_name' => $member_name,
            'type_name'   => $type_name,
            'apply_date'  => $apply_date,
            'arrive_time' => $arrive_time,
            'comment'     => $comment
        ];
    }


    private function _searchFromHistories(){
        //ここでは入力データが存在するかどうかのみチェック
        if(!isset($_POST['search_conditions'])) {
            throw new \Exception('[serach] input not set!');
        }
        parse_str($_POST['search_conditions']);//inputsのクエリ文字列を変数に

        //まず指定範囲内のレコードすべてを持ってくる
        $search_query = "
        SELECT
            members.name,
            types.name,
            histories.apply_date,
            histories.arrive_time,
            histories.reason
        FROM
            histories
            	INNER JOIN members
            		ON histories.member_id = members.id
            	INNER JOIN types
                	ON histories.type_id = types.id
        WHERE
            1 = 1
            {$additional_condition}
        ";

        $stmt = $this->_db->prepare($search_query);
        $search_date_range = array(':first_date' => $date_range_first, ':type_id' => $date_range_last);
        $stmt->query($search_date_range);
        //以下戻り値の整備

        return [
            'id' => $id,
            'member_name' => $member_name,
            'type_name'   => $type_name,
            'apply_date'  => $apply_date,
            'arrive_time' => $arrive_time,
            'comment'     => $comment
        ];
    }

}

// public function post() {
//     if (!isset($_POST['mode'])) {
//         throw new \Exception('mode not set!');
//     }
//     echo $_POST['mode'], __LINE__;
//     switch ($_POST['mode']) {
//         case 'leave':
//             return $this->_leaveInHistories();
//         case 'delete':
//             return $this->_deleteFromHistories();
//     }
// }
