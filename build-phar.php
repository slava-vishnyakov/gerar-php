<?php

unlink('bin/gerar.phar');
$phar = new Phar("bin/gerar.phar", FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_FILENAME, "gerar.phar");
$phar->buildFromDirectory(__DIR__, '/\.php$/');
$phar->setStub('#!/usr/bin/env php' . PHP_EOL . $phar->createDefaultStub("gerar.php"));
//$phar->convertToExecutable();

chmod('bin/gerar.phar', 0755);