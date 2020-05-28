![License](https://img.shields.io/github/license/jsaguilera12/OddsConverterTools)

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
require  dirname(__FILE__)."/src/Odds.php";
require  dirname(__FILE__)."/src/Payouts.php"; // optional
require  dirname(__FILE__)."/src/Surebets.php"; // optional

use OddsPHP\Odds as Odds;
```

## Tests
Check the **/test** folder to find a few examples to convert and manipulate odds.

## 1. Working with odds
### 1.1 Convert odds between formats
Convert odds between different types of formats. If the given odd is not valid it will throw an exception.
```php
// Converting decimal (5.50) to fractional (9/2).
$odd = new  Odds();
echo  $odd->set('decimal', 5.50)->get('fractional'); // 9.2

// Converting fractional (11/25) to moneyline.
$odd = new  Odds();
echo  $odd->set('fractional', '11/25')->get('moneyline');
```
Allowed formats are:

 - 'decimal'
 - 'fractional'
 - 'implied'
 - 'moneyline'

### 1.2 Reduce to the lowest term a fractional odd
Bookmarkers use the simplest form of a fraction. If we provide to the Odds class a fraction which is not reduced it will automatically reduce it for you as shown here:
```php
print $odd->set('fractional', '44/100')->get('fractional'); // 11/25
```

### 1.3 Set decimal and percentage precision
By default, all decimal odds and percentages will be returned with 2 decimals. You can modify that by using:
```php
$odd = new Odds();
$odd->set_precision(1);
$odd->set('decimal', 1.29)->get('decimal'); // 1.3


$odd->set_precision(2);
$odd->set('decimal', '1,800.00')->get('decimal'); // 1800.00
```

## 2. Creating a group of odds
Let's assume we have the following market with the given odds:
| Home Team | Draw | Away Team |
|--|--|--|
| 5.50 | 1.38 | 2.15 |

To calculate payouts or a possible bet profit, using the same or different odds format first create a group which is just the same as before:
```php
$group = new Odds()->add('decimal', 5.50)->add('decimal', 1.38)->add('decimal', 2.15);
```
### 2.1 Calculate possible profit
```php
// How much we could win on a co
print $group->calculate(15);
```
### 2.2 Getting payouts and overrounds
You can also calculate the payout and overround for a determinate set of odds. The payouts are the amount of money given back to the user who won a bet whereas the overround is the profit for the bookie.
```php
// Let's create the Payout calculation with:
$payout = new Payouts($group);
```
We first need to calculate the implied probability, which is done with the following formula:
```php
// 100 / (decimal odd) = implied probability
var_dump($payout->get_implied_probabilities()); // [18.18, 68.03, 16.67]
```
If we sum all of them we will have a remaining percentage, that's the overround.

```php
// [18.18 + 68.03 + 16.67] = 102.88
// 102.88 - 100 = Overround
print  array_sum($payout->get_implied_probabilities()); // 102.88
print  $payout->get_overround(); // 2.88
```

We may get at some point a negative result for the overround. That means we have found a surebet!

To get the payout, we simply subtract the overround to 100. This can be done using the following function:
```php
print  $payout->get_payout(); // 97.12
```

### 2.3 Calculate the real probabilities
Once we have the payout, we can also calculate the real probabilities for a set of odds. Following the example before, to calculate the real probabilities use this formula:

```php
// Formula: 100 / [Odd * (Payout / 100)] = real probability
$payout->get_real_probabilities();
```

## 3. Surebets
We can check for a surebet given a odds set. Remember that you will need at least 2 different odds bookmarkers.

```php
$surebet = new Surebets($group)->is_surebet();
print $surebet; // false
```

The next example is a real surebet case. As you might have noticed the odds are provided by different bookies.
![Surebet example](https://es.surebet.com/ess/wiki/chelseamu.png)

```php
$market = new Odds()->add('decimal', 2.3)->add('decimal', 3.3)->add('decimal', 3.97);
$surebet = new Surebets($market);

if($surebet->is_surebet()){
	print 'Surebet with '.($surebet->profit()).'% profit!';
}else{
	print 'No surebet found!';
}
```