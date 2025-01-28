<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use App\Enums\InstructionRequestStatus;

class StatusChangeNotification extends BaseEmailNotification
{
    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->getData();
        $status = $data['status'];

        $template = match($status) {
            InstructionRequestStatus::ASSIGNED => 'emails.status.assigned',
            InstructionRequestStatus::ACCEPTED => 'emails.status.accepted',
            InstructionRequestStatus::REJECTED => 'emails.status.rejected',
            default => 'emails.status.status_change'
        };

        return (new MailMessage)
            ->subject($this->getSubject())
            ->view($template, $this->prepareMailData());
    }
}
