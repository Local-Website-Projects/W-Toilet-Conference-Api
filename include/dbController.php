<?php
class DBController {
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "llinkto";
    private $from_email='business@llinkto.com';
    private $notification_email='frogbidofficial@gmail.com';
    private $conn;

    function __construct() {
        if($_SERVER['SERVER_NAME']=="www.llinkto.com"||$_SERVER['SERVER_NAME']=="llinkto.com"){
            $this->host = "localhost";
            $this->user = "uyvadbdgm4k28";
            $this->password = "+91iexAy61@)";
            $this->database = "dbvwg11v4ssqje";
        }

        $this->conn = $this->connectDB();
    }

    function connectDB() {
        $conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
        return $conn;
    }

    function checkValue($value) {
        $value=mysqli_real_escape_string($this->conn, $value);
        return $value;
    }

    function selectQuery($query) {
        $result = mysqli_query($this->conn,$query);
        while($row=mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        if(!empty($resultset))
            return $resultset;
    }

    function insertQuery($query) {
        $result = mysqli_query($this->conn,$query);
        return $result;
    }

    function updateQuery($query) {
        $result = mysqli_query($this->conn,$query);
        return $result;
    }

    function deleteQuery($query) {
        $result = mysqli_query($this->conn,$query);
        return $result;
    }

    function from_email(){
        return $this->from_email;
    }

    function numRows($query) {
        $result  = mysqli_query($this->conn,$query);
        $rowcount = mysqli_num_rows($result);
        return $rowcount;
    }

    function notify_email(){
        return $this->notification_email;
    }
}
