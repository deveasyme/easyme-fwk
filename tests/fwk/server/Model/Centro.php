<?php

namespace Model;

/**
 * @Entity @Table(name="tbl_ags_agendamentos")
 **/
class Centro{
    
    /** @Id 
     * @Column(type="integer",name="fld_codigo") 
     * @GeneratedValue **/
    protected $id;
    
    /** @Column(type="string",name="fld_servico") **/
    protected $servico;
    
    public function getId() {
        return $this->id;
    }

    public function getServico() {
        return $this->servico;
    }

    public function setServico($servico) {
        $this->servico = $servico;
    }


}