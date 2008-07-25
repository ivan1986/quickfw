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
// $Id: array_chunk.php,v 1.8 2004/06/12 07:06:41 aidan Exp $
//


/**
 * Replace array_combine()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_chunk
 * @author      Aidan Lister <aidan@php.net>
 * @author      Thiemo Mättig <thiemo.maettig@gmx.de>
 * @version     $Revision: 1.8 $
 * @since       PHP 4.2.0
 */
if (!function_exists('array_chunk'))
{
    function array_chunk ($input, $size, $preserve_keys = false)
    {
        if (!is_array($input)) {
            trigger_error('array_chunk() expects parameter 1 to be array, ' . gettype($input) . ' given', E_USER_WARNING);
            return null;
        }
		
        if (!is_numeric($size)) {
            trigger_error('array_chunk() expects parameter 2 to be long, ' . gettype($size) . ' given', E_USER_WARNING);
            return null;
        }
		
        $size = (int)$size;
        if ($size <= 0)
        {
            trigger_error('array_chunk() Size parameter expected to be greater than 0', E_USER_WARNING);
            return null;
        }

        $chunks = array();
        $i = 0;

        if ($preserve_keys !== false)
        {
            foreach ($input as $key => $value) {
                $chunks[(int)($i++ / $size)][$key] = $value;
            }
        }
        else
        {
            foreach ($input as $value) {
                $chunks[(int)($i++ / $size)][] = $value;
            }
        }

        return $chunks;
    }
}

?>