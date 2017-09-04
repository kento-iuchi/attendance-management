<?php
include 'php/ChromePhp.php';

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

    public function getDepartments() {
        $stmt = $this->_db->query("SELECT * FROM departments ORDER BY id");
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
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
        $histories_query = "
        SELECT
            H.id,
            D.name as department_name,
            M.name as member_name,
            T.name as type_name,
            H.apply_date,
            TIME_FORMAT(H.arrival_time, '%H:%i') as arrival_time,
            TIME_FORMAT(H.leaving_time, '%H:%i') as leaving_time,
            H.reason,
            H.superior_checked
        FROM
            histories H
            INNER JOIN departments D
                ON H.department_id = D.id
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
            case 'search':
                return $this->_searchFromHistories();
            case 'export':
                return $this->_exportHistoriesToCsv();
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
            department_id,
            member_id,
            type_id,
            apply_date,
            arrival_time,
            leaving_time,
            reason,
            superior_checked
        )
        VALUES
        (
            :department_id,
            :member_id,
            :type_id,
            :apply_date,
            :arrival_time,
            :leaving_time,
            :reason,
            :superior_checked
        )
        ";
        ChromePhp::log($superior_checked);
        ChromePhp::log($department_id);
        $stmt = $this->_db->prepare($sql_query);
        if ($superior_checked == 'on'){
            $superior_checked = 1;
            $superior_check_comment = "確認済み";
        }else {
            $superior_checked = 0;
            $superior_check_comment = "いいえ";
        }
        $record_insert = array(':department_id'  => $department_id,':member_id'  => $member_id,    ':type_id'     => $type_id,
                               ':apply_date' => $apply_date,   ':arrival_time'=> $arrival_time,
                               ':leaving_time'=> $leaving_time, ':reason'      => $comment,
                               ':superior_checked' => $superior_checked);

        $stmt->execute($record_insert);
        //以下戻り値の整備
        $id = $this->_db->lastInsertId();
        $query_department_name = sprintf("SELECT name FROM departments where id = %d", $department_id);
        $stmt = $this->_db->query($query_department_name);
        $department_name = $stmt->fetchColumn();
        $query_member_name = sprintf("SELECT name FROM members where id = %d", $member_id);
        $stmt = $this->_db->query($query_member_name);
        $member_name = $stmt->fetchColumn();
        $query_type_name = sprintf("SELECT name FROM types where id = %d", $type_id);
        $stmt = $this->_db->query($query_type_name);
        $type_name = $stmt->fetchColumn();


        return [
            'id' => $id,
            'department_name'=> $department_name,
            'member_name'  => $member_name,
            'type_name'    => $type_name,
            'apply_date'   => $apply_date,
            'arrival_time' => $arrival_time,
            'leaving_time' => $leaving_time,
            'comment'      => $comment,
            'superior_checked' => $superior_check_comment
        ];
    }


    private function _searchFromHistories(){
        //ここでは入力データが存在するかどうかのみチェック
        if(!isset($_POST['search_conditions'])) {
            throw new \Exception('[serach] input not set!');
        }
        //ChromePhp::log($_POST['search_conditions']);
        parse_str($_POST['search_conditions']);//クエリ文字列を変数

        //検索条件の設定
        //メンバーもタイプも指定しない
        if( $member_id == "all_member" && $type_id == "all_type" ){
            $additional_condition = "";
        }
        //メンバーだけ指定
        if( $member_id != "all_member" && $type_id == "all_type" ){
            $additional_condition = sprintf(" AND member_id = %d", $member_id);
        }
        //種類だけ指定
        if( $member_id == "all_member" && $type_id != "all_type" ){
            $additional_condition = sprintf(" AND type_id = %d", $type_id);
        }
        //メンバーも種類も指定
        if( $member_id != "all_member" && $type_id != "all_type" ){
            $additional_condition = sprintf(" AND member_id = %d AND type_id = %d",
                                            $member_id, $type_id);
        }

        $search_query = sprintf("
        SELECT
            H.id,
            M.name as member_name,
            T.name as type_name,
            H.apply_date,
            TIME_FORMAT(H.arrival_time, '%%H:%%i') as arrival_time,
            TIME_FORMAT(H.leaving_time, '%%H:%%i') as leaving_time,
            H.reason
        FROM
            histories H
            	INNER JOIN members M
            		ON H.member_id = M.id
            	INNER JOIN types T
                	ON H.type_id = T.id
        WHERE
            1 = 1
            AND H.apply_date BETWEEN '%s' AND '%s'
            {$additional_condition}
        ",  $date_range_first,  $date_range_last);
        ChromePhp::log($search_query);
        //AND histories.apply_date BETWEEN :first_date AND :last_date
        $stmt = $this->_db->query($search_query);
        ChromePhp::log($stmt);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // ChromePhp::log($results);
        // foreach ($results as $key => $value) {
        //     ChromePhp::log($value);
        // }
        //以下戻り値の整備
        //return $stmt;
        return $results;
    }


    private function _exportHistoriesToCsv(){
        //ここでは入力データが存在するかどうかのみチェック
        if(!isset($_POST['export_conditions'])) {
            throw new \Exception('[serach] input not set!');
        }
        //ChromePhp::log($_POST['search_conditions']);
        parse_str($_POST['export_conditions']);//クエリ文字列を変数

        ChromePhp::log($date_range_first);

        $search_query = sprintf("
        SELECT
            M.name as member_name,
            T.name as type_name,
            COUNT(H.type_id) as type_count
        FROM
            histories H
            	INNER JOIN members M
            		ON H.member_id = M.id
            	INNER JOIN types T
                	ON H.type_id = T.id
        WHERE
            1 = 1
            AND H.apply_date BETWEEN '%s' AND '%s'
        GROUP BY
        	H.member_id, H.type_id
        ORDER BY
        	H.member_id
        ",  $date_range_first,  $date_range_last);

        ChromePhp::log($search_query);
        $stmt = $this->_db->query($search_query);
        $export_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ChromePhp::log($export_data);

        return $results;
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
