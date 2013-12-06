#!/usr/bin/env php
<?php
require_once 'testmore/testmore.php';

date_default_timezone_set('UTC');

require_once 'dt.php';

plan('no_plan');

$t1 = dt::localtime(0, 'UTC');

is( $t1->second, 0,    "seconds are correct on epoch 0" );
is( $t1->minute, 0,    "minutes are correct on epoch 0" );
is( $t1->hour,   0,    "hours are correct on epoch 0" );
is( $t1->day,    1,    "days are correct on epoch 0" );
is( $t1->month,  1,    "months are correct on epoch 0" );
is( $t1->year,   1970, "year is correct on epoch 0" );

$dt = dt::localtime(3600, 'UTC');
is( $dt->ts(), 3600, 'creation test from epoch = 3600 (compare to epoch)' );

$now = time();
$nowtest = dt::localtime();
$nowtest2 = dt::localtime($now);

is( $nowtest->hour,   $nowtest2->hour,   "Hour: Create without args" );
is( $nowtest->month,  $nowtest2->month,  "Month : Create without args" );
is( $nowtest->minute, $nowtest2->minute, "Minute: Create without args" );

$epochtest = dt::localtime(997121000);

is( $epochtest->ts(), 997121000, "epoch method returns correct value" );
is( $epochtest->hour, 18, "hour" );
is( $epochtest->minute,  3,  "minute" );

$dt = dt::localtime(3600);
$dt->tz('Europe/Budapest');

is( $dt->ts(), 3600, 'epoch is 3600' );
is( $dt->hour,  2,    'hour is 2' );

$dt = new dt(array(
	'year'      => 1970,
	'month'     => 1,
	'day'       => 1,
	'hour'      => 0,
	'tz' => 'Atlantic/Azores',
));

is( $dt->ts(), 3600, 'epoch is 3600' );

$dt = dt::localtime(0, 'Atlantic/Azores');

is( $dt->ts(),  0,     'epoch is 0' );


$expected = 1049160602;
$epochtest = dt::localtime($expected);

is( $epochtest->ts(), $expected, "epoch method returns correct value ($expected)");
is( $epochtest->hour, 1,  "hour" );
is( $epochtest->minute,  30, "minute" );

$epochtest->hour += 2;
$expected += 2 * 60 * 60;

is( $epochtest->hour, 3, "adjusted hour" );
is( $epochtest->ts(), $expected, "epoch method returns correct adjusted value ($expected)");


is( (new dt(array('year' => 1904)))->ts(), -2082844800, "epoch should work back to at least 1904");

$dt = dt::localtime(-2082844800);
is( $dt->year,  1904, 'year should be 1904' );
is( $dt->month, 1,    'month should be 1904' );
is( $dt->day,   1,    'day should be 1904' );

/*
{
    for my $pair (
        [ 1   => -62135596800 ],
        [ 99  => -59042995200 ],
        [ 100 => -59011459200 ],
        [ 999 => -30641760000 ],
        ) {

        my ( $year, $epoch ) = @{$pair};

        is(
            DateTime->new( year => $year )->epoch, $epoch,
            "epoch for $year is $epoch"
        );
    }
}

{

    package Number::Overloaded;
    use overload
        "0+" => sub { $_[0]->{num} },
        fallback => 1;

    sub new { bless { num => $_[1] }, $_[0] }
}

{
    my $time = Number::Overloaded->new(12345);

    my $dt = DateTime->from_epoch( epoch => $time );
    is( $dt->epoch, 12345, 'can pass overloaded object to from_epoch' );

    $time = Number::Overloaded->new(12345.1234);
    $dt = DateTime->from_epoch( epoch => $time );
    is( $dt->epoch, 12345, 'decimal epoch in overloaded object' );
}

{
    my $time = Number::Overloaded->new(-12345);
    my $dt = DateTime->from_epoch( epoch => $time );

    is( $dt->epoch, -12345, 'negative epoch in overloaded object' );
}

{
    my @tests = (
        'asldkjlkjd',
        '1234 foo',
        'adlkj 1234',
    );

    for my $test (@tests) {
        eval { DateTime->from_epoch( epoch => $test ); };

        like(
            $@, qr/did not pass regex check/,
            qq{'$test' is not a valid epoch value}
        );
    }
}

done_testing();*/
