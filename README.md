twig-gettext-parser [![Build Status](https://travis-ci.org/micmia/twig-gettext-parser.png)](https://travis-ci.org/micmia/twig-gettext-parser)
===================
Twig Gettext Parser is able to extract the translation strings from twig templates for Poedit.

Installation
------------
Install [Composer](http://getcomposer.org/) and run the following command: `php composer.phar install`.

Configuration
-------------
Open Poedit, create a new source code parser (Preferences > Parsers) with the following parameters:
- Language: `Twig`
- List of extensions separated by semicolons: `*.twig`
- Parser command: `/path/to/twig-gettext-parser --sort-output --force-po -o %o %C %K -L PHP --files %F`
- An item in keywords list: `-k%k`
- An item in input files list: `%f`
- Source code charset: `--from-code=%c`
