<?php
$currentRoot = dirname(__FILE__);
require_once $currentRoot.'/dbbase.class.php';

/**
 * Profile data class
 *
 * @note Connection need to be established before methods of this class are used.
 */
class dbProfile extends dbBaseClass
{
	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_tableName = 'profile';

	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_defaultOrderSql = 'ORDER BY dt';

	/**
	 * @see dbBaseClass
	 * @var array
	 */
	protected $pv_aliasNames2colNames = array (
		'id' => 'id',
		'dt' => 'dt',
		'ankieta_id' => 'ankieta_id',
		'grupa' => 'grupa',
		
		'miejsce' => 'miejsce',
		'plec' => 'plec',
		'rok' => 'rok',
		'wiek' => '2017 - rok',
		'wyksztalcenie' => 'wyksztalcenie',
	);

	/**
	 * "Szablony" SQL do statystyk/szybkich zapytań.
	 *
	 * @see dbBaseClass->pf_getStats
	 * @var array
	 */
	protected $pv_sqlStatsTpls = array (
		// Zbiorcze podsumowanie
		'miejsce' =>
			'SELECT miejsce, count(miejsce) as licznik
			FROM profile
			WHERE {pv_constraints|(1)}
			GROUP BY miejsce
			ORDER BY 1, 2',
		'wyksztalcenie' =>
			'SELECT wyksztalcenie, count(wyksztalcenie) as licznik
			FROM profile
			WHERE {pv_constraints|(1)}
			GROUP BY wyksztalcenie
			ORDER BY 1, 2',
		'plec' =>
			'SELECT plec as `płeć`, count(plec) as licznik
			FROM profile
			WHERE {pv_constraints|(1)}
			GROUP BY plec
			ORDER BY 1, 2',
		'wiek' =>
			'
			(
			SELECT \'18-24\' as wiek, count(*) as licznik
			FROM profile
			WHERE {pv_constraints|(1)} AND (2017 - rok <= 24)
			)
			UNION
			(
			SELECT \'25-39\', count(*) as licznik
			FROM profile
			WHERE {pv_constraints|(1)} AND (2017 - rok >=25 AND 2017 - rok <= 39)
			)
			UNION
			(
			SELECT \'40-64\', count(*) as licznik
			FROM profile
			WHERE {pv_constraints|(1)} AND (2017 - rok >=40 AND 2017 - rok <= 64)
			)
			UNION
			(
			SELECT \'65+\', count(*) as licznik
			FROM profile
			WHERE {pv_constraints|(1)} AND (2017 - rok >=65)
			)
		'
	);

	/**
	 * Grupy/statusy
	 * @var array
	 */
	public static $pv_grupy = array (
		'w puli', 'grupa główna', 'zastępcza', 'rezerwowa', 'rez.zast.', 'rezygnacja'
	);

	public static function pf_wyksztalcenieTranslate($dbValue) {
		switch ($dbValue)
		{
			 case 'p': return "podstawowe";
			 case 's': return "średnie";
			 case 'w': return "wyższe";
		}
		return $dbValue;
	}
}