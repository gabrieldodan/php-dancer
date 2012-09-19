<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/* system directory */
define("SYS_DIR", "system"); 

/* application directory */
define("APP_DIR", "application");

/* define a constant for preventing direct access for scripts */
define("SECURITY_CONST", "1");

/* require sys class  */
require_once SYS_DIR . '/core/Sys.php';

/* run */
Sys::run();
?>