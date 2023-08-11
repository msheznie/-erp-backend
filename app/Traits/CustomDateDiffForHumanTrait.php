<?php

namespace App\Traits;
use Carbon\Carbon;


trait CustomDateDiffForHumanTrait
{
    private $NoofDiffInDays; 
    private $years; 
    private $months; 
    private $days; 
    
    public function getDateDiff($expiryDate, $toDay, $dayType)
    {
        $startDate = Carbon::parse($toDay);
        $endDate = Carbon::parse($expiryDate);

        $NoofDiffInDays = $startDate->diffInDays( $endDate );

        $getDayFormat = $this->getYear();
        $getDayType = $this->createDayType( $dayType );

        return $getDayFormat.' '.$getDayType;
   
    }
   
    private function createDayType( $dayType )
    {     
       return ($dayType == 1)? 'remaining': 'ago';   
    }

    private function getYear()
    {
        $years = floor($this->NoofDiffInDays / 365);
        return  $this->getMonth();
    }

    private function getMonth()
    {
        $months = floor(($this->NoofDiffInDays - ($this->years * 365))/30.5);
        return $this->getDay();
    }

    private function getDay()
    {
        $days = ($this->NoofDiffInDays - ($this->years * 365) - ($this->months * 30.5));
        return $this->createDayFormat();
    }
    
    private function createDayFormat()
    {
        $string = "";
        if($this->years > 0)
        {
            $string = ' '.$this->years.' year/s';
        }

        if($this->months > 0)
        {
            $string .= ' '.$this->months.' month/s';
        }

        if($this->days > 0)
        {
            $string .= ' '.(int)($this->days).' day/s';
        }

        return $string;
    }

}
