<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'deletion_warning_sent_at',
        'email_consent',
        'email_consent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deletion_warning_sent_at' => 'datetime',
            'email_consent_at' => 'datetime',
            'solved_questions' => 'array',
            'exam_failed_questions' => 'array',
            'bookmarked_questions' => 'array',
            'achievements' => 'array',
        ];
    }
    /**
     * Override the default email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(5), // 5 Minuten Gültigkeit für E-Mail-Änderungen
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );
        \Mail::to($this->email)->send(new \App\Mail\VerifyRegistrationMail($verificationUrl));
    }
}
