<?php
/**
 * Template helper for modules.
 * 
 * @author Maciej Nux Jaros
 */
class ModuleTemplate
{
	/**
	 * Set to template file name.
	 *
	 * This is a path relative to module dir.
	 *
	 * @var string
	 */
	public $file = '';
	/**
	 * HTML message to be displayed INSTEAD of the file.
	 * 
	 * @var string
	 */
	public $message = '';
	/**
	 * Data for the template (use in template as $tplData)
	 * @var string
	 */
	public $data = '';

	/**
	 * Extra tags (HTML) to be appended to head.
	 * @var string
	 */
	public $extraHeadTags = '';

	/**
	 * @var string
	 */
	private $moduleDir;

	/**
	 * @param string $moduleDir Set from ModuleController.
	 */
	public function __construct($moduleDir)
	{
		$this->moduleDir = $moduleDir;
	}

	/**
	 * Render (echo) the message or the file.
	 */
	public function render()
	{
		if (empty($this->message))
		{
			if (!empty($this->file))
			{
				$tplData = $this->data;
				include $this->moduleDir.'/'.$this->file;
				unset($tplData);
			}
		}
		else
		{
			echo $this->message;
		}
	}

	/**
	 * Set HTTP response status code.
	 *
	 * @param type $httpStatusCode Status to set.
	 */
	public function setResponseCode($httpStatusCode)
	{
		if (!function_exists('http_response_code')) {
			self::http_response_code_compat($httpStatusCode);
		} else {
			http_response_code($httpStatusCode);
		}
	}

	/**
	 * Set HTTP response status code.
	 *
	 * @param type $httpStatusCode Status to set.
	 */
	public function getResponseCode()
	{
		if (!function_exists('http_response_code')) {
			return self::http_response_code_compat();
		} else {
			return http_response_code();
		}
	}

	/**
	 * Replacement for `http_response_code`.
	 *
	 * `http_response_code` is availbale from PHP 5.4.
	 */
	private static function http_response_code_compat($code = NULL) {
		if ($code !== NULL) {

			switch ($code) {
				case 100: $text = 'Continue'; break;
				case 101: $text = 'Switching Protocols'; break;
				case 200: $text = 'OK'; break;
				case 201: $text = 'Created'; break;
				case 202: $text = 'Accepted'; break;
				case 203: $text = 'Non-Authoritative Information'; break;
				case 204: $text = 'No Content'; break;
				case 205: $text = 'Reset Content'; break;
				case 206: $text = 'Partial Content'; break;
				case 300: $text = 'Multiple Choices'; break;
				case 301: $text = 'Moved Permanently'; break;
				case 302: $text = 'Moved Temporarily'; break;
				case 303: $text = 'See Other'; break;
				case 304: $text = 'Not Modified'; break;
				case 305: $text = 'Use Proxy'; break;
				case 400: $text = 'Bad Request'; break;
				case 401: $text = 'Unauthorized'; break;
				case 402: $text = 'Payment Required'; break;
				case 403: $text = 'Forbidden'; break;
				case 404: $text = 'Not Found'; break;
				case 405: $text = 'Method Not Allowed'; break;
				case 406: $text = 'Not Acceptable'; break;
				case 407: $text = 'Proxy Authentication Required'; break;
				case 408: $text = 'Request Time-out'; break;
				case 409: $text = 'Conflict'; break;
				case 410: $text = 'Gone'; break;
				case 411: $text = 'Length Required'; break;
				case 412: $text = 'Precondition Failed'; break;
				case 413: $text = 'Request Entity Too Large'; break;
				case 414: $text = 'Request-URI Too Large'; break;
				case 415: $text = 'Unsupported Media Type'; break;
				case 500: $text = 'Internal Server Error'; break;
				case 501: $text = 'Not Implemented'; break;
				case 502: $text = 'Bad Gateway'; break;
				case 503: $text = 'Service Unavailable'; break;
				case 504: $text = 'Gateway Time-out'; break;
				case 505: $text = 'HTTP Version not supported'; break;
				default:
					exit('Unknown http status code "' . htmlentities($code) . '"');
				break;
			}

			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

			header($protocol . ' ' . $code . ' ' . $text);

			$GLOBALS['http_response_code'] = $code;

		} else {
			$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
		}
		return $code;
	}

	/**
	 * Formats number for display.
	 * @param float $number
	 * @param int $decimals
	 */
	public static function formatNumber($number, $decimals = 0)
	{
		return number_format($number, $decimals, ",", " ");
	}

	/**
	 * Prints typical key->value array.
	 *
	 * @param array $rows
	 * @param array $columnToAliasArray An array for alias translation:
	 *		array('id' => 'l.p.'),
	 *		This should work as if SELECT statement contained: `id as "l.p."`
	 * @param array $columnExtraTransformationArray może wyglądać np. tak:
	 *		array('id' => function($value){ return parseEmails($value); }),
	 */
	public static function printArray($rows, $columnToAliasArray = array(), $columnExtraTransformationArray = array())
	{
		if (empty($rows))
		{
			//! @todo i18n
			echo 'brak danych do wy&#347;wietlenia';
			return;
		}

		echo '<table class="sortable">';

		//
		// nagłówek
		echo '<tr>';
		foreach ($rows as &$row)
		{
			foreach ($row as $column=>&$value)
			{
				if (!empty($columnToAliasArray) && !empty($columnToAliasArray[$column]))
				{
					$column = $columnToAliasArray[$column];
				}

				if ($column!='row_class')
				{
					echo '<th>'.$column.'</th>';

					$dateRows[$column] = (preg_match('/^[dD]ata\b/', $column)) ? true : false;
				}
			}
			break;
		}
		echo "</tr>\n";

		//
		// wnętrze
		foreach ($rows as &$row)
		{
			if (!empty($row['row_class']))
			{
				echo "\n".'<tr class="'.$row['row_class'].'">';
				unset($row['row_class']);
			}
			foreach ($row as $column=>&$value)
			{
				$originalName = $column;
				if (!is_array($value))
				{
					if (!empty($columnToAliasArray) && !empty($columnToAliasArray[$column]))
					{
						$column = $columnToAliasArray[$column];
					}

					if (isset($columnExtraTransformationArray[$originalName]))
					{
						echo '<td>'. $columnExtraTransformationArray[$originalName]($value) .'</td>';
					}
					else if ($dateRows[$column] && !empty($value))
					{
						echo '<td>'. date('Y-m-d H:i:s', $value) .'</td>';
					}
					else
					{
						echo '<td>'.strtr($value,array('  '=>' &nbsp;',"\n"=>'<br />')).'</td>';
					}
				}
				else
				{
					echo '<td>';
					pf_printArray($value);
					echo '</td>';
				}
			}
			echo '</tr>';
		}

		//
		// koniec
		echo '</table>';
	}
}
