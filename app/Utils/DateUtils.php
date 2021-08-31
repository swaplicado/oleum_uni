<?php namespace App\Utils;

use Carbon\Carbon;

class DateUtils {

    
    public static function getCurrentMonth($oDate)
    {
        $startOfMonth = $oDate->copy()->startOfMonth();
        $endOfMonth = $oDate->copy()->endOfMonth();

        return [$startOfMonth, $endOfMonth];
    }

    public static function getDates($sRange)
    {
        //dd/mm/yyyy - dd/mm/yyyy
        $st = substr($sRange, 0, 10);
        $startDate = Carbon::createFromFormat('d/m/Y', $st);
        $end = substr($sRange, 13, 10);
        $endDate = Carbon::createFromFormat('d/m/Y', $end);

        return [$startDate, $endDate];
    }
}