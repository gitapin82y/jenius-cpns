<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserStatusMail extends Mailable 
{
    use Queueable, SerializesModels;

    public $user;
    public $statusText;
    public $isApproved;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $status
     */
    public function __construct(User $user, string $status)
    {
        $this->user = $user;
        $this->isApproved = $status === 'active';
        $this->statusText = $this->isApproved ? 'disetujui' : 'ditolak';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->isApproved ? 
            'Pendaftaran Akun Anda Disetujui' : 
            'Pendaftaran Akun Anda Ditolak';
            
        return $this->subject($subject)
                    ->view('emails.user-status')
                    ->with([
                        'user' => $this->user,
                        'isApproved' => $this->isApproved,
                        'statusText' => $this->statusText
                    ]);
    }
}