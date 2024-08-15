<?php

namespace App\Commands;

use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Repositories\DebitNoteDetailsRepository;

class AddDebitNoteDetails implements CommandInterface
{
    private $debitNoteDetailsRepository;
    private $debitNote;
    private $debitNoteDetails;

    public function __construct(
        DebitNote $debitNote,
        DebitNoteDetails $debitNoteDetails)
    {
        $this->debitNote = $debitNote;
        $this->debitNoteDetails = $debitNoteDetails;
        $this->debitNoteDetailsRepository = app()->make(DebitNoteDetailsRepository::class);
    }

    public function execute()
    {
        // TODO: Implement execute() method.
        return $this->debitNoteDetailsRepository->store();
        dd($this->debitNote,$this->debitNoteDetails);
    }
}
