<?php
/*
 * @package   OddsPHP/Payouts
 * @author    Github @jsgm
 * @license   MIT
 * @since     08-02-2020
 * @updated   07-06-2021
 *
 */

namespace OddsPHP;

class Payouts{
	private $odds=[];
	private $probs=[];
	private $precision=2;
    
	public function __construct($group=null){
        $odds = new Odds();
        if($group>null && is_array($group)){

            foreach($group as $odd){
                // Odd array must have the following format: ['format', 'odd']
                if(count($odd)!=2) continue;

                // The odd format is not valid.
                if(!$odds->is_valid_format($odd[0])) continue;

                $odd = $odds->set($odd[0], $odd[1]);
                array_push($this->probs, $odd->get('implied'));
                array_push($this->odds, $odd->get('decimal'));
            }
        }else{
            throw new \Exception('Given odds must be an array.');
        }
	}

    public function get_implied_probabilities(){
        return $this->probs;
    }

    public function get_payout(){
        return abs(100-$this->get_overround());
    }
    
    public function get_overround(){
        return number_format(array_sum($this->probs)-100, $this->precision);
    }

    public function get_real_probabilities(){
        $probabilities = [];
        foreach($this->odds as $odd){
            array_push($probabilities, round(100/($odd*($this->get_payout()/100)), $this->precision));
        }
        return $probabilities;
    }
}
?>