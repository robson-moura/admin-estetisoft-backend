<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $isUpdate;

    /**
     * Create a new message instance.
     */
    public function __construct($appointment, $isUpdate = false)
    {
        $this->appointment = $appointment;
        $this->isUpdate = $isUpdate;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->isUpdate
            ? 'Seu atendimento foi atualizado'
            : 'Novo atendimento agendado';

        return $this->subject($subject)
            ->view('emails.appointment_notification');
    }
}
