<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('Set')) {
    require LIBS . 'set.php';
}

class Configure {

/**
 * Array of values currently stored in Configure.
 *
 * @var array
 */
	protected static $_values = array(
		'debug' => 0
	);

/**
 * Used to store a dynamic variable in Configure.
 *
 * Usage:
 * {{{
 * Configure::write('One.key1', 'value of the Configure::One[key1]');
 * Configure::write(array('One.key1' => 'value of the Configure::One[key1]'));
 * Configure::write('One', array(
 *     'key1' => 'value of the Configure::One[key1]',
 *     'key2' => 'value of the Configure::One[key2]'
 * );
 *
 * Configure::write(array(
 *     'One.key1' => 'value of the Configure::One[key1]',
 *     'One.key2' => 'value of the Configure::One[key2]'
 * ));
 * }}}
 *
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::write
 * @param array $config Name of var to write
 * @param mixed $value Value to set for var
 * @return boolean True if write was successful
 */
	public static function write($config, $value = null) {
		if (!is_array($config)) {
			$config = array($config => $value);
		}

		foreach ($config as $name => $value) {
			if (strpos($name, '.') === false) {
				self::$_values[$name] = $value;
			} else {
				$names = explode('.', $name, 4);
				switch (count($names)) {
					case 2:
						self::$_values[$names[0]][$names[1]] = $value;
					break;
					case 3:
						self::$_values[$names[0]][$names[1]][$names[2]] = $value;
					break;
					case 4:
						$names = explode('.', $name, 2);
						if (!isset(self::$_values[$names[0]])) {
							self::$_values[$names[0]] = array();
						}
						self::$_values[$names[0]] = Set::insert(self::$_values[$names[0]], $names[1], $value);
					break;
				}
			}
		}
		return true;
	}

/**
 * Used to read information stored in Configure.  Its not
 * possible to store `null` values in Configure.
 *
 * Usage:
 * {{{
 * Configure::read('Name'); will return all values for Name
 * Configure::read('Name.key'); will return only the value of Configure::Name[key]
 * }}}
 *
 * @linkhttp://book.cakephp.org/2.0/en/development/configuration.html#Configure::read
 * @param string $var Variable to obtain.  Use '.' to access array elements.
 * @return mixed value stored in configure, or null.
 */
	public static function read($var = null) {
		if ($var === null) {
			return self::$_values;
		}
		if (isset(self::$_values[$var])) {
			return self::$_values[$var];
		}
		if (strpos($var, '.') !== false) {
			$names = explode('.', $var, 3);
			$var = $names[0];
		}
		if (!isset(self::$_values[$var])) {
			return null;
		}
		switch (count($names)) {
			case 2:
				if (isset(self::$_values[$var][$names[1]])) {
					return self::$_values[$var][$names[1]];
				}
			break;
			case 3:
				if (isset(self::$_values[$var][$names[1]][$names[2]])) {
					return self::$_values[$var][$names[1]][$names[2]];
				}
				if (!isset(self::$_values[$var][$names[1]])) {
					return null;
				}
				return Set::classicExtract(self::$_values[$var][$names[1]], $names[2]);
			break;
		}
		return null;
	}

/**
 * Used to delete a variable from Configure.
 *
 * Usage:
 * {{{
 * Configure::delete('Name'); will delete the entire Configure::Name
 * Configure::delete('Name.key'); will delete only the Configure::Name[key]
 * }}}
 *
 * @link http://book.cakephp.org/2.0/en/development/configuration.html#Configure::delete
 * @param string $var the var to be deleted
 * @return void
 */
	public static function delete($var = null) {
		if (strpos($var, '.') === false) {
			unset(self::$_values[$var]);
			return;
		}

		$names = explode('.', $var, 2);
		self::$_values[$names[0]] = Set::remove(self::$_values[$names[0]], $names[1]);
	}

	public static function load($file) {
		return self::write(@include($file));
	}
}