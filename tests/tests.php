<?php
header("Content-type:text/plain; charset=utf-8");

//
function catch_errors($n, $str){
    print "Exception: [{$n}] {$str}".PHP_EOL;
    die();
}
set_error_handler("catch_errors");

/*
 * 
 * Load the Odds Class.
 * 
 */
require dirname(__FILE__)."/../src/Odds.php";
require dirname(__FILE__)."/../src/Payouts.php";
require dirname(__FILE__)."/../src/Surebets.php";

/*
 * 
 * Odds convertions
 * 
 */
use OddsPHP\Odds as Odds;
$odd = new Odds();

print "1800.00 to decimal: ".$odd->set('decimal', 1800.00)->get('decimal').PHP_EOL;

// Converting from decimal to fractional.
print "1.55 to fractional: ".$odd->set('decimal', 1.55)->get('fractional').PHP_EOL;
print "5.50 to fractional: ".$odd->set('decimal', 5.50)->get('fractional').PHP_EOL;

// Converting fractional to decimal.
print "11/20 to decimal: ".$odd->set('fractional', '11/20')->get('decimal') . PHP_EOL;
print "22/40 to decimal: ".$odd->set('fractional', '22/40')->get('decimal') . PHP_EOL; // Fractions are automatically reduced.

// Converting moneyline to decimal.
print "-500 to decimal: ".$odd->set('moneyline', '-500')->get('decimal') . PHP_EOL;
print "125 to decimal: ".$odd->set('moneyline', '125')->get('decimal') . PHP_EOL;

// Fractional to moneyline
print "11/20 to moneyline: ".$odd->set('fractional', '11/20')->get('moneyline') . PHP_EOL;
exit("Test completed!");
?>