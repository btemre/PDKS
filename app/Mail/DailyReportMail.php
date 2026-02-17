<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, \Illuminate\Support\Collection> Birim bazlı rapor bölümleri */
    public array $records;

    /** @var string Mail konusu */
    public string $title;

    /** @var array<int, string> Bölüm sırası (mobil mail için) */
    public array $sectionOrder;

    public function __construct(array $records, string $title, array $sectionOrder = [])
    {
        $this->records = $records;
        $this->title = $title;
        $this->sectionOrder = $sectionOrder ?: array_keys($records);
    }

    public function build(): static
    {
        return $this->subject($this->title)
            ->view('emails.daily-report');
    }
}
