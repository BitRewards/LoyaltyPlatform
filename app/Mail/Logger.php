<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Logger extends Mailable
{
    use Queueable, SerializesModels;

    private $record;

    /**
     * Create a new message instance.
     */
    public function __construct($record)
    {
        $this->record = $record;

        if (isset($this->record['context']['exception'])) {
            try {
                $exceptionClass = get_class($this->record['context']['exception']);
                $parts = explode('\\', $exceptionClass);
                $exceptionClass = array_pop($parts);
            } catch (Exception $e) {
                $exceptionClass = 'UnknownClass';
            }

            unset($this->record['context']['exception']);

            $this->subject = "$exceptionClass occurred at ".$this->record['datetime']->format('d.m.Y');
        } else {
            $level = $this->record['level_name'] ?? 'Log';

            $this->subject = "$level occurred at ".$this->record['datetime']->format('d.m.Y');
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.logger', ['record' => $this->record]);
    }
}
