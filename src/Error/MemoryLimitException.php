<?php
namespace Easyme\Error;

class MemoryLimitException extends \Exception{
    
    public function __construct() {
        parent::__construct('Limite de memoria atingido', null, null);
    }
    
    public function alert(){
        echo "    <script>
        $(function(){
        
            if($('#alert_memory_limit_exception')[0]) return;
            
            jConfirm('<input type=\"hidden\" id=\"alert_memory_limit_exception\">Não foi possível carregar todos os seus lançamentos.<br>Os dados apresentados podem estar inconsistentes ou incompletos.','Falha de sistema',function(r){
                if(r){
                    location.href = '/limitememoria';
                }
            },{
                ok : 'Exibir detalhes',
                cancel: 'Fechar'
            });
        })
    </script> ";
    }
    
    public function box(){
        echo '<div style="padding: 0 20px;"> <p class="text-center alert-danger" style="padding: 10px;">
        Não foi possível carregar todos os seus lançamentos. <a href="limitememoria">Clique aqui para mais informações.</a>
        </p></div>';
    }
    
}