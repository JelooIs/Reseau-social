<?php
class Database {
    private $host = 'localhost';
    private $port = '3308';
    private $db_name = 'reseau_social';
    private $username ='root';
    private $password = '';

    public $pdo;

    public function __construct() {
        try {
            $bdd = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $bdd;
        } catch (PDOException $e) {
           die("Erreur de connection: " . $e->getMessage());
       }
    }
}