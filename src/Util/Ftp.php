<?php
namespace Easyme\Util;

class Ftp{
    
    /**
     * Handle de conexao
     * @var Resource 
     */
    private $ftp;
    
    /**
     * Host de conexao
     * @var string 
     */
    private $host;
    
    private $user;
    private $password;
    private $passive;
    private $ssl;
    
    public function __construct($host, $user, $password, $passive = true, $ssl = false) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->passive = $passive;
        $this->ssl = $ssl;
    }
    
    public function connect(){
        $this->ftp = $this->ssl ? ftp_ssl_connect($this->host) : ftp_connect($this->host);
        
        if($this->ftp === FALSE || !@ftp_login($this->ftp, $this->user, $this->password)){
            throw new Exception('Falha ao criar conexão ftp');
        }
        
        if($this->passive && !ftp_pasv($this->ftp, true)){
            $this->disconnect();
            throw new Exception('Falha ao setar modo passivo');
        }
    }
    
    public function disconnect(){
        if($this->ftp) ftp_close($this->ftp);
    }
    
    public function pwd(){
        $dir = ftp_pwd($this->ftp);
        if($dir === FALSE) throw new \Exception("Falha ao obter diretório");
        return $dir;
    }
    
    public function chdir($dir){
        if(!ftp_chdir($this->ftp,$dir)) 
            throw new \Exception("Falha ao obter diretório");
    }
    
    public function chmod($dir){
//        if(!ftp_chdir($this->ftp,$dir)) 
//            throw new \Exception("Falha ao obter diretório");
    }
    
}