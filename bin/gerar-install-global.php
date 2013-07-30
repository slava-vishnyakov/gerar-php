<?php

print "Installing Gerar PHP globally to /usr/local/bin/gerar ...\n";

$text = file_get_contents('https://github.com/slava-vishnyakov/gerar-php/blob/master/bin/gerar.phar?raw=true');
file_put_contents('/usr/local/bin/gerar', $text);
chmod('/usr/local/bin/gerar', 0755);

print "Done\n";
