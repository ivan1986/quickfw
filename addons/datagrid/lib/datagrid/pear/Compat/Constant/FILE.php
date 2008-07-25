<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: FILE.php,v 1.4 2004/06/12 06:53:00 aidan Exp $
//


/**
 * Replace filesystem constants
 * 
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/ref.filesystem
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.4 $
 * @since       PHP 5
 */
defined('FILE_USE_INCLUDE_PATH')    or define('FILE_USE_INCLUDE_PATH',        1);
defined('FILE_IGNORE_NEW_LINES')    or define('FILE_IGNORE_NEW_LINES',        2);
defined('FILE_SKIP_EMPTY_LINES')    or define('FILE_SKIP_EMPTY_LINES',        4);
defined('FILE_APPEND')              or define('FILE_APPEND',                  8);
defined('FILE_NO_DEFAULT_CONTEXT')  or define('FILE_NO_DEFAULT_CONTEXT',      16);
?>