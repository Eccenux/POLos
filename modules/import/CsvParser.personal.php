<?php
	/**
		@file Parser data.
	*/
	$columnParsers = array();
	$columnParsers['region'] = $columnParsers['pesel'] = function($name, $value){
		return CsvParser::parseColumnRequired($name, $value);
	};
