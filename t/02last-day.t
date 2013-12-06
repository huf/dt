#!/usr/bin/env php
<?php
require_once 'testmore/testmore.php';

date_default_timezone_set('UTC');

require_once 'dt.php';

plan('no_plan');

$last = array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
$leap_last = $last;
$leap_last[1]++;

foreach (range(1, 12) as $month) {
	$dt = new dt(array(
		'year' => 2001,
		'month' => $month+1,
		'day' => 0,
		'tz' => 'UTC',
	));

    is( $dt->year,  2001,                'check year' );
    is( $dt->month, $month,              'check month' );
    is( $dt->day,   $last[ $month - 1 ], 'check day' );
}

foreach (range(1, 12) as $month) {
	$dt = new dt(array(
		'year' => 2004,
		'month' => $month+1,
		'day' => 0,
		'tz' => 'UTC',
	));

    is( $dt->year,  2004,                     'leap check year' );
    is( $dt->month, $month,                   'leap check month' );
    is( $dt->day,   $leap_last[ $month - 1 ], 'leap check day' );
}

