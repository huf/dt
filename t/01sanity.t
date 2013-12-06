#!/usr/bin/env php
<?php
require_once 'testmore/testmore.php';

date_default_timezone_set('UTC');

require_once 'dt.php';

plan('no_plan');

$dt = new dt(array(
	'year' => 1870,
	'month' => 10,
	'day' => 21,
	'hour' => 12,
	'minute' => 10,
	'second' => 45,
	'tz' => 'UTC'
));

is($dt->year, '1870', "Year accessor, outside of the epoch");
is($dt->month, '10', "Month accessor, outside the epoch");
is($dt->day, '21', "Day accessor, outside the epoch");
is($dt->hour, '12', "Hour accessor, outside the epoch");
is($dt->minute, '10', "Minute accessor, outside the epoch");
is($dt->second, '45', "Second accessor, outside the epoch");

