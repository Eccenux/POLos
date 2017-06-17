<?php
	/**
		@file Parser data.
	*/
	$columnParsers = array();
	// must be a number higher then 0
	$columnParsers['invites_no'] = function($name, $value){
		//return CsvParser::parseColumnInteger($name, $value);
		$number = intval($value, 10);
		return array(
			'state' => is_numeric($value) && ($number > 0) ? CsvRowState::OK : CsvRowState::INVALID,
			'columns' => array(
				$name => $number,
			)
		);
	};
	$columnParsers['age_min_max'] = function($name, $value){
		return CsvParser::parseColumnRange('age_', $value);
	};
	$columnParsers['region'] = $columnParsers['group_name'] = function($name, $value){
		return CsvParser::parseColumnRequired($name, $value);
	};
