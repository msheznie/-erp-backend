<?php
namespace App\enums\attendance;
class AbsentType{

    const ON_TIME = 1;
    const LATE = 2;
    const ABSENT = 4;
    const ON_LEAVE = 5;
    const HALF_DAY = 7;
    const HOLIDAY = 8;
    const WEEKEND = 9;
    const WORK_OUT = 10;
    const REMOTELY = 11;
    const MANDATE = 12;
    const SECONDMENT = 13;
    const ON_TRIP = 14;
    const MISSED_PUNCH = 15;
    const EXCEPTION = 16;
}
