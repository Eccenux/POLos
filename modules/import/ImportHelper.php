<?php
/**
 * Import forms helper.
 */
class ImportHelper
{
	public $parser = null;
	/**
	 * Id of the saved file. Can be used as a reference.
	 * @var int
	 */
	public $fileId = null;

	/**
	 * Init.
	 * @param array $columnParsers Array of column parser functions.
	 * @param string $type Type of file ('profile' / 'personal').
	 */
	function __construct($columnParsers, $type)
	{
		$this->columnParsers = $columnParsers;
		$this->type = $type;
	}

	/**
	 * Parse and save uploaded file.
	 *
	 * Note! The file will not be save to DB if parser was not able to parse the file.
	 * That is when its state is `CsvParserState::GENERAL_ERROR`.
	 *
	 * @param array $file Uploaded file specs from `$_FILES`.
	 * @param string $order Order sent by user.
	 * @return \CsvParser
	 */
	function parse($file, $order) {
		/* @var $ticks cTicks */
		global $ticks;
		// parse file
		$csvPath = $file["tmp_name"];
		$csvOrder = explode(",", $order);
		$parser = new CsvParser($csvPath, $csvOrder);
		$parser->columnParsers = $this->columnParsers;
		$ticks->pf_insTick("parse-csv");
		$state = $parser->parse(true);
		$ticks->pf_endTick("parse-csv");
		//echo "<pre>".var_export($parser->rows, true)."</pre>";

		$ticks->pf_insTick("parse-save-file");
		if ($state !== CsvParserState::GENERAL_ERROR) {
			// save file
			require_once ('./inc/db/file.php');
			$dbFile = new dbFile();
			$this->fileId = $dbFile->pf_insRecord(array(
				'type' => $this->type,
				'column_map' => $order,
				'name' => $file['name'],
				//'contents' => file_get_contents($csvPath),
			), true);
		}
		$ticks->pf_endTick("parse-save-file");

		$this->parser = $parser;
		return $parser;
	}

	/**
	 * Standard record insert.
	 */
	static function insRecord(&$dbClass, &$record, $rowState, $fileId) {
		if ($rowState === CsvRowState::OK) {
			$record['csv_file'] = $fileId;
			if (!$dbClass->pf_insRecord($record)) {
				$rowState = CsvRowState::INVALID;	// set to invalid to re-attempt saving as invalid
			}
		}
		if ($rowState !== CsvRowState::OK) {
			$invalidRecord = array(
				'row_state' => $rowState,
				'csv_file' => $fileId,
				'csv_row' => $record['csv_row'],
			);
			return $dbClass->pf_insRecord($invalidRecord);
		}
		return true;
	}

	/**
	 * Save data.
	 *
	 * Note! The data will not be save to DB if parser was not able to parse the file.
	 * That is when its state is `CsvParserState::GENERAL_ERROR`.
	 *
	 * @param callable $insRecord Function for inserting a row record.
	 */
	function save($insRecord) {
		/* @var $ticks cTicks */
		global $ticks;
		
		$parser = $this->parser;
		$state = $parser->state;

		if ($state === CsvParserState::GENERAL_ERROR) {
			return false;
		}
		$ticks->pf_insTick("save");
		foreach ($parser->rows as $rowState => $records)
		{
			foreach ($records as $record)
			{
				if (!$insRecord($record, $rowState, $this->fileId)) {
					trigger_error("Error inserting record. Will not continue!\n". var_export($record, true));
					return false;
				}
			}
		}
		$ticks->pf_endTick("save");
		return true;
	}


	/**
	 * Checks if row count is near popular formats limitations.
	 * @param number $rowsCount
	 * @return string yes/probably/not
	 */
	private static function isSuspicious($rowsCount) {
		$files = array(
			'oldXls' => 16384,
			'newXls' => 65536,
			'xlsx' => 1048576,
		);
		foreach ($files as $limit) {
			if (abs($rowsCount - $limit) < 20) {
				if (abs($rowsCount - $limit) < 2) {
					return "yes";
				}
				return "probably";
			}
		}
		return "not";
	}

	/**
	 * Information about parsing.
	 *
	 * @return string HTML messages.
	 */
	function infoBuild($saveStatus = true) {
		$html = "";

		if (!$saveStatus) {
			$html .=
				"<div class='message error'>"
					. "Zapis danych w bazie nie powiódł się. Więcej informacji w logu błędów."
				. "</div>"
			;
		}

		$parser = $this->parser;

		// count rows
		$rowsCount = array(
			'ok' => !isset($parser->rows[CsvRowState::OK]) ? 0 : count($parser->rows[CsvRowState::OK]),
			'invalid' => !isset($parser->rows[CsvRowState::INVALID]) ? 0 : count($parser->rows[CsvRowState::INVALID]),
			'warning' => !isset($parser->rows[CsvRowState::WARNING]) ? 0 : count($parser->rows[CsvRowState::WARNING]),
		);

		// check for XLS/XLSX/ODT limits
		$total = $rowsCount['ok'] + $rowsCount['invalid'] + $rowsCount['warning'];
		$totalSuspicious = "";
		// $total = 65536; // test
		switch (self::isSuspicious($total)) {
			case "yes":
				$totalSuspicious =
					"<div class='message error'>"
						. "<p>Liczba wierszy w przesłanym pliku jest BARDZO podejrzana!"
						. "<p>Wygląda na to, że przekraczasz limit danych, "
							. "które można przechowywać w arkuszach typu Excel."
						. "<p>Prześlij brakujące dane!"
					. "</div>"
				;
			break;
			case "probably":
				$totalSuspicious =
					"<div class='message warning'>"
						. "<p>Liczba wierszy w przesłanym pliku jest dosyć podejrzana. "
						. "<p>Możliwe, że przekraczasz limit danych, "
							. "które można przechowywać w arkuszach typu Excel."
						. "<p>Zweryfikuj swój plik i w razie potrzeby prześlij brakujące dane."
					. "</div>"
				;
			break;
		}

		// state info
		switch ($parser->state) {
			case CsvParserState::GENERAL_ERROR:
				$html .=
					"<div class='message error'>"
						. "Błąd! Przetwarzanie pliku nie powiodło się. "
						. "Dane nie zostały zapisane."
					. "</div>"
				;
			break;
			case CsvParserState::OK:
				if (empty($totalSuspicious)) {
					$html .= "<div class='message success'>OK! Wszystkie dane zostały pomyślnie przetworzone.</div>";
				} else {
					$html .= "<div class='message success'>Dane zostały przetworzone, ale...</div>";
					$html .= "$totalSuspicious";
				}
			break;
			//CsvParserState::ROW_WARNINGS || case CsvParserState::ROW_ERRORS
			default:
                if ($rowsCount['warning'] + $rowsCount['invalid'] <= 2) {
					$html .= "<div class='message note'>"
						. "Nie wszystkie dane udało się przetworzyć, ale wygląda na to, że to tylko nagłówki."
					;
				} else {
					$html .= "<div class='message warning'>"
						. "Nie wszystkie dane udało się przetworzyć."
					;
				}
				$html .= ""
						. "<ul>"
							. "<li>Przetworzone dane: {$rowsCount['ok']}."
							. (empty($rowsCount['invalid']) ? '' : "<li>Niepoprawne dane: {$rowsCount['invalid']}.")
							. (empty($rowsCount['warning']) ? '' : "<li>Częściowo niepoprawne dane: {$rowsCount['warning']}.")
						. "</ul>"
						. "Dane zostały zaimportowane."
					. "</div>"
				;
				if (!empty($totalSuspicious)) {
					$html .= "$totalSuspicious";
				}
			break;
		}

		// display parser messages
		if (!empty($parser->messages)) {
			$html .= "<div class='extra-info'>";
			foreach ($parser->messages as $message)
			{
				$html .= "<div>";
				$html .= htmlspecialchars($message);
				$html .= "</div>";
			}
			$html .= "</div>";
		}

		return $html;
	}

}