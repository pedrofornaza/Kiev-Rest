<?php

define('ROOT_PATH', realpath('./'));
define('SRC_PATH', realpath(ROOT_PATH .DIRECTORY_SEPARATOR. 'src'));
set_include_path(get_include_path() .PATH_SEPARATOR. SRC_PATH);

spl_autoload_register(function ($className) {
	$className = trim($className, '\\');

	$fileName = '';
	if ($lastSeparator = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastSeparator);
		$className = substr($className, $lastSeparator+1);

		$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}

	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if (!stream_resolve_include_path($fileName)) {
		throw new Exception("Cannot find the file (\"{$fileName}\").");
	}

	include $fileName;
});