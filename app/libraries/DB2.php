<?php defined('BASEPATH') OR exit('No direct script access allowed');

class DB2
{
    private static $db2;

    public function __construct ()
    {

        self::$db2 = app('adodb')->init('db2');
    }

    public static function db2 ()
    {
        return self::$db2;
    }

    public static function Debug ($debug)
    {
        return self::$db2->debug = $debug;
    }

    public static function isDebug ()
    {
        return self::$db2->debug ?? FALSE;
    }

    public static function BeginTrans ()
    {
        return self::$db2->BeginTrans();
    }

    public static function CommitTrans ($ok = true)
    {
        return self::$db2->CommitTrans($ok = true);
    }

    public static function RollbackTrans ()
    {
        return self::$db2->RollbackTrans();
    }

    public static function StartTrans ($errfn = 'ADOdb2_TransMonitor')
    {
        return self::$db2->StartTrans($errfn);
    }

    public static function FailTrans ()
    {
        // return self::$db2->FailTrans();

        if (self::$db2->debug)
        if (self::$db2->transOff == 0)
        {
            ADOConnection::outp("FailTrans outside StartTrans/CompleteTrans");
        }
        else
        {
            ADOConnection::outp("Estusae : FailTrans was called");
        }

        self::$db2->_transOK = false;
    }

    public static function CompleteTrans ($autoComplete = true)
    {
        return self::$db2->CompleteTrans($autoComplete);
    }

    public static function ErrorMsg ()
    {
        return self::$db2->errormsg();
    }

    public static function Execute ($sql, $inputarr = false)
    {
        $ret = self::$db2->Execute($sql, $inputarr);

        $is_ajax_request = is('ajax');

        if (!$is_ajax_request)
        {
            if ($ret == FALSE && $sql != '')
            {
                $errmsg = "sql error : " . self::ErrorMsg();
                die($errmsg);
            }
        }

        return $ret;
    }

    public static function CacheExecute ($secs2cache, $sql = false, $inputarr = false)
    {
        $ret = self::$db2->CacheExecute($secs2cache, $sql, $inputarr);

        $is_ajax_request = is('ajax');

        if (!$is_ajax_request)
        {
            if ($ret == FALSE && $sql != '')
            {
                $errmsg = "sql error : " . self::ErrorMsg();
                die($errmsg);
            }
        }

        return $ret;
    }

    public static function GetOne ($sql, $inputarr = false)
    {
        $ret = self::$db2->GetOne($sql, $inputarr);

        $is_ajax_request = is('ajax');

        if (!$is_ajax_request)
        {
            if (self::ErrorMsg() != '' && $sql != '')
            {
                $errmsg = "sql error : " . self::ErrorMsg();
                die($errmsg);
            }
        }

        return $ret;
    }

    public static function CacheGetOne ($secs2cache, $sql = false, $inputarr = false)
    {
        $ret = self::$db2->CacheGetOne($secs2cache, $sql, $inputarr);

        $is_ajax_request = is('ajax');

        if (!$is_ajax_request)
        {
            if (self::ErrorMsg() != '' && $sql != '')
            {
                $errmsg = "sql error : " . self::ErrorMsg();
                die($errmsg);
            }
        }

        return $ret;
    }

    public static function InsertSQL ($rs, $arrFields, $magic_quotes = false, $force = null)
    {
        return self::$db2->GetInsertSQL($rs, $arrFields, $magic_quotes, $force);
    }

    public static function UpdateSQL ($rs, $arrFields, $forceUpdate = false, $magic_quotes = false, $force = null)
    {
        return self::$db2->GetUpdateSQL($rs, $arrFields, $forceUpdate, $magic_quotes, $force);
    }

    public static function qStr ($s, $magic_quotes = false)
    {
        return self::$db2->qStr($s, $magic_quotes);
    }
}