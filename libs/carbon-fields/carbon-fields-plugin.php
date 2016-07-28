<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require __DIR__ . '/vendor/autoload.php';
} else {
	function carbon_fields_spl_autoload_register($class) {
		$prefix = 'Carbon_Fields';
		if (stripos($class, $prefix) === false) {
			return;
		}

		$file_path = __DIR__ . '/core/' . str_ireplace('Carbon_Fields\\', '', $class) . '.php';
		$file_path = str_replace('\\', DIRECTORY_SEPARATOR, $file_path);
		include_once $file_path;
	}

	spl_autoload_register('carbon_fields_spl_autoload_register');
}

include_once __DIR__ . '/carbon-fields.php';
include_once __DIR__ . '/core/functions.php';
