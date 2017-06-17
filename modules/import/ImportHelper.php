<?php
/**
 * Import forms helper.
 */
class ImportHelper
{
	public $parser = null;

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
		// parse file
		$csvPath = $file["tmp_name"];
		$csvOrder = explode(",", $order);
		$parser = new CsvParser($csvPath, $csvOrder);
		$parser->columnParsers = $this->columnParsers;
		$state = $parser->parse(true);
		//echo "<pre>".var_export($parser->rows, true)."</pre>";

		if ($state !== CsvParserState::GENERAL_ERROR) {
			// save file
			require_once ('./inc/db/file.php');
			$dbFile = new dbFile();
			$dbFile->pf_insRecord(array(
				'type' => $this->type,
				'column_map' => $order,
				'name' => $file['name'],
				'contents' => file_get_contents($csvPath),
			));
		}

		return $parser;
	}

	/**
	 * Information about parsing.
	 *
	 * @param CsvParser $parser The parser object.
	 * @return
	 */
	function infoBuild($parser) {
		$html = "";

		// count rows
		$rowsCount = array(
			'ok' => !isset($parser->rows[CsvRowState::OK]) ? 0 : count($parser->rows[CsvRowState::OK]),
			'invalid' => !isset($parser->rows[CsvRowState::INVALID]) ? 0 : count($parser->rows[CsvRowState::INVALID]),
			'warning' => !isset($parser->rows[CsvRowState::WARNING]) ? 0 : count($parser->rows[CsvRowState::WARNING]),
		);

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
				$html .= "<div class='message success'>OK! Wszystkie dane zostały pomyślnie przetworzone.</div>";
			break;
			//CsvParserState::ROW_WARNINGS || case CsvParserState::ROW_ERRORS
			default:
				$html .= "<div class='message warning'>"
						. "Nie wszystkie dane udało się przetworzyć."
						. "<ul>"
							. "<li>Przetworzone dane: {$rowsCount['ok']}."
							. (empty($rowsCount['invalid']) ? '' : "<li>Niepoprawne dane: {$rowsCount['invalid']}.")
							. (empty($rowsCount['warning']) ? '' : "<li>Częściowo niepoprawne dane: {$rowsCount['warning']}.")
						. "</ul>"
						. "Dane zostały zaimportowane."
					. "</div>"
				;
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