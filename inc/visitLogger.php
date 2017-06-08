<?php
/**
 * Logger for regiring visits.
 */
class VisitLogger
{
	const FILE_PATH = './.vists.log';
	const SEPARATOR = "\t";
	const MAX_REQUEST_VALUE = 100;

	/**
	 * Get current base origin.
	 * @param array $s $_SERVER or euqivalent.
	 * @param boolean $use_forwarded_host
	 * @return string
	 */
	private static function baseUrl($use_forwarded_host = false )
	{
		$s = $_SERVER;
		$ssl      = self::isSecure();
		$sp       = strtolower( $s['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port     = $s['SERVER_PORT'];
		$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
		$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) )
			? $s['HTTP_X_FORWARDED_HOST']
			: ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null )
		;
		$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
		return $protocol . '://' . $host;
	}

	/**
	 * Check is HTTPS is used.
	 * @return boolean
	 */
	public static function isSecure()
	{
		$s = $_SERVER;
		$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
		return $ssl;
	}

	/**
	 * Less strict URL encoding.
	 *
	 * Only makes sure strings are usable, don't obfucate string...
	 */
	private static function lightUrlEncode($str)
	{
		return preg_replace('#\s#', '%20', $str);
	}
	/**
	 * Encode GET/POST request table.
	 * @param array $table
	 */
	private static function encodeRequest($table)
	{
		if (empty($table)) {
			return "";
		}
		$queryString = "?";
		foreach ($table as $key => $value)
		{
			if (!is_string($value)) {
				$value = json_encode($value);
			}
			if (strlen($value) > self::MAX_REQUEST_VALUE) {
				$value = substr($value, 0, self::MAX_REQUEST_VALUE - 1) . "…";
			}
			$queryString .= self::lightUrlEncode($key)."=".self::lightUrlEncode($value)."&";
		}
		return rtrim($queryString, "&");
	}

	/**
	 * Gather visit data.
	 * 
	 * @param ModuleController $controller The controller.
	 * @return array
	 */
	public static function gatherData($controller)
	{
		$data = array();
		// dt
		$data[] = date('c');
		// IP
		$data[] = $_SERVER['REMOTE_ADDR'];
		// user
		$data[] = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '-';
		// base url
		$data[] = self::baseUrl();
		// script
		$data[] = $_SERVER["SCRIPT_NAME"];
		// response code
		$data[] = $controller->tpl->getResponseCode();
		// module info
		$data[] = 'MODULE'.$controller->getRawActionUrl($controller->action);
		// GET/POST
		$data[] = 'GET'.self::encodeRequest($_GET);
		$data[] = 'POST'.self::encodeRequest($_POST);
		return $data;
	}

	/**
	 * Register the visit.
	 *
	 * @param ModuleController $controller The controller.
	 */
	public static function register($controller)
	{
		$info = implode(self::SEPARATOR, self::gatherData($controller));
		file_put_contents(self::FILE_PATH, $info."\n", FILE_APPEND);
	}
}
