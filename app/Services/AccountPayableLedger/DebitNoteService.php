<?php

namespace App\Services\AccountPayableLedger;

use App\Commands\CreateDebitNote;
use App\Models\DebitNote;
use http\Exception\InvalidArgumentException;

class DebitNoteService
{

    public function store($data)
    {
        if(is_array($data))
        {
            $debitNote = new DebitNote();
            $debitNote->fill($data);
        }else if($data instanceof DebitNote)
        {
            $debitNote = $data;
        }else {
            throw new InvalidArgumentException("Invalid Data");

        }

        try {
            $debitNoteCommand = new CreateDebitNote($debitNote);
            $storeDebitNote = $debitNoteCommand->execute();
            return $storeDebitNote;

        }catch (\Exception $exception)
        {
            return $exception;
        }
    }

}
