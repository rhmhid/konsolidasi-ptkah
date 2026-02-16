<?php defined('BASEPATH') OR exit('No direct script access allowed');

class DB3
{
    private static $db3;

    public function __construct ()
    {

        self::$db3 = app('adodb')->init('db3');
    }

    public static function db3 ()
    {
        return self::$db3;
    }

    public static function Debug ($debug)
    {
        return self::$db3->debug = $debug;
    }

    public static function isDebug ()
    {
        return self::$db3->debug ?? FALSE;
    }

    public static function BeginTrans ()
    {
        return self::$db3->BeginTrans();
    }

    public static function CommitTrans ($ok = true)
    {
        return self::$db3->CommitTrans($ok = true);
    }

    public static function RollbackTrans ()
    {
        return self::$db3->RollbackTrans();
    }

    public static function StartTrans ($errfn = 'ADOdb3_TransMonitor')
    {
        return self::$db3->StartTrans($errfn);
    }

    public static function FailTrans ()
    {
        // return self::$db3->FailTrans();

        if (self::$db3->debug)
        if (self::$db3->transOff == 0)
        {
            ADOConnection::outp("FailTrans outside StartTrans/CompleteTrans");
        }
        else
        {
            ADOConnection::outp("Estusae : FailTrans was called");
        }

        self::$db3->_transOK = false;
    }

    public static function CompleteTrans ($autoComplete = true)
    {
        return self::$db3->CompleteTrans($autoComplete);
    }

    public static function ErrorMsg ()
    {
        return self::$db3->errormsg();
    }

    public static function Execute ($sql, $inputarr = false)
    {
        $ret = self::$db3->Execute($sql, $inputarr);

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

    public static function CacheExecute ($secs3cache, $sql = false, $inputarr = false)
    {
        $ret = self::$db3->CacheExecute($secs2cache, $sql, $inputarr);

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
        $ret = self::$db3->GetOne($sql, $inputarr);

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

    public static function CacheGetOne ($secs3cache, $sql = false, $inputarr = false)
    {
        $ret = self::$db3->CacheGetOne($secs2cache, $sql, $inputarr);

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
        return self::$db3->GetInsertSQL($rs, $arrFields, $magic_quotes, $force);
    }

    public static function UpdateSQL ($rs, $arrFields, $forceUpdate = false, $magic_quotes = false, $force = null)
    {
        return self::$db3->GetUpdateSQL($rs, $arrFields, $forceUpdate, $magic_quotes, $force);
    }

    public static function qStr ($s, $magic_quotes = false)
    {
        return self::$db3->qStr($s, $magic_quotes);
    }
}
