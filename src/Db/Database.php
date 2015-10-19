<?php

namespace Easyme\Db;

use \mysqli as mysqli;

class Database{

    const __DEFAULT_CHARSET__  = 'utf8'      ;

    private $mysql;
    private $host;
    private $user;
    private $password;
    private $database;
    private $insert_id;
    private $error;
    
    //Contador de transacoes abertas
    private $begins;
    
    /*Utilizada para marcar que uma transacao esta aberta*/
    private $inTransaction;
    
    /**
     * Cria objeto de Database.
     * @param string $host Host do banco
     * @param string $user Usuario do banco
     * @param string $password Password do usuario
     * @param string $database Nome da base de dados 
     */
    public function __construct($host = NULL , $user = NULL , $password = NULL , $database = NULL){
        
        $this->set($host,$user,$password,$database);
        $this->inTransaction = false;
        
        $this->begins = 0;
    }
    
    /**
     * Seta os parametros de conexao com o banco
     * @param string $host Host do banco
     * @param string $user Usuario do banco
     * @param string $password Password do usuario
     * @param string $database Nome da base de dados 
     */
    public function set($host = NULL , $user = NULL , $password = NULL , $database = NULL){

        /*Setando valores para conexao*/
        $this->host 	= $host ? $host : DB_HOST;
        $this->user 	= $user ? $user : DB_USER;
        $this->password = $password ? $password : DB_PASSWORD;
        $this->database = $database ? $database : DB_DATABASE;
    }
    
    /**
     * Conecta ao banco
     * @return Boolean 
     */
    private function _connect(){

        /*Conectando*/
        $this->mysql = new mysqli($this->host, $this->user, $this->password, $this->database);
        
        if($this->mysql->connect_error){
            throw new \Exception("Falha ao conectar com o banco de dados. 
                Errno: {$this->mysql->connect_errno} 
                Error: {$this->mysql->connect_error}");
        }

        /*Setando o charset da conexao*/
        $this->mysql->set_charset ( Database::__DEFAULT_CHARSET__ );

        
        /*Retorna V ou forca finalizacao do script*/
        return !$this->mysql->connect_error OR die($this->mysql->connect_error);
    }
    
    /**
     * Disconecta do banco 
     */
    private function _disconnect(){

        /*Desconecta*/
        $this->mysql->close();
    }
    
    /**
     * Executa uma query qualquer 
     * @param string $query Query a ser executada
     * @param callable Uma funcao que recebe cada linha do retorno de um select
     * @return mixed Retorna FALSE em caso de erro, TRUE em caso de INSERT/UPDATE
     * ou um array associativo em forma de matriz para outras consultas.
     */
    public function query($query,$callback = null, $fetchTypeForCallback = MYSQLI_ASSOC){

        /*Conecta se nao existe transacao aberta*/
        if(!$this->inTransaction) $this->_connect();
        
//        $msc=microtime(true);
        $res = $this->mysql->query($query);
//        $msc=microtime(true)-$msc;
//        
//        $log = new \Easyme\Util\Logger('Database');
//        $log->log(array(
//            'query' => $query,
//            'executionTime' => $msc * 1000
//        ));
        
        /*Executa query*/

        /*Erro na consulta ou INSERT | UPDATE*/
        if($res === FALSE || $res === TRUE){
            
            $this->insert_id = $this->mysql->insert_id;
            
            $this->error = $this->mysql->error;
            
            /*Desconectando*/
            if(!$this->inTransaction) $this->_disconnect();

            return $res;
        }

        /*Array para armazenar resultado*/
        $result = array();
        
        if(is_callable($callback)){
            while( $row = $res->fetch_array($fetchTypeForCallback) ){
                call_user_func($callback,$row);
            }
        }else{
            //fetch_all por alguma razao nao funciona no servidor da Under :/
            while( $row = $res->fetch_array($fetchTypeForCallback) ){
                $result[] = $row;
            }
            
        }
        
        /*Limpando memoria*/
        $res->free();

        /*Desconecta se nao existe transacao aberta*/
        if(!$this->inTransaction) $this->_disconnect();
                
        /*Retorna matriz de resultados associativos*/
        return $result;
    }
    
    /**
     * Executa uma instrucao de insert
     * @param string $query Query
     * @return Boolean 
     */
    public function select($query){
        /*Executa query*/
        return $this->query($query);
    }
    
    /**
     * Executa uma instrucao de insert
     * @param string $query Query
     * @return Boolean 
     */
    public function insert($query){
        /*Executa query*/
        return $this->query($query);
    }
    
    /**
     * Executa uma instrucao de insert
     * @param string $query Query
     * @return Boolean 
     */
    public function update($query){
        /*Executa query*/
        return $this->query($query);
    }
    
    /**
     * Executa uma instrucao de insert
     * @param string $query Query
     * @return Boolean 
     */
    public function delete($query){
        /*Executa query*/
        return $this->query($query);
    }
    
    /**
     * Executa uma instrucao de insert
     * @param string $query Query
     * @return Boolean 
     */
    public function count($query){
        /*Executa query*/
        return $this->executeCount($query);
    }
    
    /**
     * Retorna id do ultimo elemento inserido ou 0 caso a ultima query nao tenha
     * sido um INSERT.
     * @return int 
     */
    public function lastInsertID(){
        
        /*Resgatando ultimo id*/
        return $this->insert_id;
    }
    
    public function error(){
        return $this->error;
    }
    
    /**
     * Retorna o resultado de um COUNT()
     * @param string $query Count query
     * @return int 
     */
    public function executeCount($query){

        /*Conectando*/
        if(!$this->inTransaction) $this->_connect();

        /*Executa query*/
        $res = $this->mysql->query($query);

        /*Erro na consulta ou INSERT | UPDATE*/
        if($res === FALSE || $res === TRUE){
            
            $this->insert_id = $this->mysql->insert_id;

            /*Desconectando*/
            if(!$this->inTransaction) $this->_disconnect();

            return $res;
        }

        $linha = $res->fetch_array();

        /*Limpando memoria*/
        $res->free();

        /*Desconectando*/
        if(!$this->inTransaction) $this->_disconnect();

        return $linha[0];
    }
    
    public function begin(){
        
        if(!$this->begins++){
            $this->_transaction(true);
            /*Conectando*/
            $this->_connect();
            /*Desabilitando autocommit, i.e.: iniciando transacao*/
            $this->mysql->autocommit(FALSE);
        }
    }
    public function rollback(){
        
        if(!--$this->begins){
            $this->_transaction(false);
            /*Executando rollback*/
            $this->mysql->rollback();
            /*Desconectando*/
            $this->_disconnect();
        }
    }
    public function commit(){

        if(!--$this->begins){
            $this->_transaction(false);
            /*Comitando alteracoes*/
            $this->mysql->commit();
            /*Desabilitando autocommit, i.e.: iniciando transacao*/
            //$this->mysql->autocommit(TRUE);
            /*Desconectando*/
            $this->_disconnect();
        }
    }
    
    private function _transaction($onOff){
        $this->inTransaction = $onOff;
    }
    

    /*Metodos antigos - Depreciados (evitar usar)*/

    public function executeQuery($query){

        /*Conectando*/
        if(!$this->inTransaction) $this->_connect();

        /*Executa query*/
        $res = $this->mysql->query($query);

        /*Erro na consulta ou INSERT | UPDATE*/
        if($res === FALSE || $res === TRUE){
            
            $this->insert_id = $this->mysql->insert_id;

            /*Desconectando*/
            if(!$this->inTransaction) $this->_disconnect();

            return $res;
        }

        /*Informacoes sobre os campos*/
        $finfo = $res->fetch_fields();

        /*Array para armazenar resultado*/
        $result = array();

        for($j = 0 ; $j < $res->num_rows ; $j++){

            $row = $res->fetch_assoc();

            /*Alimenta array de retorno na forma matricial*/
            for($i = 0 ; $i < $res->field_count ; $i++ ){
                /*Indices invertidos*/
                $key = $finfo[$i]->name;
                $result[ $key ][$j] = $row[ $key ];
            }
        }

        /*Salvando tamanho da consulta na posicao 'size' */
        $result['size'] = $res->num_rows;

        /*Limpando memoria*/
        $res->free();

        /*Desconectando*/
        if(!$this->inTransaction) $this->_disconnect();


        /*Retorna matriz de resultados associativos*/
        return $result;

    }

    public function executeUpdate($query){
        
        return $this->executeQuery($query);
    }

    public function executeInsert($query){
        
        return $this->executeQuery($query);
    }

}


?>
