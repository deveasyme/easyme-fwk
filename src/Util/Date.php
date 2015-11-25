<?php
namespace Easyme\Util;

use DateTime;

class Date{
    
    /*
     * Retorna o ultimo dia de um mes
     * @param $ano_mes String String de data no formato yyyy-mm (opcional)
     */
    public function last_day($ano_mes=NULL)
    {
        if(!$ano_mes) $ano_mes = date("Y-m");
        return substr(sub_date(add_date($ano_mes,1,"MONTH") , 1 , "DAY" ),8,2) ;
    }

    public function datebtw($date,$start,$end){
        /*Se data antes do limite inferior, retorna negativo*/
        if( ($inf = datecmp($date,$start)) < 0 ) return $inf;
        /*Se data depois do limite superior, retorna positivo*/
        if( ($sup = datecmp($date,$end)) > 0 ) return $sup;
        /*Senao retorna 0*/
        return 0;
    }

    public function datecmp($date1,$date2=null)
    {
        if(!$date2) $date2 = date("Y-m-d");

        $date2 = strtotime($date2);

        $date1 = strtotime($date1);

        return $date1 == $date2 ? 0 : ($date1<$date2 ? -1 : 1);

    }

    public function sub_date($date,$quant,$interval="MONTH")
    {
        $mask = strlen($date) >= 19 ? "Y-m-d H:i:s" : "Y-m-d";
        if(!strlen($interval)) $interval = "MONTH";

        if($quant && $interval)
            {
                    if($date)
                    {
                            $aux = date($mask,strtotime($this->date_string_interval($quant*(-1),$interval),strtotime($date)));

                            if( ($interval == "MONTH" || $interval == "YEAR") && strlen($date) >= 10 && ( substr($date,8,2) != substr($aux,8,2) ) )
                            {
                                    $aux = sub_date($aux,1,"MONTH");
                                    $ano_mes = substr($aux,0,7);				
                                    $aux = $ano_mes . "-" . last_day($ano_mes);
                            }
                            return $aux;
                    }
                    else
                    {
                            return null;
                    }
            }
        else
            return $date;
    }

    public function add_date($date,$quant,$interval="MONTH")
    {

        $mask = strlen($date) >= 19 ? "Y-m-d H:i:s" : "Y-m-d";
        if(!strlen($interval)) $interval = "MONTH";

        if($quant && $interval)
            {
                    if($date)
                    {
                            $aux = date($mask,strtotime($this->date_string_interval($quant,$interval),strtotime($date)));

                            if( ($interval == "MONTH" || $interval == "YEAR") && strlen($date) >= 10 && ( substr($date,8,2) != substr($aux,8,2) ) )
                            {
                                    $aux = sub_date($aux,1,"MONTH");
                                    $ano_mes = substr($aux,0,7);				
                                    $aux = $ano_mes . "-" . last_day($ano_mes);
                            }
                            return $aux;
                    }
                    else
                    {
                            return null;
                    }
            }
        else
            return $date;
    //    $mask = strlen($date) >= 19 ? "Y-m-d H:i:s" : "Y-m-d";
    //    if(!strlen($interval)) $interval = "MONTH";
    //
    //    if($quant && $interval)
    //        return ($date) ? date($mask,strtotime(date_string_interval($quant,$interval),strtotime($date))) : null;
    //    else
    //        return $date;
    }

    public function date_string_interval($quant,$interval="MONTH")
    {
        return "$quant $interval";
    }

    /*Realiza $date1 - $date2 */
    public function diff_date($date1,$date2,$interval="MONTH",$frequency=1)
    {
        $i = 0;
        if($frequency)
        {
            if(datecmp($date1,$date2) > 0)
            {
                while(datecmp($date1,$date2) > 0)
                {
                    $date1 = sub_date($date1, $frequency , $interval);
                    $i++;
                }
            }
            else if(datecmp($date1,$date2) < 0)
            {
                while(datecmp($date1,$date2) < 0)
                {
                    $date1 = add_date($date1, $frequency , $interval);
                    $i--;
                }
            }
        }
        return $i;
    }


    public function timestamp($data=null)
    {
        if(!$data) $data = date("Y-m-d");

        $dia = substr($data,8,2);
        $mes = substr($data,5,2);
        $ano = substr($data,0,4);

        return mktime(0,0,0,$mes,$dia,$ano);

    }

    public function today()
    {
        return date("Y-m-d");
    }
    public function now()
    {
        return date("Y-m-d H:i:s");
    }
    
    public function format($date, $extended = false){
        $date = new DateTime($date);
        return $date->format($extended ? 'Y-m-d H:i:s' : 'Y-m-d');
    }
    
}