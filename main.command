#!/usr/bin/php
<?php
/**
 *  main.command

 *  Created by Karl Holz on 2016-03-18.
 *  Copyright © 2016 Karl Holz. All rights reserved.

 */


/**
 * Basic Debug
 */
require 'ShellActionTools.class.php';

$s=new ShellActionTools();
$s->debug=TRUE;

$s->debug();




exit();
?>