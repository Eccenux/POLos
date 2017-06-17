<?php

/**
 * Description of PeselParser
 *
 * @author Maciej Nux Jaros
 */
class PeselParser
{
	/**
	 * Validate PESEL.
	 * @param string $pesel
	 * @return boolean true if OK.
	 */
	static function validatePesel($pesel) {
		if (strlen($pesel) !== 11 || !preg_match('/^\d+$/', $pesel)) {
			return false;
		}
		$x = 9*$pesel[0]
		   + 7*$pesel[1]
		   + 3*$pesel[2]
		   + 1*$pesel[3]
		   + 9*$pesel[4]
		   + 7*$pesel[5]
		   + 3*$pesel[6]
		   + 1*$pesel[7]
		   + 9*$pesel[8]
		   + 7*$pesel[9];
		return $x % 10 == $pesel[10];
	}

	/**
	 * Read birth date from PESEL.
	 * @param string $pesel
	 * @return string ISO date.
	 */
	static function birthFromPesel($pesel) {
		$y = substr($pesel, 0, 2);
		$m = substr($pesel, 2, 2);
		$d = substr($pesel, 4, 2);
		$m = intval($m);
		if ($m > 80) {
			$y = "18$y";
			$m -= 80;
		} else if ($m > 60) {
			$y = "22$y";
			$m -= 60;
		} else if ($m > 40) {
			$y = "21$y";
			$m -= 40;
		} else if ($m > 20) {
			$y = "20$y";
			$m -= 20;
		} else {
			$y = "19$y";
		}
		$m = sprintf("%02d", $m);
		return "$y-$m-$d";
	}

	/**
	 * Calculate age form PESEL.
	 * @param string $pesel
	 * @return int
	 */
	static function ageFromPesel($pesel) {
		$date = new DateTime(self::birthFromPesel($pesel));
		$now = new DateTime();
		$diff = $date->diff($now);
		return $diff->y;
	}
}
