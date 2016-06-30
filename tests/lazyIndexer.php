<?php

$basedir = __DIR__.DIRECTORY_SEPARATOR."test_replays".DIRECTORY_SEPARATOR;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basedir));
$arr = [];

foreach ($rii as $file) {
	$arr[$file] = ["new ressources\Replay()"];
}