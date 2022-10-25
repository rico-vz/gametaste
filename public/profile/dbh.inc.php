<?php

class Dbh
{

    private $servername;
    private $dbname;
    private $charset;

    public function connect()
    {
        $this->servername = "localhost";
        $this->dbname = "UR_DB_NAME";
        $this->username = "root";
        $this->charset = "utf8mb4";

        try {
            $dataSourceName = "mysql:host=" . $this->servername . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $pdo = new PDO($dataSourceName, $this->username);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            $dbname = $pdo->query('select database()')->fetchColumn();
            return $pdo;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}