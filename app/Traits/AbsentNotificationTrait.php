<?php

namespace App\Traits;

trait AbsentNotificationTrait
{    
    public function getEmailBodyContent($shiftDet)
    {
        $body = "<br/><br/><b>Shift Details</b><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">Shift</th>
                        <th style="text-align: center;border: 1px solid black;">Day</th>
                        <th style="text-align: center;border: 1px solid black;">On Duty Time</th>
                        <th style="text-align: center;border: 1px solid black;">Off Duty Time</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';
        $body .= '<tr>
                    <td style="text-align:left;border: 1px solid black;">'.$shiftDet->Description.'</td>
                    <td style="text-align:left;border: 1px solid black;">'.$this->dayName.'</td>
                    <td style="text-align:left;border: 1px solid black;">'.$shiftDet->onDutyTime.'</td>
                    <td style="text-align:left;border: 1px solid black;">'.$shiftDet->offDutyTime.'</td>
                 </tr>
                 </tbody>
                 </table>';

        return $body;
    }
}
