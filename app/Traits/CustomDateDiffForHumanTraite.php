<?php

namespace App\Traits;
use Carbon\Carbon;

trait CustomDateDiffForHumanTraite
{
    public function getDateDiff($expiryDate, $toDay, $dayType)
    {
        $startDate = Carbon::parse($toDay);
        $endDate = Carbon::parse($expiryDate);

        $diffInDays = $startDate->diffInDays($endDate);

        $getDayFormat = $this->getYear($diffInDays);
        $getDayType = $this->createDayType($dayType);

        return $getDayFormat.' '.$getDayType;
   
    }
   
    private function createDayType($dayType)
    {     
       return ($dayType == 1)? 'remaining': 'ago';   
    }

    private function getYear($diffInDays)
    {
        $years = floor($diffInDays / 365);
        return  $this->getMonth($diffInDays,$years);
    }

    private function getMonth($diffInDays, $years)
    {
        $months = floor(($diffInDays - ($years * 365))/30.5);
        return $this->getDay($diffInDays, $years, $months);
    }

    private function getDay($diffInDays, $years, $months)
    {
        $days = ($diffInDays - ($years * 365) - ($months * 30.5));
        return $this->createDayFormat($years, $months, $days);
    }
    
    private function createDayFormat($years, $months, $days)
    {
        $string = "";
        if($years > 0)
        {
            $string = ' '.$years.' year/s';
        }

        if($months > 0)
        {
            $string .= ' '.$months.' month/s';
        }

        if($days > 0)
        {
            $string .= ' '.(int)($days).' day/s';
        }

        return $string;
    }

}
