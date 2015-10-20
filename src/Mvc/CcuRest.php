<?php

namespace Easyme\Mvc;

use 
Easyme\Util\Response,
\Exception;

abstract class CcuRest extends \Easyme\DI\Injectable{
    
//    private function getParam(){
//        
//        $id = $this->dispatcher->getAction();
//        if($id == 'index'){
//            $id = null;
//        }
//        
//        return $id;
//    }
    
    public function _build() {
        
        // TODO: verificar tipo de requisicao de entrada $_SERVER[ACCEPT]
        
        
        $this->response->setContentType(Response::CONTENT_TYPE_JSON);
        
        
        
    }
    
    public function _dispatch($action,$params){
        
//        $method = strtolower($this->request->getMethod()) ;
//        $action = $method. 'Action';
        
        try{
//            $data = array();
//            $id = $this->getParam();
//            if($id !== null) $data[] = $id;
            
//            if( ($this->request->isDelete() || $this->request->isPut()) && ($id === null) ){
//                throw new Exception("O formato da requisicao é: " . $_SERVER['REQUEST_URI'] . '/:id', Response::RESPONSE_CODE_BAD_REQUEST);
//            }
//            
            $resp = call_user_method_array($action, $this, $params);
            
            if($resp){
                if(!($resp instanceof Response)){
                    
                    if($this->request->isGet()){
                        if(is_array($resp)){
                            $this->response->setJsonContent(array_map(function(ResourceInterface $resource){
                                return $resource->toArray();
                            },$resp));
                        }else{
                            $this->response->setJsonContent($resp->toArray());
                        }
                    // Pode fazer alguns outros tratamentos pra outros metodos depois
                    }else{
                        $this->setResource($resp->toArray());
                    }
                    
                }
            }
            
        } catch (\Easyme\Error\ApiException $ex) {
            $this->response->setStatusCode($ex->getCode());
            $this->response->setJsonContent(array(
                'error' => $ex->getId(),
                'message'  => $ex->getMessage()
//                'dados' => $this->request->getJsonRawBody()
            ));
        }
        
        return $this->response;
    }
    
    public function setResource(ResourceInterface $resource){
        
        $this->response->setJsonContent($resource->toArray());
        
        $id = $this->getParam();
        $rid = $resource->getId();
        if( $rid  && ($id != $rid) ){
            
            
            $location = 'http' . ($this->request->getPort()== 443 ? 's' : '') . "://".$this->request->getServerName() . $this->request->getUri(true);
            
            // Se ja tinha um id na uri anterior
            if($id){
                $location = substr($location, 0, strrpos($location, '/'));
            }
            
            $this->response->setStatusCode(Response::RESPONSE_CODE_CREATED);
            $this->response->setHeader('Location',  $location . '/' . $rid );
        }
    }
   
}
