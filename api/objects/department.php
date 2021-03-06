<?php
/**
 * @var $this Department Class
 *
 * @author Junaid Farooqui
 */
class Department{

    // database connection and table name
    private $conn;
    private $table_name = "department";

    // object properties
    public $id;
    public $name;

    public function __construct($db){
        $this->conn = $db;
    }

    // used by select drop-down list
    public function readAll(){
        //select all data
        $query = "SELECT
                    dept_no, dept_name
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        return $stmt;
    }
}