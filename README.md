![Building](https://github.com/jsgm/OddsPHP/actions/workflows/phplint.yml/badge.svg) ![License](https://img.shields.io/github/license/jsgm/OddsPHP)

# OddsPHP
OddsPHP is a lightweight library to easily work with odds conversions and calculations.

With a few lines of code you'll be able to:
 - Odds conversions between **decimal, fractional, american** and **implied probability**.
 - Calculate **payouts** and **overrounds**.
 - Getting the **real probabilities**.
 - **Surebets** calculations.

## Installation
Clone the repository or just download the files:
```
git clone https://github.com/jsgm/OddsPHP.git
```

At the top of your PHP file, add the classes you need in the following order:
```php
require  dirname(__FILE__)."/OddsPHP/src/Odds.php";
// require  dirname(__FILE__)."/OddsPHP/src/Payouts.php"; // (optional)
// require  dirname(__FILE__)."/OddsPHP/src/Surebets.php"; // (optional)

use OddsPHP\Odds as Odds;
// use OddsPHP\Payouts as Payouts;
// use OddsPHP\Surebets as Surebets;
```

## Tests
Check the **/test** folder to find a few examples to convert and manipulate the odds.

## 1. Working with odds
### 1.1 Convert odds between formats
Convert odds between different types of formats. If the given odd is not valid it will throw an exception.
```php
// Converting decimal to fractional.
$odd = new  Odds();
echo  $odd->set('decimal', 5.50)->get('fractional'); // "string" 9/2

// Converting fractionalto moneyline.
$odd = new  Odds();
echo  $odd->set('fractional', '11/25')->get('moneyline'); // "float" -227

// Converting fractional to implied probability.
$odd = new  Odds();
echo  $odd->set('fractional', '44/100')->get('implied'); // "string" 69.44
```
Allowed formats are:

 - 'decimal'
 - 'fractional'
 - 'implied'
 - 'moneyline'

### 1.2 Reduce to the lowest term a fractional odd
Reduce a fraction with the **reduce()** method or just pass any fraction and it will automatically reduce it for you as shown here:
```php
print $odd->set('fractional', '44/100')->reduce(); // "string" 11/25
print $odd->set('fractional', '44/100')->get('fractional'); // "string" 11/25
```

Bookmarkers always use the simplest form of a fraction and you should do too.

### 1.3 Set decimal and percentage precision
By default, all decimal odds and percentages will be returned with 2 decimals. You can modify that by using:
```php
$odd = new Odds();
$odd->set_precision(1);
$odd->set('decimal', 1.29)->get('decimal'); // "float" 1.3


$odd->set_precision(2);
$odd->set('decimal', '1,800.00')->get('decimal'); // "float" 1800.00
```

## 2. Calculating payouts and overrounds
Now that you know how to convert odds in a few seconds, OddsPHP also includes a few tools to help you doing extra calculations easily.

The payouts are the amount of money given back to the user who won a bet whereas the overround is the profit that the bookie takes. If you don't know this concepts, check [this website](https://caanberry.com/understanding-the-over-round-in-betting-markets/) where you can find a bunch of examples.

Let's assume we have the following market with the given odds:
| Home Team | Draw | Away Team |
|--|--|--|
| 4.45 | 3.40 | 1.90 |

This group of odds will be defined in our code like this (Rembember that you also can mix different odd formats):
```php
$group = [['decimal', 4.45], ['decimal', 3.40], ['decimal', 1.90]];

// Use the 'Payouts' class and don't forget to require it at the beginning of your code.
$payout = new Payouts($group);
```

The **payout** is calculated by simply subtracting the overround to 100. This can be done using the following method:
```php
print  $payout->get_payout(); // "float" 95.49
```

Getting the **implied probabilities** for each odd. This probabilities are not real since the bookmarker comission is added. You may need them if you want to calculate by yourself.
```php
// 100 / (decimal odd) = implied probability
var_dump($payout->get_implied_probabilities()); // [22.47, 29.41, 52.63]
```

If we sum all of them we will have a remaining percentage, that's the **overround**.
```php
// array_sum([22.47, 29.41, 52.63]) = 104.51
// 104.51 - 100 = Overround
print  array_sum($payout->get_implied_probabilities()); // "float" 104.51
print  $payout->get_overround(); // "float" 4.51
```

We can also get the **real probabilities** for a group of odds:
```php
// Formula: 100 / [Odd * (Payout / 100)] = real probability
$payout->get_real_probabilities(); // [23.53, 30.8, 55.12]
```

We may get at some point a negative result for the overround. That means we have found a surebet!

## 3. Surebets
We can check for a surebet given a odds set. Remember that you will need at least 2 different odds bookmarkers. The next example is a real surebet case. As you might have noticed the odds are provided by different bookies.

![Surebet example](https://es.surebet.com/ess/wiki/chelseamu.png)

```php
// The 'Payouts' class has to be required in order to use 'Surebets'.
$surebet = new Surebets([['decimal', 2.3], ['decimal', 3.3], ['decimal', 3.97]]);
if($surebet->is_surebet()){
	print 'Surebet found! :)';
	// '.($surebet->profit()).'% profit!';
}else{
	print 'Sorry! :( No surebet found!';
}
```
