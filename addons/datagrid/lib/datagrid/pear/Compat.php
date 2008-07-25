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
// $Id: Compat.php,v 1.11 2004/05/29 04:16:15 aidan Exp $
//


/**
 * Provides missing functionality in the form of constants and functions
 *   for older versions of PHP
 *
 * Optionally, you may simply include the file.
 *   e.g. require_once PEAR_DIR . 'PHP/Compat/Function/scandir.php';
 *
 * @category    PHP
 * @package     PHP_Compat
 * @version     0.1
 * @author      Aidan Lister <aidan@php.net>
 * @static
 */
class PHP_Compat
{

    /**
     * Load a function, or array of functions
     *
     * @param   string|array    $function The function or functions to load.
     * @return  bool|array      true if loaded, false if not
     */
    function loadFunction ($function)
    {
        if (is_array($function)) {
            $res = array ();
            foreach ($function as $singlefunc) {
                $res[] = PHP_Compat::loadFunction($singlefunc);
            }
            return $res;
        }

        else {
            if (!function_exists($function)) {
                $file = sprintf('PHP/Compat/Function/%s.php', $function);

                if ((@include_once $file) !== false) {
                    return true;
                }
            }

            return false;
        }
    }


    /**
     * Load a constant, or array of constants
     *
     * @param   string|array    $constant The constant or constants to load.
     * @return  bool|array      true if loaded, false if not
     */
    function loadConstant ($constant)
    {
        if (is_array($constant)) {
            $res = array ();
            foreach ($constant as $singleconst) {
                $res[] = PHP_Compat::loadConstant($singleconst);
            }
            return $res;
        }

        else {
            if (!defined($constant)) {
                $file = sprintf('PHP/Compat/Constant/%s.php', $constant);
                
                if ((@include_once $file) !== false) {
                    return true;
                }
            }

            return false;
        }
    }

}

?>