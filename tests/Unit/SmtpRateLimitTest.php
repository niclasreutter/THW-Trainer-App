<?php

use App\Helpers\SmtpRateLimit;
use Carbon\Carbon;
use Symfony\Component\Mailer\Exception\UnexpectedResponseException;

test('isRateLimitError erkennt 554 mit "limit" Wortlaut', function () {
    $message = 'Expected response code "250" but got code "554", with message "554 5.7.0 Your message could not be sent. The limit on the number of allowed outgoing messages was exceeded. Try again later.".';
    $exception = new UnexpectedResponseException($message, 554);

    expect(SmtpRateLimit::isRateLimitError($exception))->toBeTrue();
});

test('isRateLimitError ist false bei 554 ohne "limit"', function () {
    $exception = new UnexpectedResponseException('554 5.7.1 Spam detected.', 554);

    expect(SmtpRateLimit::isRateLimitError($exception))->toBeFalse();
});

test('isRateLimitError ist false bei anderen SMTP-Codes', function () {
    $exception = new UnexpectedResponseException(
        'Expected response code "250" but got code "450", with message "450 limit reached".',
        450
    );

    expect(SmtpRateLimit::isRateLimitError($exception))->toBeFalse();
});

test('isRateLimitError ist false bei normalen Exceptions', function () {
    $exception = new \RuntimeException('Database connection failed');

    expect(SmtpRateLimit::isRateLimitError($exception))->toBeFalse();
});

test('isRateLimitError ist false bei null', function () {
    expect(SmtpRateLimit::isRateLimitError(null))->toBeFalse();
});

test('isRateLimitError prüft die Exception-Kette (getPrevious)', function () {
    $inner = new UnexpectedResponseException(
        'Expected response code "250" but got code "554", with message "554 limit exceeded".',
        554
    );
    $outer = new \RuntimeException('Mail send failed', 0, $inner);

    expect(SmtpRateLimit::isRateLimitError($outer))->toBeTrue();
});

test('calculateRetryDelay: 08:33 → 09:10 (37 Minuten)', function () {
    $now = Carbon::create(2026, 5, 12, 8, 33, 0);

    expect(SmtpRateLimit::calculateRetryDelay($now))->toBe(37 * 60);
});

test('calculateRetryDelay: 09:05 → 10:10 (65 Minuten)', function () {
    $now = Carbon::create(2026, 5, 12, 9, 5, 0);

    expect(SmtpRateLimit::calculateRetryDelay($now))->toBe(65 * 60);
});

test('calculateRetryDelay: 09:10 → 10:10 (genau 60 Minuten)', function () {
    $now = Carbon::create(2026, 5, 12, 9, 10, 0);

    expect(SmtpRateLimit::calculateRetryDelay($now))->toBe(60 * 60);
});

test('calculateRetryDelay: 09:59 → 10:10 (11 Minuten)', function () {
    $now = Carbon::create(2026, 5, 12, 9, 59, 0);

    expect(SmtpRateLimit::calculateRetryDelay($now))->toBe(11 * 60);
});

test('calculateRetryDelay: exakt zur vollen Stunde → 70 Minuten', function () {
    $now = Carbon::create(2026, 5, 12, 9, 0, 0);

    expect(SmtpRateLimit::calculateRetryDelay($now))->toBe(70 * 60);
});

test('calculateRetryDelay: nie unter 60 Sekunden', function () {
    // Edge-Case: 09:09:59 → ~1 Minute bis 09:10 wäre möglich,
    // wir nutzen aber "NÄCHSTE volle Stunde +10", also 10:10 → ~61 min
    $now = Carbon::create(2026, 5, 12, 9, 9, 59);

    expect(SmtpRateLimit::calculateRetryDelay($now))->toBeGreaterThanOrEqual(60);
});

test('nextRetryAt liefert die nächste volle Stunde + 10 Minuten', function () {
    $now = Carbon::create(2026, 5, 12, 8, 33, 0);

    $retry = SmtpRateLimit::nextRetryAt($now);

    expect($retry->format('H:i'))->toBe('09:10');
    expect($retry->second)->toBe(0);
});

test('nextRetryAt: 23:45 → 00:10 (nächster Tag)', function () {
    $now = Carbon::create(2026, 5, 12, 23, 45, 0);

    $retry = SmtpRateLimit::nextRetryAt($now);

    expect($retry->format('Y-m-d H:i'))->toBe('2026-05-13 00:10');
});
