<?php defined('BASEPATH') OR exit('No direct script access allowed');

class DB4
{
    private static $db4;

    public function __construct ()
    {
        self::$db4 = app('adodb')->init('db4');
    }

    public static function db4 ()
    {
        return self::$db4;
    }

    public static function Debug ($debug)
    {
        return self::$db4->debug = $debug;
    }

    public static function isDebug ()
    {
        return self::$db4->debug ?? FALSE;
    }

    public static function BeginTrans ()
    {
        return self::$db4->BeginTrans();
    }

    public static function CommitTrans ($ok = true)
    {
        return self::$db4->CommitTrans($ok = true);
    }

    public static function RollbackTrans ()
    {
        return self::$db4->RollbackTrans();
    }

    public static function StartTrans ($errfn = 'ADODB_TransMonitor')
    {
        return self::$db4->StartTrans($errfn);
    }

    public static function FailTrans ()
    {
        // return self::$db4->FailTrans();

        if (self::$db4->debug)
        if (self::$db4->transOff == 0)
        {
            ADOConnection::outp("FailTrans outside StartTrans/CompleteTrans");
        }
        else
        {
            ADOConnection::outp("Estusae : FailTrans was called");
        }

        self::$db4->_transOK = false;
    }

    public static function CompleteTrans ($autoComplete = true)
    {
        return self::$db4->CompleteTrans($autoComplete);
    }

    public static function ErrorMsg ()
    {
        return self::$db4->errormsg();
    }

    public static function Execute ($sql, $inputarr = false)
    {
        $ret = self::$db4->Execute($sql, $inputarr);

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
        $ret = self::$db4->CacheExecute($secs2cache, $sql, $inputarr);

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
        $ret = self::$db4->GetOne($sql, $inputarr);

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
        $ret = self::$db4->CacheGetOne($secs2cache, $sql, $inputarr);

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
        return self::$db4->GetInsertSQL($rs, $arrFields, $magic_quotes, $force);
    }

    public static function UpdateSQL ($rs, $arrFields, $forceUpdate = false, $magic_quotes = false, $force = null)
    {
        return self::$db4->GetUpdateSQL($rs, $arrFields, $forceUpdate, $magic_quotes, $force);
    }

    public static function qStr ($s, $magic_quotes = false)
    {
        return self::$db4->qStr($s, $magic_quotes);
    }
}