<?php

// TODO:
// tests, especially around fucking dst and leap seconds and such
// determine the time range this shit works on
// comparison stuff?
// +/- on dates?
// exceptions? (bad __construct call, bad strptime call)

class dt {
	const VERSION = '1.0';

	public
		$second,
		$minute,
		$hour,
		$day,
		$month,
		$year,
		$wday,
		$yday,
		$tz;

	public function __construct($params = array(), $_vals = null, $_tz = null) {
		if ($params === null
			&& $_vals
			&& is_array($_vals)) // tz is optional
		{
			// internal call
			$this->second = $_vals[0];
			$this->minute = $_vals[1];
			$this->hour = $_vals[2];
			$this->day = $_vals[3];
			$this->month = 1 + $_vals[4];
			$this->year = 1900 + $_vals[5];
			$this->wday = $_vals[6];
			$this->yday = $_vals[7];
			$this->tz = isset($_tz) ? $_tz : date_default_timezone_get();
		}
		else {
			// nice external call
			$this->second = isset($params['second']) ? $params['second'] : 0;
			$this->minute = isset($params['minute']) ? $params['minute'] : 0;
			$this->hour = isset($params['hour']) ? $params['hour'] : 0;
			$this->day = isset($params['day']) ? $params['day'] : 1;
			$this->month = isset($params['month']) ? $params['month'] : 1;
			$this->year = isset($params['year']) ? $params['year'] : 1970;
			$this->tz = isset($params['tz']) ? $params['tz'] : date_default_timezone_get();

			$this->recalc(); // to get wday and yday
		}
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
		return new self(null, array_values($l), $tz);
	}

	public static function mktime($dt = null) {
		if ($dt === null) {
			return time();
		}

		self::_set_tz($dt->tz);
		$ts = mktime($dt->hour, $dt->minute, $dt->second,
			$dt->month, $dt->day, $dt->year,
			-1);
		self::_set_tz();
		return $ts;
	}

	public static function localtime($ts = null, $tz = null) {
		if ($ts === null) {
			$ts = time();
		}
		$tz && self::_set_tz($tz);
		$dt = new self(null, localtime($ts), $tz);
		$tz && self::_set_tz();
		return $dt;
	}

	public static function convert_tz($dt, $to) {
		return self::localtime(self::mktime($dt), $to);
	}

	public function tz($tz = null) {
		$ptz = $this->tz;
		if ($tz !== null && $tz !== $ptz) {
			$this->_clobber(self::convert_tz($this, $tz));
		}
		return $ptz;
	}

	public function ts() {
		return self::mktime($this);
	}

	public function recalc() {
		$dt = self::localtime(self::mktime($this), $this->tz);
		$this->_clobber($dt);
	}

	private function _clobber($dt) {
		foreach ($dt as $k => $v) {
			$this->$k = $v;
		}
	}

	public function add($params_or_dt) {
		foreach ($params_or_dt as $k => $v) {
			switch ($k) {
			case 'second':
			case 'minute':
			case 'hour':
			case 'day':
			case 'month':
			case 'year':
				$this->$k += $v;
			}
		}
		$this->recalc();
	}
}

