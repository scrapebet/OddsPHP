<?php
/*
 * @package   OddsPHP/Odds
 * @author    @jsgm (Github)
 * @license   MIT
 * @since     08-02-2020
 * @updated   28-05-2020
 *
 */

namespace OddsPHP;

class Odds{
	private $decimal=null;
	private $precision=2;

	const DECIMAL = 'decimal';
	const FRACTIONAL = 'fractional';
	const MONEYLINE = 'moneyline';
	const IMPLIED = 'implied';

  	/*
	 *
	 *
	 * Constructor.
	 *
	 *
	 */  
	function __construct(){

	}
    
    /*
	 *
	 *
	 * Edit precision.
	 *
	 *
	 */
    public function set_precision($precision=null){
        if($precision>null && $this->is_numeric($precision) && $precision>=0){
            $this->precision = (int)$precision;
		}
	}
    public function get_current_precision(){
        return $this->precision;
    }
    
	/*
	 *
	 *
	 * Sets the odd before making conversions.
	 *
	 *
	 */
    public function set($type=null, $odd=null){
        if($type>null && $odd>null){
			switch($type):
				case self::DECIMAL:
					return $this->set_decimal($this->parse_float($odd));
				break;
				case self::FRACTIONAL:
					return $this->set_fractional($odd);
				break;
				case self::MONEYLINE:
					return $this->set_moneyline($odd);
				break;
				case self::IMPLIED:
					return $this->set_implied($odd);
				break;
				default:
					throw new \Exception('Please provide a correct type for set, allowed are: \'decimal\', \'fractional\' or \'moneyline\'.');
				break;
			endswitch;
		}else{
			throw new \Exception('Please provide a valid odd and type.');
		}
		return $this;
    }
    public function set_decimal($odd=NULL){
        if($odd>NULL && $this->is_decimal($odd)){
            $this->decimal = $odd;
        }else{
            throw new \Exception('Provided decimal odd is not correct.');
		}
		return $this;
    }
    public function set_fractional($odd=NULL){
        if($odd>NULL && $this->is_fractional($odd)){
            $this->decimal=$this->fractional_to_decimal($odd);
        }else{
            throw new \Exception('Provided fractional odd is not correct.');
		}
		return $this;
    }
    public function set_moneyline($odd=NULL){
        if($odd>NULL && $this->is_moneyline($odd)){
            $this->decimal=$this->moneyline_to_decimal($odd);
        }else{
            throw new \Exception('Provided moneyline odd is not correct.');
		}
		return $this;
	}
	public function set_implied($odd){
		if($odd>null){

		}
		return $this;
	}
	
	/*
	 *
	 *
	 * Returns the odd in the chosen format.
	 *
	 *
	 */   
	public function get($type=null){
        if($type>null){
			switch($type):
				case self::DECIMAL:
					$result = $this->get_decimal();
				break;
				case self::FRACTIONAL:
					$result = $this->get_fractional();
				break;
				case self::MONEYLINE:
					$result = $this->get_moneyline();
				break;
				case self::IMPLIED:
					$result = $this->get_implied_probability();
				break;
				default:
					throw new \Exception('Please provide a correct type for set, allowed are: \'decimal\', \'fractional\' or \'moneyline\'.');
				break;
			endswitch;

			if(isset($result) && $result){
				return $result;
			}
		}else{
			throw new \Exception('Please provide a valid odd and type.');
		}
		return $this;
	}

	private function odd_not_set_exception(){
		if($this->decimal==null){
			throw new \Exception('Please provide a valid odd first.');
		} 
	}
	
	public function get_decimal(){
		$this->odd_not_set_exception();
		$decimal = (float)($this->decimal);
		return (float)($this->decimal);
	}
	public function get_moneyline(){
        $this->odd_not_set_exception();
		return (float)round($this->decimal_to_moneyline($this->decimal));
	}
	public function get_fractional(){
        $this->odd_not_set_exception();
		return (string)$this->decimal_to_fraction($this->decimal);
	}
	public function get_implied_probability(){
        $this->odd_not_set_exception();
		return $this->decimal_to_implied_probability($this->decimal);
	}
	
	/*
	 *
	 *
	 * Convert odds between the allowed formats.
	 *
	 *
	 */
	private function decimal_to_implied_probability($decimal){
		if($this->is_decimal($decimal)){
			return round(1/(float)$decimal*100, $this->precision);	
		}
		return false;
	}
	private function decimal_to_moneyline($decimal){
		// https://www.pinnacle.com/en/betting-articles/educational/converting-between-american-and-decimal-odds/PBS2VKQZ7ZB5TZDB
		try{
			if($this->is_decimal($decimal)){
				if($decimal>=2.00){
					return ($decimal-1)*100;
				}else{
					if($decimal-1 == 0) return 0;
					return (-100)/($decimal-1);
				}
			}
		}catch(Exception $ex){
			return false;
		}
		return false;
	}
	private function decimal_to_fraction($dec){
		if($this->is_decimal($dec)){
			$dec = number_format($dec, $this->precision);
            $reduced = $this->reduce_fraction(round(($dec-1)*100), round(100));
            return $reduced[0]."/".$reduced[1];
        }
        return false;
	}
	private function fractional_to_decimal($fractional){
		if($this->is_fractional($fractional)){
			$fraction = explode("/", $fractional);
			return $fraction[0]/$fraction[1]+1.00;
		}
		return false;
	}
	private function moneyline_to_decimal($moneyline){
		if($this->is_moneyline($moneyline)){
			if($moneyline>0){
				return $moneyline/100+1;
			}else{
				return abs($moneyline)/100+1;
			}
		}
		return false;
	}
	 
	/*
	 *
	 *
	 * Check given odds format. Allowed formats are the following ones:
	 * - Decimal
	 * - Fractional
	 * - Moneyline (US)
	 *
	 *
	 */
	private function is_decimal($odds){
		return ($this->has_decimal_part($odds) || is_numeric($odds));
	}
    private function has_decimal_part($odds){
        return (is_numeric($odds) && floor($odds) != $odds);
    }
	private function is_moneyline($odd=NULL){
		return $this->is_numeric(abs($odd));
	}
	private function is_fractional($odds){
		if(strpos($odds, "/") !== FALSE){
			$fraction = explode("/", $odds);
			if(!empty($fraction) && count($fraction) == 2){
				foreach($fraction as $number){
					if(!ctype_digit($number)){
						return false;
					}
				}
				return true;
			}
		}
		return false;
	}
	
	/*
	 *
	 *
	 * Greatest common divisor.
	 *
	 *
	 */
	private function reduce_fraction($a, $b){
		$gcd = $this->gcd($a, $b);
	  	return [$a/$gcd, $b/$gcd];
	}
	private function gcd($a, $b) {
		return ( $a % $b ) ? $this->gcd( $b, $a % $b ) : $b;
	}
	
	/*
	 *
	 *
	 * Helpers
	 *
	 *
	 */
	private function is_numeric($value){
		return preg_match('/^[0-9]+$/i', $value);
	}
	private function parse_float($value=0.0){
		return floatval(preg_replace('/\.(?=.*\.)/', '', str_replace(",", ".", $value)));
	}
}
?>