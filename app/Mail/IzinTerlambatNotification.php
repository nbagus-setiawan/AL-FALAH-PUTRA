<?php

namespace App\Mail;

use App\Models\Izin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IzinTerlambatNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Izin $izin)
    {
        //
    }

    public function build()
    {
        return $this->subject("[SIAP AFP] Santri Telat Kembali dari Izin — {$this->izin->santri->nama_lengkap}")
            ->view('emails.izin-terlambat')
            ->with([
                'santri' => $this->izin->santri,
                'izin' => $this->izin,
            ]);
    }
}
