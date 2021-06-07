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

class Surebets extends Payouts{
    public function is_surebet(){
        return ($this->get_overround()<0);
    }

    public function profit(): float{
        return 0.0;
    }
}
?>