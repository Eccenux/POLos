<?php
$currentRoot = dirname(__FILE__);
require_once $currentRoot.'/dbbase.class.php';

/**
 * Presonal data class
 *
 * @note Connection need to be established before methods of this class are used.
 */
class dbPersonal extends dbBaseClass
{
	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_tableName = 'personal';

	/**
	 * @see dbBaseClass
	 * @var string
	 */
	protected $pv_defaultOrderSql = 'ORDER BY nazwisko, imie';

	/**
	 * @see dbBaseClass
	 * @var array
	 */
	protected $pv_aliasNames2colNames = array (
		'id' => 'id',
		'dt' => 'dt',
		'ankieta_id' => 'ankieta_id',
		
		'nazwisko_imie' => 'CONCAT(nazwisko, \', \', imie)',
		'imie' => 'imie',
		'nazwisko' => 'nazwisko',
		'nr_tel' => 'nr_tel',
		'e_mail' => 'e_mail',
	);
}

?>