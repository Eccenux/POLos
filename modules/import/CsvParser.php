<?php

/**
 * Parsing state enum.
 */
abstract class CsvParserState
{
	const UNINTIALIZED = -1;	// Not parsed yet
	const OK = 0;				// All rows are OK
	const GENERAL_ERROR = 1;	// Failed to load file or all rows are invalid
	const ROW_ERRORS = 2;		// Some row(s) are invalid
}

/**
 * States for rows (and columns).
 */
abstract class CsvRowState
{
	const OK = 0;				// All rows are OK
	const INVALID = 1;			// failed to load file or all rows are invalid
	const WARNING = 2;			// failed to load file or all rows are invalid
}

/**
 * Helper class for parsing CSV.
 *
 * @author Maciej Nux Jaros
 */
class CsvParser
{
	/**
	 * Must be larger then longest line (also including new line character)
	 * @var int
	 */
	public $maxPerLine = 2000;
	/**
	 * Name of column to be ignored.
	 * @var string
	 */
	public $ignoreName = "-";
	/**
	 * Array of functions to parse columns.
	 *
	 * See \a parseColumn() for a basic function.
	 * 
	 * @var array
	 */
	public $columnParsers = array();
	/**
	 * Messages with user-readable errors.
	 * @var array
	 */
	public $messages = array();

	/**
	 * Parsed rows.
	 * @var array
	 */
	private $rows = array();
	/**
	 * File handle.
	 * @var resource
	 */
	private $file = null;
	/**
	 * Order array with ignored columns set to "-".
	 * @var array
	 */
	public $order = array();
	/**
	 * Parsing state.
	 * @var CsvParserState
	 */
	private $state = null;

	public function __construct($path, $order)
	{
		$this->order = $order;
		if (($this->file = fopen($path, "r")) === FALSE) {
			$this->messages[] = "Nie udało się otworzyć pliku. Błąd dostępu lub plik nie istnieje.";
			$this->state = CsvParserState::GENERAL_ERROR;
		}
	}

	/**
	 * Parse file.
	 * 
	 * If an error occurs see \a messages for details.
	 *
	 * @return CsvParserState.
	 */
	public function parse()
	{
		if ($this->state === CsvParserState::GENERAL_ERROR) {
			return $this->state;
		}
		$handle = $this->file;
		while (($data = fgetcsv($handle, $this->maxPerLine)) !== FALSE) {
			return $this->parseRow($data);
		}
		fclose($handle);
		$this->state = CsvParserState::OK;
		return $this->state;
	}

	public function parseRow($data)
	{
		$count = count($data);
		if ($count > count($this->order)) {
			$count = count($this->order);
		}

		$row_state = CsvRowState::OK;
		$row_columns = array();
		for ($c=0; $c < $count; $c++) {
			$name = $this->order[$c];
			$value = $data[$c];
			if (isset($this->columnParsers[$name]) && is_callable($this->columnParsers[$name])) {
				$parsed = $this->columnParsers[$name]($name, $value);
			} else {
				$parsed = $this->parseColumn($name, $value);
			}
			$row_columns = array_merge($row_columns, $parsed['columns']);
			$row_state &= $parsed['state'];
        }
		
		return array(
			'state' => $row_state,
			'columns' => $row_columns
		);
	}

	public function parseColumn($name, $value)
	{
		return array(
			'state' => CsvRowState::OK,
			'columns' => array(
				$name => $value,
			)
		);
	}
}