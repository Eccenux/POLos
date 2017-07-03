<?php
/*
	@file Parser data.
*/
	require_once 'PeselParser.php';

	$columnParsers = array();
	$columnParsers['pesel'] = function($name, $pesel) use ($ageBaseTime) {
		$ret = array(
			'state' => CsvRowState::INVALID,
			'columns' => array(),
		);
		if (PeselParser::validatePesel($pesel)) {
			$ret['state'] = CsvRowState::OK;
			$ret['columns']['pesel'] = $pesel;
			$ret['columns']['sex'] = PeselParser::sexFromPesel($pesel);
			$ret['columns']['age'] = PeselParser::ageFromPesel($pesel, $ageBaseTime);
		}
		return $ret;
	};
	$columnParsers['region'] = function($name, $value){
		return CsvParser::parseColumnRequired($name, $value);
	};
