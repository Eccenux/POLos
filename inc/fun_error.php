<?
/* --------------------------------------------------------- *\
	Plik: fun_error
	
	Zbiór funkcji do przetwarzania/obsługiwania błędów.
	
	Użycie
		Wywołania błędów
		trigger_error (string error_msg, int error_type)
		* Dla SQL
			trigger_error ('sql:'.$zapytanie, E_USER_ERROR);
		* Inne (DEBUG_MODE = 1)
			trigger_error ('tekst błędu', E_USER_ERROR);
			trigger_error ('tekst błędu', E_USER_WARNING);
			trigger_error ('tekst błędu', E_USER_NOTICE);
		* Testowe wyswietlanie zawrtosci zmiennych
			trigger_error (myVarDump($zmienna), E_USER_NOTICE);
\* --------------------------------------------------------- */

/*
*/

/*
	Stałe wewnętrzne
*/
//
// Parametry pliku do wyświetlania
define ('HANDLER_ERR_LOG', './.err.log');	// scieżka
define ('MAX_LOG_SIZE', 4194304);			// maksymalna wielkosc (tu 4MB)
//
// Tryb debugowania
define ('DEBUG_MODE', 0);	// 1 - ON, 0 - OFF
// Tryb pomocniczy ("chowa" komunikaty)
define ('SILENT_DEBUG_MODE', 0);	// 1 - ON, 0 - OFF
//
//define ('PHP_MANUAL_PATH', 'file:///home/ewa/Documents/prog/php-manual/');
ini_set('docref_root', '/test/php_man/pl/');
ini_set('docref_ext', '.html');

/* --------------------------------------------------------- *\
	Funkcja: myVarDump
	
	Zwraca lub wyswietla sformatowany HTML-owo 
	zrzut wartosci zmiennej (także tablicy)
	
	Parametry:
		$var - zmienna do wyswietlenia
		$return - domyslnie true
			true - kod HTML zostanie zwrócony jako wartosc
			false - kod HTML zostanie wyświetlony (zwykłe echo)
\* --------------------------------------------------------- */
function myVarDump($var, $return = true)
{
	$txt = '<div style="white-space: pre">'. wordwrap(htmlspecialchars(var_export($var,true)),90) .'</div>';
	if ($return)
		return $txt;
	else
		echo $txt;
}

/* --------------------------------------------------------- *\
	Funkcja: init_myErrorHandler
	
	Inicjowanie obsługi błędów
\* --------------------------------------------------------- */
function init_myErrorHandler()
{
	global $debug_msgtext;
	$debug_msgtext = '';
	error_reporting(E_ALL);
	set_error_handler('myErrorHandler');
}

/* --------------------------------------------------------- *\
	Funkcja: myErrorHandler
	
	Funkcja do obsługi błędów. Wywołanie opisane wczesniej.
	
	Opis ogólny i parametrów na stronie:
	http://pl.php.net/manual/pl/function.set-error-handler.php
	
	Parametry (globalne):
		$debug_msgtext - zmienna przechowująca dotychczasowe błędy 
			"zerowana" przy inicjalizacji (fun. init_myErrorHandler)
\* --------------------------------------------------------- */
function myErrorHandler($errno, $errmsg, $filename, $linenum)
{
	global $debug_msgtext;

	$done = false;
	//
	// Special errors handling
	//
	if ($errno == E_USER_ERROR ||
		$errno == E_USER_WARNING ||
		$errno == E_USER_NOTICE)
	{
		//
		// Check for prefix
		//
		if (strpos($errmsg, 'sql:') === 0)
		{
			substr($errmsg, 4);
			$errmsg = myErrorHandler_sql($matches[2], $filename, $linenum);
			if ($errno == E_USER_ERROR)
			{
				printout_html_msg ($errmsg);
			}
			else
			{
				$debug_msgtext .= $errmsg;
				$done = true;
			}
		}
	}
	//
	// Standard error handling
	//
	if (DEBUG_MODE && !$done)
	{
		$new_err_msg = myErrorHandler_std($errno, $errmsg, $filename, $linenum);

		$debug_msgtext .= $new_err_msg;
	}
	// tylko zapis do pliku
	else if (!$done)
	{
		myErrorHandler_std($errno, $errmsg, $filename, $linenum);
	}
}

/* --------------------------------------------------------- *\
	Funkcja: myErrorHandler_sql
	
	Zwraca sformatowany HTML-owo kod błędu i zapisuje 
	poufne dane do pliku (stała: HANDLER_ERR_LOG).
	
	Parametry:
		$sql - kod zapytania SQL (które wywołało bład)
		$err_file_name - nazwa pliku, w którym wystapił bład
		$err_line_num - numer linii pliku, w miejscu wystapienia błędu
\* --------------------------------------------------------- */
function myErrorHandler_sql ($sql, $err_file_name, $err_line_num)
{
	global $db;
/*
	global $HTTP_COOKIE_VARS;

	if ($HTTP_COOKIE_VARS["blad_sql_wystapil"]!=1) {
		$err_msg = str_replace('Something is wrong in your syntax', 'Błąd w składni', mysql_error());
	
		$wnetrze_msgtext = "Nieprawidłowe zapytanie: \n". $sql;
		$wnetrze_msgtext.= "\n".'Błąd (' . mysql_errno(). '): '. $err_msg;

		emalia('egil@wp.pl', "Błąd SQL", $wnetrze_msgtext, 'viking@megapolis.pl');

	}
	setcookie ("blad_sql_wystapil", 1);
	
	return $wnetrze_msgtext;
*/
	$err_log_file = HANDLER_ERR_LOG;
	if (@filesize($err_log_file)<1024*1024) {
/* 		echo "<br>-".filesize($err_log_file)."-<br>"; */
		$err_msg = str_replace('Something is wrong in your syntax', 'Błąd w składni', $db->error());
	
		$log_msg = "\n--------------------------------\n ". date('D d.m.Y H:i:s (T)') .  "\n $err_line_num - $err_file_name\n--------------------------------\n";
		$log_msg .= "Nieprawidłowe zapytanie: \n". $sql;
		$log_msg.= "\nBłąd (" . mysql_errno(). "): $err_msg\n";
		@error_log ($log_msg, 3, $err_log_file);
	}

	if (DEBUG_MODE)
	{
		return '<div><pre>' .htmlspecialchars($log_msg). '</pre></div>';
	}
	else
	{
		return '<div class="mymsgdie"><b>Wystąpił błąd bazy danych!</b><br />Jeśli to się powtórzy, to prosimy o kontakt przez e-mail.</div>';
	}
}

/* --------------------------------------------------------- *\
	Funkcja: myErrorHandler_std
	
	Zwraca sformatowany HTML-owo kod błędu i zapisuje 
	poufne dane do pliku (stała: HANDLER_ERR_LOG).
	
	Parametry:
		jak w fun. myErrorHandler
\* --------------------------------------------------------- */
function myErrorHandler_std ($errno, $errmsg, $err_file_name, $err_line_num)
{
	//
	// translation array
	//	
	$errortype = array (
		E_WARNING		=> 'Warning',
		E_NOTICE		=> 'Notice',
		E_USER_ERROR	=> 'User Error',
		E_USER_WARNING	=> 'User Warning',
		E_USER_NOTICE	=> 'User Notice',
	);
	if (!isset($errortype[$errno]))
	{
		$errortype[$errno] = 'Unknown';
	}

	//
	// debug_msgtext
	//
	$file_name = basename($err_file_name);
	$dir_name = dirname($err_file_name);
	$debug_msgtext = "<div>
		<b>{$errortype[$errno]}</b> ($errno): $errmsg<br/>
		In [$dir_name/<b>$file_name</b>] at line ($err_line_num)";
	
	/***
	// get file's lines
	$handle = fopen($err_file_name, 'r');
	$i = 0;
	$file_lines = '';
	while (!feof($handle)) {
		$line = fgets($handle, 4096);
		$i++;
		switch($err_line_num-$i)
		{
			case -1:
			case 1:
				$file_lines .= $line;
			break;
			case 0:
				$file_lines .= rtrim($line). "// ($err_line_num)\n";
			break;
		}
	}
	$debug_msgtext .= '<br/>'.highlight_string ($file_lines, TRUE);
	fclose($handle);
	/**/
	
	// close debug div
	$debug_msgtext .=  "</div>\n\n";

	/**/
	if (@filesize(HANDLER_ERR_LOG) < MAX_LOG_SIZE)
	{
		$log_debug_msgtext = "\n----------------------------------------------------\n ".date("Y-m-d H:i:s (T)")."\n {$errortype[$errno]}($errno): $errmsg\n In [$err_file_name] at line ($err_line_num) \n----------------------------------------------------";
		error_log ($log_debug_msgtext, 3, HANDLER_ERR_LOG);
	}
	/**/
	
	return $debug_msgtext;
}

?>