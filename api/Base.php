<?php

include_once "Database.php";


Class Base{	        

	/**
     * Makes the connect with database to check access token
     */
    function Connect(){
        $db = new Database();        
        $this->db = $db->connect();        
    }

    function checkToken(){
        $this->headers = $this->getHeaders();

        $authorization = !empty($this->headers['Authorization']) && isset($this->headers['Authorization']) ? $this->headers['Authorization'] : false;
        
        if(!$authorization){
            echo json_encode(array("TYPE" => 'ERROR', "MSG" => 'Authorization TOKEN é obrigatório nesta requisição'));
            die();
        }

        $this->Connect();

        $variables = array(
            "pass" => $authorization
        );

        $query = "SELECT * FROM usuarios u WHERE passw = :pass";
        
        try{
            $check = $this->db->prepare($query);
            $check->execute($variables);
        }catch(PDOException $Exception){
            echo json_encode(array("TYPE" => 'ERROR', "MSG" => 'Ocorreu um erro durante a consulta'));
            die();
        }

        $user = $check->fetchObject();
        
        if(!$user){
            echo json_encode(array("TYPE" => 'ERROR', "MSG" => 'TOKEN inválido'));
            die(); 
        }else{
            return true;
        }        
    }

    /**
     * Get all request headers or block the rest if doesn't have request headers
     */
    function getHeaders(){
        $aux = array();
        $headers = getallheaders();

        foreach ($headers as $key => $value) {
            $aux[$key] = $value;
        }
        if(empty($aux)) $this->returnError("Request headers can not is empty");
        return $aux;
    } 

    function returnError($msg){
        echo json_encode(array("TYPE" => 'ERROR', "MSG" => $msg));
        die();
    }

    function checkEmptyVariable($str){
        $aux = !empty($str) ? $str : false;
        if(!$aux) $this->returnError("Requisição inválida");

        return urldecode($aux);
    }

}