<?php

namespace App\Commands;

use App\Models\DebitNote;
use App\Repositories\DebitNoteRepository;

class CreateDebitNote implements CommandInterface
{

    private $debitNoteRepository;

    private $debitNote;

    public function __construct(
        DebitNote $debitNote
    )
    {
        $this->debitNote = $debitNote;
        $this->debitNoteRepository = app()->make(DebitNoteRepository::class);
    }

    public function execute():DebitNote
    {
        return $this->debitNoteRepository->store($this->debitNote);
    }
}
