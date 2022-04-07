<?php
    /**
     * Created by Sebbans.
     * Date: 2020-08-22
     * Time: 22:15
     */

    require_once "../definitions.php";

    class Database{

        public static $instance;

        private $dbhost = DB_HOST;
        private $dbuser = DB_USER;
        private $dbpass = DB_PASS;
        private $dbname = DB_DATABASE;

        private $conn;

        public $dbErrors = array();
        public $stmt;


        public function __construct(){
            $dsn = "mysql:host=$this->dbhost;dbname=$this->dbname;";

            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            );

            try{
                $this->conn = new PDO($dsn, $this->dbuser, $this->dbpass, $options);
            }catch (PDOException $e){
                $this->dbErrors[] = $e->getMessage();
            }

        }

        public static function getInstance(){

            if (!isset(self::$instance)) {
                self::$instance = new Database();
            }

            return self::$instance;
        }

        public function query($query){
            $this->stmt = $this->conn->prepare($query);
        }

        public function execute($arr = array()){
            return $this->stmt->execute($arr);
        }

        public function resultAll(){
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function resultSingle(){
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function rowCount(){
            return $this->stmt->rowCount();
        }

        public function lastInsertedId(){
            return $this->conn->lastInsertId();
        }
 
        public function select($columns, $table, $where = "1", $order = "1"){
            $this->query("SELECT ".implode(", ", $columns)." FROM $table WHERE $where ORDER BY $order");
            $this->execute();
            return $this->resultAll();
        }

        public function selectFirstRow($columns, $table, $where = "1", $order = "1"){
            $this->query("SELECT ".implode(", ", $columns)." FROM $table WHERE $where ORDER BY $order LIMIT 1");
            $this->execute();
            
            return $this->resultSingle();
        }

        public function updateColumn($column, $value, $table, $where){
            $this->query("UPDATE $table SET $column = :value WHERE $where");
            $this->execute(array("value" => $value));
        }

        public function updateMultipleColumns($associativeArray, $table, $where){
            $sql = "UPDATE $table SET ";
            $valueInsertArray = [];
            foreach($associativeArray as $name => $value){
                $valueInsertArray[$name."_index"] = $value;
                $sql .= "$name = :".$name."_index, ";
            }
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE $where";
            $this->query($sql);
            $this->execute($valueInsertArray);
        }

        public function advancedReturnQuery($query, $values = null){
            $this->query($query);
            $this->execute($values);
            $result = $this->resultAll();
            if(count($result) < 1){
                return $result;
            }
            return count($result) > 1 ? $result : $result[0];
        }

        public function advancedPushQuery($query, $values = null){
            $this->query($query);
            $this->execute($values);
        }



    }

