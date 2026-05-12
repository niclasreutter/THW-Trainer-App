<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'pending_email',
        'password',
        'avatar_path',
        'active_accessories',
        'last_activity_at',
        'email_consent',
        'email_consent_at',
        'push_consent',
        'push_consent_at',
        'notify_daily_reminder_email',
        'notify_daily_reminder_push',
        'notify_spaced_repetition_email',
        'notify_spaced_repetition_push',
        'notify_league_updates_email',
        'notify_league_updates_push',
        'leaderboard_consent',
        'leaderboard_consent_at',
        'leaderboard_banner_dismissed',
        'onboarding_completed',
        'onboarding_tour_completed',
        'exam_date',
        'extras_enabled',
    ];

    public const NOTIFICATION_CATEGORIES = [
        'daily_reminder',
        'spaced_repetition',
        'league_updates',
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
            'email_consent' => 'boolean',
            'email_consent_at' => 'datetime',
            'push_consent' => 'boolean',
            'push_consent_at' => 'datetime',
            'notify_daily_reminder_email' => 'boolean',
            'notify_daily_reminder_push' => 'boolean',
            'notify_spaced_repetition_email' => 'boolean',
            'notify_spaced_repetition_push' => 'boolean',
            'notify_league_updates_email' => 'boolean',
            'notify_league_updates_push' => 'boolean',
            'leaderboard_consent' => 'boolean',
            'leaderboard_consent_at' => 'datetime',
            'weekly_reset_at' => 'datetime',
            'solved_questions' => 'array',
            'exam_failed_questions' => 'array',
            'bookmarked_questions' => 'array',
            'achievements' => 'array',
            'exam_date' => 'date',
            'streak_freeze_log' => 'array',
            'streak_freeze_reset_at' => 'date',
            'glowing_name_until' => 'datetime',
            'double_xp_until' => 'datetime',
            'profile_frame_until' => 'datetime',
            'rank_color_until' => 'datetime',
            'active_title_until' => 'datetime',
            'active_accessories' => 'array',
            'extras_enabled' => 'boolean',
        ];
    }

    /**
     * User hat viele Zusatz-Fragen-Fortschritte
     */
    public function extraQuestionProgress()
    {
        return $this->hasMany(UserExtraQuestionProgress::class);
    }
    
    /**
     * Avatar-URL basierend auf dem gespeicherten Pfad
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $base = 'https://dicebear.niclas-reutter.de/9.x/';

                if ($this->avatar_path) {
                    $url = $base . $this->avatar_path;
                } else {
                    // Fallback for users without avatar_path
                    $seed = urlencode($this->id . str_replace(' ', '', $this->name));
                    $url = $base . 'avataaars/svg?radius=50&seed=' . $seed . '&backgroundType=gradientLinear&backgroundColor=00337f,0055cc&backgroundRotation=135&eyes=default,happy&mouth=default,smile';
                }

                // Aktive Accessoires: URL parsen und Parameter überschreiben (nicht duplizieren!)
                $active = $this->active_accessories;
                if (!empty($active)) {
                    $parts = parse_url($url);
                    parse_str($parts['query'] ?? '', $params);

                    if (!empty($active['accessories'])) {
                        $params['accessories'] = $active['accessories'];
                        $params['accessoriesProbability'] = '100';
                        if (!empty($active['accessoriesColor'])) {
                            $params['accessoriesColor'] = $active['accessoriesColor'];
                        }
                    }
                    if (!empty($active['top'])) {
                        $params['top'] = $active['top'];
                    }
                    if (!empty($active['facialHair'])) {
                        $params['facialHair'] = $active['facialHair'];
                        $params['facialHairProbability'] = '100';
                        if (!empty($active['facialHairColor'])) {
                            $params['facialHairColor'] = $active['facialHairColor'];
                        }
                    }

                    $url = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . '?' . http_build_query($params);
                }

                return $url;
            },
        );
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

    public function isAdmin(): bool
    {
        return $this->useroll === 'admin';
    }

    /**
     * Standard-Landingseite nach Login. Ausbilder landen auf ihrem
     * OV-Dashboard, alle anderen auf dem persönlichen Dashboard.
     */
    public function homePath(): string
    {
        $ausbilderOV = $this->ortsverbande()
            ->wherePivot('role', 'ausbildungsbeauftragter')
            ->first();

        return $ausbilderOV
            ? '/ortsverband/' . $ausbilderOV->id . '/dashboard'
            : '/dashboard';
    }

    public function canEditQuestions(): bool
    {
        return in_array($this->useroll, ['admin', 'contributor'], true);
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
     * Bei pending_email wird der Code an die neue Adresse gesendet.
     */
    public function sendEmailVerificationNotification()
    {
        $code = $this->generateVerificationCode();
        $targetEmail = $this->pending_email ?? $this->email;

        try {
            \Log::info('Attempting to send verification email', [
                'user_id' => $this->id,
                'email' => $targetEmail,
                'is_pending' => !is_null($this->pending_email),
            ]);

            \Mail::to($targetEmail)->send(new \App\Mail\VerifyRegistrationMail($code));

            \Log::info('Verification email sent successfully', [
                'user_id' => $this->id,
                'email' => $targetEmail
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'user_id' => $this->id,
                'email' => $targetEmail,
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
     * User hat viele Avatar-Accessoires
     */
    public function avatarAccessories()
    {
        return $this->hasMany(UserAvatarAccessory::class);
    }

    /**
     * User hat viele Shop-Käufe
     */
    public function shopPurchases()
    {
        return $this->hasMany(ShopPurchase::class)->orderBy('created_at', 'desc');
    }

    /**
     * User hat viele Wochenbelohnungen
     */
    public function lootboxes()
    {
        return $this->hasMany(Lootbox::class)->orderBy('created_at', 'desc');
    }

    /**
     * Ungeöffnete Wochenbelohnungen
     */
    public function unopenedLootboxes()
    {
        return $this->hasMany(Lootbox::class)->where('opened', false);
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

    /**
     * User hat viele XP-Verlauf-Einträge
     */
    public function xpHistories()
    {
        return $this->hasMany(XpHistory::class)->orderBy('created_at', 'desc');
    }

    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function wantsEmailFor(string $category): bool
    {
        if (!$this->email_consent) {
            return false;
        }
        $column = "notify_{$category}_email";
        return (bool) ($this->{$column} ?? false);
    }

    public function wantsPushFor(string $category): bool
    {
        if (!$this->push_consent) {
            return false;
        }
        $column = "notify_{$category}_push";
        return (bool) ($this->{$column} ?? false);
    }
}
