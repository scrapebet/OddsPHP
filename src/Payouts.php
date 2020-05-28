<?php
/*
 * @package   OddsPHP/Payouts
 * @author    Github @jsgm
 * @license   MIT
 * @since     08-02-2020
 * @updated   14-05-2020
 *
 */

namespace OddsPHP;

class Payouts{
	private $odds=[];
	private $probs=[];
	private $precision=2;
    
	public function __construct($odds=null){
        $precision = new Odds();
        $this->precision = $precision->get_current_precision();
        unset($precision);
        
		if($odds>null && is_array($odds)){
			foreach($odds as $odd){
                if(is_object($odd)){
                    array_push($this->odds, $odd->get('decimal'));
                }else{
                    $odd = new Odds($odd);
                    array_push($this->odds, $odd->get_decimal());
                    array_push($this->probs, $odd->get_implied_probability());
                }
            }
		}else{
            throw new Exception('Given odds must be an array.');
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