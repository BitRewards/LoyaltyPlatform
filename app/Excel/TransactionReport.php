<?php

namespace App\Excel;

use App\Models\Partner;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    /**
     * @var Partner
     */
    protected $partner;

    /**
     * @var \DateTime
     */
    protected $from;

    /**
     * @var \DateTime
     */
    protected $to;

    public function __construct(Partner $partner, \DateTime $from, \DateTime $to)
    {
        $this->partner = $partner;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        return Transaction::model()->whereAttributes([
            ['status', Transaction::STATUS_CONFIRMED],
            ['created_at', '>=', $this->from],
            ['created_at', '<=', $this->to],
            ['partner_id', $this->partner->id],
        ])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            __('Date'),
            __('An increase of'),
            __('Description'),
            __('Transaction'),
        ];
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->id,
            \HDate::dateFull(strtotime($transaction->created_at)),
            $transaction->balance_change > 0 ? $transaction->balance_change : 0,
            $transaction->balance_change < 0 ? $transaction->balance_change : 0,
            \HTransaction::getTitle($transaction),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = $event->sheet->getDelegate()->getHighestRow();

                $event->sheet
                    ->getStyle('A1:E1')
                    ->getFont()
                    ->setBold(true);

                $event->sheet
                    ->getStyle('A1:E1')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $event
                    ->sheet
                    ->getStyle("A2:E{$rowCount}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
