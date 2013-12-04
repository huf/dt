<?php

// TODO:
// tests, especially around fucking dst and leap seconds and such
// determine the time range this shit works on
// comparison stuff?
// +/- on dates?

class dt {
	public 
		$sec,
		$min,
		$hour,
		$mday,
		$mon,
		$year,
		$wday,
		$yday,
		$tz;

	private function __construct($vals, $tz) {
		$this->sec = $vals[0];
		$this->min = $vals[1];
		$this->hour = $vals[2];
		$this->mday = $vals[3];
		$this->mon = 1 + $vals[4];
		$this->year = 1900 + $vals[5];
		$this->wday = $vals[6];
		$this->yday = $vals[7];
		$this->tz = isset($tz) ? $tz : date_default_timezone_get();
	}

	private static function _set_tz($tz = null) {
		static $prev_tz = null;
		if ($tz === null
			&& $prev_tz !== null
			&& $prev_tz !== date_default_timezone_get())
		{
			date_default_timezone_set($prev_tz);
		}
		else if ($tz !== null && $tz !== date_default_timezone_get()) {
			$prev_tz = date_default_timezone_get();
			date_default_timezone_set($tz);
		}
	}

	public static function strftime($fmt, $dt = null) {
		$ts = self::mktime($dt);
		$dt && self::_set_tz($dt->tz);
		$str = strftime($fmt, $ts);
		$dt && self::_set_tz();
		return $str;
	}

	public static function strptime($fmt, $dts, $tz = null) {
		$tz && self::_set_tz($tz);
		$l = strptime($dts, $fmt);
		$tz && self::_set_tz();

		if ($l === false) {
			return false;
		}
		return new self(array_values($l), $tz);
	}

	public static function mktime($dt = null) {
		if ($dt === null) {
			return time();
		}

		self::_set_tz($dt->tz);
		$ts = mktime($dt->hour, $dt->min, $dt->sec,
			$dt->mon, $dt->mday, $dt->year,
			-1);
		self::_set_tz();
		return $ts;
	}

	public static function localtime($ts = null, $tz = null) {
		if ($ts === null) {
			$ts = time();
		}
		$tz && self::_set_tz($tz);
		$dt = new self(localtime($ts), $tz);
		$tz && self::_set_tz();
		return $dt;
	}

	public static function convert_tz($dt, $to) {
		return self::localtime(self::mktime($dt), $to);
	}

	public function tz($tz = null) {
		$ptz = $this->tz;
		if ($tz !== null && $tz !== $ptz) {
			foreach (self::convert_tz($this, $tz) as $k => $v) {
				$this->$k = $v;
			}
		}
		return $ptz;
	}

	public function ts() {
		return self::mktime($this);
	}
}

