<?php

namespace App\Jobs;

use App\Models\ImportedData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(): void
    {
        $csv = Reader::createFromPath(Storage::path($this->path), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            ImportedData::create([
                'date' => $record['date'],
                'value' => $record['value'],
            ]);
        }

        Storage::delete($this->path);
    }
}
