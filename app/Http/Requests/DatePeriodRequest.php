<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property \DateTimeInterface      $from
 * @property \DateTimeInterface|null $to
 */
class DatePeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => 'required|date',
            'to' => 'date|after:from',
        ];
    }

    public function getFrom(): Carbon
    {
        return new Carbon($this->from);
    }

    public function getTo(): ?Carbon
    {
        return $this->to ? new Carbon($this->to) : null;
    }
}
