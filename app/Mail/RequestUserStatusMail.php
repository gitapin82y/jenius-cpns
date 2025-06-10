<?php
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RequestUserStatusMail extends Mailable 
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param string $status
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Request Pendaftaran Akun '. $this->user->name;
            
        return $this->subject($subject)
                    ->view('emails.request-user-status')
                    ->with([
                        'user' => $this->user,
                    ]);
    }
}