<?php

// set error reporting high
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// make sure current directory and class directories are on include path
// this is necessary for auto load to work
set_include_path(
	// distribution files (where the zip / tgz is unpacked)
	dirname(dirname(__FILE__)) . PATH_SEPARATOR .

	// test file directory "tests"
	dirname(__FILE__) . PATH_SEPARATOR .

	// current include path (for PHPUnit, etc.)
	get_include_path()
);

// set up an autoload for Zend / Pear style class loading
spl_autoload_register(create_function('$class', '@include(str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php");'));