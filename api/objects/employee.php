<?php

/**
 * Creating Product Object
 *
 * @author Junaid Farooqui
 */
class Employee
{

    // database connection and table name
    private $conn;
    private $table_name = "employees";

    // object properties
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $phoneNumber;
    public $profileImage;
    public $departmentId;
    public $created;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // read products
    public function read()
    {

        // select all query
        $query = "SELECT
                c.name as department_name, p.id, p.firstName, p.lastName, p.email, p.phoneNumber, p.profileImage, p.departmentId, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    department c
                        ON p.departmentId = c.id
            ORDER BY
                p.created DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();


        return $stmt;
    }

    // create product
    public function create()
    {

        // query to insert record
        $query = "INSERT INTO
                " . $this->table_name . "
            SET
                firstName=:firstName, lastName=:lastName, email=:email, phoneNumber=:phoneNumber, profileImage=:profileImage, departmentId=:departmentId, created=:created";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phoneNumber = htmlspecialchars(strip_tags($this->phoneNumber));
        $this->profileImage = htmlspecialchars(strip_tags($this->profileImage));
        $this->departmentId = htmlspecialchars(strip_tags($this->departmentId));
        $this->created = htmlspecialchars(strip_tags($this->created));

        // bind values
        $stmt->bindParam(":firstName", $this->firstName);
        $stmt->bindParam(":lastName", $this->lastName);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phoneNumber", $this->phoneNumber);
        $stmt->bindParam(":profileImage", $this->profileImage);
        $stmt->bindParam(":departmentId", $this->departmentId);
        $stmt->bindParam(":created", $this->created);

        // execute query
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // used when filling up the update product form
    public function readOne()
    {

        // query to read single record
        $query = "SELECT
                c.name as department_name, p.id, p.firstName, p.lastName, p.email, p.phoneNumber, p.profileImage, p.departmentId, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    department c
                        ON p.departmentId = c.id
            WHERE
                p.id = ?
            LIMIT
                0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = array();

        // set values to object properties
        $response['id'] = $row['id'];
        $response['firstName'] = $row['firstName'];
        $response['lastName'] = $row['lastName'];
        $response['email'] = $row['email'];
        $response['phoneNumber'] = $row['phoneNumber'];
        $response['profileImage'] = $row['profileImage'];
        $response['departmentId'] = $row['departmentId'];
        $response['created'] = $row['created'];

        return $response;
    }

    // update the product
    public function update()
    {

        // update query
        $query = "UPDATE
                " . $this->table_name . "
            SET
                firstName = :firstName,
                lastName = :lastName,
                email = :email,
                phoneNumber = :phoneNumber,
                profileImage = :profileImage,
                departmentId = :departmentId
            WHERE
                id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phoneNumber = htmlspecialchars(strip_tags($this->phoneNumber));
        $this->profileImage = htmlspecialchars(strip_tags($this->profileImage));
        $this->departmentId = htmlspecialchars(strip_tags($this->departmentId));
        $this->created = htmlspecialchars(strip_tags($this->created));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind values
        $stmt->bindParam(":firstName", $this->firstName);
        $stmt->bindParam(":lastName", $this->lastName);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phoneNumber", $this->phoneNumber);
        $stmt->bindParam(":profileImage", $this->profileImage);
        $stmt->bindParam(":departmentId", $this->departmentId);
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // delete the product
    public function delete()
    {

        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;

    }

    // search products
    public function search($keywords)
    {

        // select all query
        $query = "SELECT
                c.name as department_name, p.id, p.firstName, p.lastName, p.email, p.phoneNumber, p.profileImage, p.departmentId, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.departmentId = c.id
            WHERE
                p.firstName LIKE ? OR p.lastName LIKE ? OR c.name LIKE ?
            ORDER BY
                p.created DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // read products with pagination
    public function readPaging($from_record_num, $records_per_page)
    {

        // select query
        $query = "SELECT
                c.name as department_name, p.id, p.firstName, p.lastName, p.email, p.phoneNumber, p.profileImage, p.departmentId, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.departmentId = c.id
            ORDER BY p.created DESC
            LIMIT ?, ?";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind variable values
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        // execute query
        $stmt->execute();

        // return values from database
        return $stmt;
    }

    // used for paging products
    public function count()
    {
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];
    }
}