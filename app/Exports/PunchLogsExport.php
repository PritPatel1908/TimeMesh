<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PunchLogsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $punchLogs;

    public function __construct($punchLogs)
    {
        $this->punchLogs = $punchLogs;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->punchLogs;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'User Code',
            'User Name',
            'Room Number',
            'Punch Status',
            'Punch Date & Time',
            'WhatsApp Sent'
        ];
    }

    /**
     * @param mixed $log
     * @return array
     */
    public function map($log): array
    {
        return [
            $log->user_code,
            $log->user->name ?? 'N/A',
            $log->user->room_number ?? 'N/A',
            $log->punch_status ? 'IN' : 'OUT',
            $log->punch_date_time->format('d-m-Y h:i A'),
            $log->send_message ? 'Yes' : 'No'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
