<?php
/**
 * Various file&directory helpers.
 */
class DirHelper
{
	/**
	 * Secure string sent be a user for usage in a file name.
	 * @param string $name
	 * @return string
	 */
	public static function secureFileName($name)
	{
		$f = trim($name, './\\');
		$f = strtr($f, array('\\'=>'_', '/'=>'__', '..'=>'_', ':'=>'_'));
		return $f;
	}
	/**
	 * Secure string sent be a user for usage in as path (part of the path).
	 * @param string $path
	 * @return string
	 */
	public static function secureFilePath($path)
	{
		$f = trim($path, './\\');
		$f = strtr($f, array('\\'=>'_', '..'=>'_', ':'=>'_'));
		return $f;
	}

	/**
	 * Gets subdirectories names.
	 * @param type $path
	 * @param type $ignoreArray
	 * @return array Names of subdirs (or an empty array).
	 */
	public static function getSubdirectories($path, $ignoreArray = array('.', '..'))
	{
		$ret = array();
		$d = dir($path);
		while (false !== ($pv_entry = $d->read()))
		{
			if (!in_array($pv_entry, $ignoreArray) && is_dir(rtrim($path, '/').'/'.$pv_entry))
			{
				$ret[] = $pv_entry;
			}
		}
		$d->close();

		return $ret;
	}
}
