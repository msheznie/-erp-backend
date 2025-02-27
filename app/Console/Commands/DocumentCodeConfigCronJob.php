<?php

namespace App\Console\Commands;

use App\Models\DocumentCodeTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DocumentCodeConfigCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:codeConfigEdit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $documentCodeTransaction = DocumentCodeTransaction::where('isGettingEdited', 1)->get();
        if($documentCodeTransaction){
            foreach ($documentCodeTransaction as $key => $value) {
                $editedTime = Carbon::parse($value->isGettingEditedTime);
                $currentTime = Carbon::now();
                $timeDiff = $currentTime->diffInHours($editedTime);

                if ($timeDiff > 1) {
                    $value->isGettingEdited = 0;
                    $value->save();
                }
            }
        }

    }
}
