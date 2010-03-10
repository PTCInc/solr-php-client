#! /usr/bin/php
<?php

// make sure the working directory is correct (parent directory)
// phpunit will use it to include our configuration files and find
// the following specified test suite
chdir(dirname(__FILE__));

// run phpunit - will automatically use ./phpunit.xml for configuration
// that configuration file points to a bootstrap that will include our test suite
passthru("phpunit Apache_Solr_TestAll");

// extra newline so our next prompt isn't stuck appended to the output
echo "\n";