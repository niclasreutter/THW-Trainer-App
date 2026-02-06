<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'last_activity_at',
        'email_consent',
        'email_consent_at',
        'leaderboard_consent',
        'leaderboard_consent_at',
        'leaderboard_banner_dismissed',
        'onboarding_completed',
    ];

    /**
     * The attributes that are not mass assignable (security-critical fields).
     *
     * @var list<string>
     */
    protected $guarded = [
        'id',
        'is_admin',
        'exam_passed_count',
        'solved_questions',
        'wrong_answers',
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
            'verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
            'deletion_warning_sent_at' => 'datetime',
            'email_consent_at' => 'datetime',
            'leaderboard_consent_at' => 'datetime',
            'weekly_reset_at' => 'datetime',
            'solved_questions' => 'array',
            'exam_failed_questions' => 'array',
            'bookmarked_questions' => 'array',
            'achievements' => 'array',
        ];
    }
    
    /**
     * User hat viele Fragen-Fortschritte
     */
    public function questionProgress()
    {
        return $this->hasMany(UserQuestionProgress::class);
    }
    
    /**
     * User ist in vielen Lehrgängen eingeschrieben
     */
    public function enrolledLehrgaenge()
    {
        return $this->belongsToMany(Lehrgang::class, 'user_lehrgaenge')
            ->withPivot('punkte', 'completed', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }
    
    /**
     * User hat viele Lehrgang-Fortschritte
     */
    public function lehrgangProgress()
    {
        return $this->hasMany(UserLehrgangProgress::class);
    }
    
    /**
     * Ortsverband den der User erstellt hat (als Ausbildungsbeauftragter)
     */
    public function ownedOrtsverband()
    {
        return $this->hasOne(Ortsverband::class, 'created_by');
    }
    
    /**
     * Ortsverbände in denen der User Mitglied ist
     */
    public function ortsverbände()
    {
        return $this->belongsToMany(Ortsverband::class, 'ortsverband_members')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }
    
    /**
     * Ortsverbände in denen der User Mitglied ist (Alias ohne Umlaut)
     */
    public function ortsverbande()
    {
        return $this->ortsverbände();
    }
    
    /**
     * Prüft ob User Ausbildungsbeauftragter eines Ortsverbands ist
     */
    public function isAusbildungsbeauftragter(Ortsverband $ortsverband): bool
    {
        return $ortsverband->isAusbildungsbeauftragter($this);
    }
    
    /**
     * Generiert einen 6-stelligen Zahlencode und speichert ihn (15 min Gültigkeit).
     */
    public function generateVerificationCode(): string
    {
        $code = (string) random_int(100000, 999999);

        $this->verification_code = $code;
        $this->verification_code_expires_at = now()->addMinutes(15);
        $this->save();

        return $code;
    }

    /**
     * Sendet die Verifikations-E-Mail mit einem Zahlencode.
     */
    public function sendEmailVerificationNotification()
    {
        $code = $this->generateVerificationCode();

        try {
            \Log::info('Attempting to send verification email', [
                'user_id' => $this->id,
                'email' => $this->email,
                'name' => $this->name
            ]);

            \Mail::to($this->email)->send(new \App\Mail\VerifyRegistrationMail($code));

            \Log::info('Verification email sent successfully', [
                'user_id' => $this->id,
                'email' => $this->email
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'user_id' => $this->id,
                'email' => $this->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Lernpool Relationships
     */
    public function lernpoolEnrollments()
    {
        return $this->hasMany(OrtsverbandLernpoolEnrollment::class);
    }

    public function enrolledLernpools()
    {
        return $this->belongsToMany(
            OrtsverbandLernpool::class,
            'ortsverband_lernpool_enrollments',
            'user_id',
            'lernpool_id'
        )->withPivot('enrolled_at', 'completed_at');
    }

    public function lernpoolProgress()
    {
        return $this->hasMany(OrtsverbandLernpoolProgress::class);
    }

    /**
     * User hat viele Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Ungelesene Notifications
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->orderBy('created_at', 'desc');
    }
}
