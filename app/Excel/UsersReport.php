<?php

namespace App\Excel;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $partner;

    public function __construct(Partner $partner)
    {
        $this->partner = $partner;
    }

    public function collection(): Collection
    {
        return User::query()->where('partner_id', $this->partner->id)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            __('Name'),
            __('Email'),
            __('Phone'),
            __('Signup date'),
            __('Balance'),
        ];
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone,
            \HDate::dateFull($user->created_at),
            (float) $user->balance,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = $event->sheet->getDelegate()->getHighestRow();

                $event->sheet
                    ->getStyle('A1:F1')
                    ->getFont()
                    ->setBold(true);

                $event->sheet
                    ->getStyle('A1:F1')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event
                    ->sheet
                    ->getStyle("A2:F{$rowCount}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
