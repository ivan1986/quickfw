<?php

require_once QFWPATH.'/DbSimple/Generic.php';

/**
 * Database class for MySQL.
 */
class DbSimple_Mypdo extends DbSimple_Generic_Database
{
    private $PDO;
    private $prepareCache = array();
    
    public function DbSimple_Mypdo($dsn)
    {
        $p = DbSimple_Generic::parseDSN($dsn);
        $base = preg_replace('{^/}s', '', $p['path']);
        if (!class_exists('PDO')) {
            return $this->_setLastError("-1", "PDO extension is not loaded", "PDO");
        }
        
        try {
		    $this->PDO = new PDO('mysql:host='.$p['host'].';dbname='.$base, $p['user'], isset($p['pass'])?$p['pass']:'');
		} catch (PDOException $e) {
			$this->_setLastError($e->getCode() , $e->getMessage(), 'new PDO');
		}
    }

    function _performGetPlaceholderIgnoreRe()
    {
        return '
            "   (?> [^"\\\\]+|\\\\"|\\\\)*    "   |
            \'  (?> [^\'\\\\]+|\\\\\'|\\\\)* \'   |
            `   (?> [^`]+ | ``)*              `   |   # backticks
            /\* .*?                          \*/      # comments
        ';
    }
    
    function _performEscape($s, $isIdent=false)
    {
        if (!$isIdent) {
            return $this->PDO->quote($s);
        } else {
            return "`" . str_replace('`', '``', $s) . "`";
        }
    }
    
    function _performQuery($queryMain)
    {
        $this->_lastQuery = $queryMain;
        $this->_expandPlaceholders($queryMain, true);
        $p = $this->PDO->prepare($queryMain[0]);
        $res = $p->execute(array_slice($queryMain,1));
        $res = $p->fetchAll();
        //print_r($res);
    	return $res;
    	
    	
        $result = @mysql_query($queryMain[0], $this->link);
        if ($result === false) return $this->_setDbError($queryMain[0]);
        if (!is_resource($result)) {
            if (preg_match('/^\s* INSERT \s+/six', $queryMain[0])) {
                // INSERT queries return generated ID.
                return @mysql_insert_id($this->link);
            }
            // Non-SELECT queries return number of affected rows, SELECT - resource.
            return @mysql_affected_rows($this->link);
        }
        return $result;
    }
    
    function _performTransformQuery(&$queryMain, $how)
    {
        // If we also need to calculate total number of found rows...
        switch ($how) {
            // Prepare total calculation (if possible)
            case 'CALC_TOTAL':
                $m = null;
                if (preg_match('/^(\s* SELECT)(.*)/six', $queryMain[0], $m)) {
                    $queryMain[0] = $m[1] . ' SQL_CALC_FOUND_ROWS' . $m[2];
                }
                return true;
        
            // Perform total calculation.
            case 'GET_TOTAL':
                // Built-in calculation available?
                $queryMain = array('SELECT FOUND_ROWS()');
                // Else use manual calculation.
                // TODO: GROUP BY ... -> COUNT(DISTINCT ...)
                $re = '/^
                    (?> -- [^\r\n]* | \s+)*
                    (\s* SELECT \s+)                                      #1     
                    (.*?)                                                 #2
                    (\s+ FROM \s+ .*?)                                    #3
                        ((?:\s+ ORDER \s+ BY \s+ .*?)?)                   #4
                        ((?:\s+ LIMIT \s+ \S+ \s* (?:, \s* \S+ \s*)? )?)  #5
                $/six';
                $m = null;
                if (preg_match($re, $queryMain[0], $m)) {
                    $query[0] = $m[1] . $this->_fieldList2Count($m[2]) . " AS C" . $m[3];
                    $skipTail = substr_count($m[4] . $m[5], '?'); 
                    if ($skipTail) array_splice($query, -$skipTail);
                }
                return true;
        }
        
        return false;
    }
    
    function _setDbError($query)
    {
        return '';//$this->_setLastError($err[1], $err[2], $query);
    }

}

class DbSimple_Mypdo_Blob extends DbSimple_Generic_Blob
{
    // MySQL does not support separate BLOB fetching. 
    var $blobdata = null;
    var $curSeek = 0;

    function DbSimple_Mypdo_Blob(&$database, $blobdata=null)
    {
        $this->blobdata = $blobdata;
        $this->curSeek = 0;
    }

    function read($len)
    {
        $p = $this->curSeek;
        $this->curSeek = min($this->curSeek + $len, strlen($this->blobdata));
        return substr($this->blobdata, $this->curSeek, $len);
    }

    function write($data)
    {
        $this->blobdata .= $data;
    }

    function close()
    {
        return $this->blobdata;
    }

    function length()
    {
        return strlen($this->blobdata);
    }
}

?>