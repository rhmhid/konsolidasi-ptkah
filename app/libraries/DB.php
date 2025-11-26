<?php defined('BASEPATH') OR exit('No direct script access allowed');

abstract class DB extends CI_Model
{
    private static $db;

	public function __construct ()
    {
        parent::__construct();

        self::$db = app('adodb')->init();
    }

    public static function db ()
    {
        return self::$db;
    }

    public static function Debug ($debug)
    {
        return self::$db->debug = $debug;
    }

    public static function isDebug ()
    {
        return self::$db->debug ?? FALSE;
    }

    public static function BeginTrans ()
    {
        return self::$db->BeginTrans();
    }

    public static function CommitTrans ($ok = true)
    {
        return self::$db->CommitTrans($ok = true);
    }

    public static function RollbackTrans ()
    {
        return self::$db->RollbackTrans();
    }

    public static function StartTrans ($errfn = 'ADODB_TransMonitor')
    {
        return self::$db->StartTrans($errfn);
    }

    public static function FailTrans ()
    {
        // return self::$db->FailTrans();

        if (self::$db->debug)
        if (self::$db->transOff == 0)
        {
            ADOConnection::outp("FailTrans outside StartTrans/CompleteTrans");
        }
        else
        {
            ADOConnection::outp("Estusae : FailTrans was called");
        }

        self::$db->_transOK = false;
    }

    public static function CompleteTrans ($autoComplete = true)
    {
        return self::$db->CompleteTrans($autoComplete);
    }

    public static function ErrorMsg ()
    {
        return self::$db->errormsg();
    }

    public static function Execute ($sql, $inputarr = false)
    {
        $ret = self::$db->Execute($sql, $inputarr);

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
        $ret = self::$db->CacheExecute($secs2cache, $sql, $inputarr);

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
        $ret = self::$db->GetOne($sql, $inputarr);

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
        $ret = self::$db->CacheGetOne($secs2cache, $sql, $inputarr);

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
        return self::$db->GetInsertSQL($rs, $arrFields, $magic_quotes, $force);
    }

    public static function UpdateSQL ($rs, $arrFields, $forceUpdate = false, $magic_quotes = false, $force = null)
    {
        return self::$db->GetUpdateSQL($rs, $arrFields, $forceUpdate, $magic_quotes, $force);
    }

    public static function qStr ($s, $magic_quotes = false)
    {
        return self::$db->qStr($s, $magic_quotes);
    }
}