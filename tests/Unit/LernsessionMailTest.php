<?php

use App\Mail\LernsessionStartedMail;
use App\Mail\LernsessionEndedMail;
use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Models\LearningSessionParticipant;
use App\Models\User;

test('LernsessionStartedMail has correct subject', function () {
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';

    $session = new LearningSession();
    $session->title = 'THW Grundlagen Quiz';
    $session->description = 'Test Beschreibung';
    $session->scope = 'global';

    $instance = new LearningSessionInstance();
    $instance->status = 'active';

    $mail = new LernsessionStartedMail($user, $session, $instance);

    expect($mail->envelope()->subject)->toBe('Lernsession gestartet: THW Grundlagen Quiz');
});

test('LernsessionStartedMail uses correct view', function () {
    $user = new User();
    $user->name = 'Test User';
    $user->email = 'test@example.com';

    $session = new LearningSession();
    $session->title = 'Test Session';
    $session->scope = 'global';

    $instance = new LearningSessionInstance();

    $mail = new LernsessionStartedMail($user, $session, $instance);

    expect($mail->content()->view)->toBe('emails.lernsession-started');
});

test('LernsessionStartedMail stores correct properties', function () {
    $user = new User();
    $user->name = 'Max Mustermann';

    $session = new LearningSession();
    $session->title = 'Prüfungsvorbereitung';
    $session->description = 'Intensivkurs';
    $session->scope = 'global';

    $instance = new LearningSessionInstance();

    $mail = new LernsessionStartedMail($user, $session, $instance);

    expect($mail->user)->toBe($user);
    expect($mail->session)->toBe($session);
    expect($mail->instance)->toBe($instance);
});

test('LernsessionStartedMail has no attachments', function () {
    $user = new User();
    $session = new LearningSession();
    $session->scope = 'global';
    $instance = new LearningSessionInstance();

    $mail = new LernsessionStartedMail($user, $session, $instance);

    expect($mail->attachments())->toBe([]);
});

test('LernsessionEndedMail has winner subject when user won', function () {
    $user = new User();
    $user->name = 'Gewinner';

    $session = new LearningSession();
    $session->title = 'Finale Session';
    $session->scope = 'global';

    $instance = new LearningSessionInstance();
    $instance->status = 'completed';

    $participant = new LearningSessionParticipant();
    $participant->final_rank = 1;
    $participant->questions_answered = 10;
    $participant->questions_correct = 8;
    $participant->accuracy_percent = 80.0;
    $participant->xp_earned = 150;

    $mail = new LernsessionEndedMail($user, $session, $instance, $participant, true, 5, true);

    expect($mail->envelope()->subject)->toBe('Lernsession gewonnen: Finale Session');
});

test('LernsessionEndedMail has ended subject when user did not win', function () {
    $user = new User();
    $user->name = 'Teilnehmer';

    $session = new LearningSession();
    $session->title = 'Wochensession';
    $session->scope = 'global';

    $instance = new LearningSessionInstance();

    $participant = new LearningSessionParticipant();
    $participant->final_rank = 3;
    $participant->questions_answered = 5;
    $participant->questions_correct = 3;
    $participant->accuracy_percent = 60.0;
    $participant->xp_earned = 80;

    $mail = new LernsessionEndedMail($user, $session, $instance, $participant, false, 10, false);

    expect($mail->envelope()->subject)->toBe('Lernsession beendet: Wochensession');
});

test('LernsessionEndedMail uses correct view', function () {
    $user = new User();
    $session = new LearningSession();
    $session->scope = 'global';
    $instance = new LearningSessionInstance();
    $participant = new LearningSessionParticipant();
    $participant->final_rank = 1;

    $mail = new LernsessionEndedMail($user, $session, $instance, $participant, false, 1, false);

    expect($mail->content()->view)->toBe('emails.lernsession-ended');
});

test('LernsessionEndedMail stores correct properties', function () {
    $user = new User();
    $user->name = 'Test';

    $session = new LearningSession();
    $session->title = 'Test Session';
    $session->scope = 'global';

    $instance = new LearningSessionInstance();

    $participant = new LearningSessionParticipant();
    $participant->final_rank = 2;
    $participant->questions_answered = 15;
    $participant->questions_correct = 12;
    $participant->accuracy_percent = 80.0;
    $participant->xp_earned = 200;

    $mail = new LernsessionEndedMail($user, $session, $instance, $participant, true, 8, true);

    expect($mail->user)->toBe($user);
    expect($mail->session)->toBe($session);
    expect($mail->instance)->toBe($instance);
    expect($mail->participant)->toBe($participant);
    expect($mail->isWinner)->toBeTrue();
    expect($mail->totalParticipants)->toBe(8);
    expect($mail->hasLootbox)->toBeTrue();
});

test('LernsessionEndedMail has no attachments', function () {
    $user = new User();
    $session = new LearningSession();
    $session->scope = 'global';
    $instance = new LearningSessionInstance();
    $participant = new LearningSessionParticipant();

    $mail = new LernsessionEndedMail($user, $session, $instance, $participant, false, 1, false);

    expect($mail->attachments())->toBe([]);
});
