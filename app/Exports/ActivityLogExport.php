<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ActivityLogExport implements FromView, WithChunkReading, WithCustomCsvSettings
{
    protected $activity_log;

    public function __construct($activity_log)
    {
        $this->activity_log = $activity_log;
    }

    public function view(): View
    {
        return view('exports.activity_log', [
            'activity_logs' => $this->activity_log
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ";"
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
