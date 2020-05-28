<?php
/*
 * @package   OddsPHP/Surebets
 * @author    Github @jsgm
 * @license   MIT
 * @since     08-02-2020
 * @updated   14-05-2020
 *
 */

namespace OddsPHP;

class Surebets{
    private $is_surebet=false;
    public function __construct($odds=null){
        if($odds>null && is_array($odds)){
            $this->set($odds);
        }
    }
    public function set($odds){
        $payouts = new Payouts($odds);
        if($payouts->get_overround() < 0){
            $this->is_surebet=true;
        }else{
            $this->is_surebet=false;
        }
    }

    public function is_surebet(){
        return $this->is_surebet;
    }

    public function profit(){
        
    }
}
?>