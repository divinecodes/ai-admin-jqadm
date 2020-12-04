<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


/*
 * Set error reporting to maximum
 */
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set( 'display_errors', '1' );

date_default_timezone_set( 'UTC' );

/*
 * Set locale settings to reasonable defaults
 */
setlocale( LC_ALL, 'en_US.UTF-8' );
setlocale( LC_NUMERIC, 'POSIX' );
setlocale( LC_CTYPE, 'en_US.UTF-8' );
setlocale( LC_TIME, 'POSIX' );


require_once 'TestHelperCustom.php';
\TestHelperCustom::bootstrap();
