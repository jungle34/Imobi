<?php 
    class Database {
        private $user ;
        private $host;
        private $pass ;
        private $db;

        // Configurações de conexão com a base
        public function __construct() {
            $this->user = "************";
            $this->host = "************";
            $this->pass = "***********";
            $this->db = "***********";
        }

        public function connect() {
            try {
                $dataBase = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->pass);
                $dataBase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dataBase->setAttribute(PDO::ATTR_EMULATE_PREPARES, false );
            } catch ( PDOException $Exception ) {

                print_r ($Exception);
                die('Erro ao conectar na base de dados');
            }
            return $dataBase;
        }		
    }
?>
