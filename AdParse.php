<?php

declare(strict_types=1);

function exceptionHandler($err_no, $err_msg, $filename, $linenum)
{
    if (error_reporting() != E_ALL) {
        return false;
    }
}
set_error_handler("exceptionHandler");

function catchException($e)
{
    if (error_reporting() === 0) {
        return;
    } /*else { // DEV &&/|| DEBUG
        $r = $e;
        $r->xdebug_message = null;
        //var_dump($e);
        return false;
    }*/
}
set_exception_handler('catchException');


interface AdParseInt
{
    public static function getDateTimeFormated($pFormat = 'd/m/Y H:i:s'): string;
    public static function getDateTimeDB($pFormat = 'Y-m-d H:i:s'): string;
    public static function getVal(): string;
}


class AdParse implements AdParseInt
{
    private static $instance;
    const AD_UNIX_CONVERTOR = ((1970 - 1601) * 365.242190) * 86400;

    /**
     * segundos desde 1º de janeiro de 1601
     * @return doubleval $segundosADEpoch
     */
    protected static float $secADEpoch;

    protected static int $timestampADEpoch;

    protected static int $timestampUnix;

    private static function setSecADEpoch($pValue = 1): void
    {
        self::$secADEpoch = 1;
        if ($pValue > 1) {
            self::$secADEpoch = $pValue / (10000000);
        }
    }

    protected static function getSecADEpoch(): float
    {
        return self::$secADEpoch;
    }

    protected static function setTimestampADEpoch(int $pValue = 1): void
    {
        self::$timestampADEpoch = $pValue;
        self::setSecADEpoch(self::$timestampADEpoch);
        self::setTimestampUnix();
    }

    protected static function setTimestampUnix(): void
    {
        self::$timestampUnix = intval(self::getSecADEpoch() - self::AD_UNIX_CONVERTOR);
    }

    protected static function getTimestampUnix(): int
    {
        return self::$timestampUnix;
    }

    public static function dateAD(int $pEpochAD)
    {
        if (is_null($pEpochAD)) return false;
        if (empty($pEpochAD)) return false;
        if (in_array($pEpochAD, [0, 1])) return false;

        self::$instance = new static();
        self::setTimestampADEpoch($pEpochAD);

        return self::$instance;
    }

    public static function getDateTimeFormated($pFormat = 'd/m/Y H:i:s'): string
    {
        if (AdParse::$timestampUnix === false) return null;
        return date($pFormat, AdParse::$timestampUnix);
    }

    public static function getDateTimeDB($pFormat = 'd/m/Y H:i:s'): string
    {
        if (AdParse::$timestampUnix === false) return null;
        return date($pFormat, AdParse::$timestampUnix);
    }

    public static function getVal(): string
    {
        return strval(AdParse::$timestampADEpoch);
    }
}
