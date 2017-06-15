<?php
	/**
		@file Parser data.
	*/
	$columnParsers = array();
	$columnParsers['invites_no'] = function($name, $value){
		return CsvParser::parseColumnInteger($name, $value);
	};
	$columnParsers['age_min_max'] = function($name, $value){
		return CsvParser::parseColumnRange('age_', $value);
	};
	$columnParsers['region'] = $columnParsers['group_name'] = function($name, $value){
		return CsvParser::parseColumnRequired($name, $value);
	};
