<?php
/**
 * Encodes numbers to string and back.
 *
 * Strings are constructed from basic ANSI letters and numbers that should be readable
 * and should not be familiar with each other (e.g. both "I" and "l" will not be used).
 *
 * Test:
 * 	$encoder = new NumericEncoder();
 * 	echo "\n".$encoder->encode(32);
 *	echo "\n".$encoder->decode($encoder->encode(32));
 *
 * @author Maciej Nux Jaros
 */
class NumericEncoder
{
	//private $str = '0123456789abcdef';	// hex
	private $str = 'wehk7rt8xc3vbn4myupad9';
	private $strlen = 0;

	public function __construct()
	{
		$this->strlen = strlen($this->str);
	}

	public function encode($number)
	{
		$strlen = $this->strlen;
		$strnum = '';
		do
		{
			$remainder = $number % $strlen;
			$strnum .= $this->str[$remainder];
			$number = ($number - $remainder) / $strlen;
		}
		while ($number > 0);
		return $strnum;
	}
	public function decode($text)
	{
		$strlen = $this->strlen;
		$number = 0;
		for ($i=strlen($text)-1; $i >= 0; $i--)
		{
			$index = strpos($this->str, $text[$i]);
			$number = ($number + $index) * $strlen;
		}
		return $number / $strlen;
	}
}
