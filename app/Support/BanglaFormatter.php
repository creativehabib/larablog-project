<?php

namespace App\Support;

use Carbon\CarbonInterface;

class BanglaFormatter
{
    /**
     * Mapping of English to Bangla digits.
     */
    protected const DIGIT_MAP = [
        '0' => '০',
        '1' => '১',
        '2' => '২',
        '3' => '৩',
        '4' => '৪',
        '5' => '৫',
        '6' => '৬',
        '7' => '৭',
        '8' => '৮',
        '9' => '৯',
    ];

    /**
     * Mapping of English day names to Bangla.
     */
    protected const DAY_MAP = [
        'Saturday' => 'শনিবার',
        'Sunday' => 'রবিবার',
        'Monday' => 'সোমবার',
        'Tuesday' => 'মঙ্গলবার',
        'Wednesday' => 'বুধবার',
        'Thursday' => 'বৃহস্পতিবার',
        'Friday' => 'শুক্রবার',
    ];

    /**
     * Mapping of month numbers to Bangla names.
     */
    protected const MONTH_MAP = [
        1 => 'জানুয়ারি',
        2 => 'ফেব্রুয়ারি',
        3 => 'মার্চ',
        4 => 'এপ্রিল',
        5 => 'মে',
        6 => 'জুন',
        7 => 'জুলাই',
        8 => 'আগস্ট',
        9 => 'সেপ্টেম্বর',
        10 => 'অক্টোবর',
        11 => 'নভেম্বর',
        12 => 'ডিসেম্বর',
    ];

    /**
     * Convert the supplied value into Bangla digits.
     */
    public static function digits(int|string|float|null $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return strtr((string) $value, self::DIGIT_MAP);
    }

    /**
     * Format the provided date as a Bangla full date string (e.g. "শুক্রবার, ০৩ অক্টোবর ২০২৫").
     */
    public static function fullDate(?CarbonInterface $date, ?string $timezone = null): string
    {
        if (! $date) {
            return '';
        }

        $date = (clone $date)->setTimezone($timezone ?? config('app.timezone', 'Asia/Dhaka'));

        $dayName = self::DAY_MAP[$date->format('l')] ?? $date->format('l');
        $day = self::digits($date->format('d'));
        $month = self::MONTH_MAP[(int) $date->format('n')] ?? $date->format('F');
        $year = self::digits($date->format('Y'));

        return sprintf('%s, %s %s %s', $dayName, $day, $month, $year);
    }

    /**
     * Format the provided date as a Bangla date string without the day name.
     */
    public static function shortDate(?CarbonInterface $date, ?string $timezone = null): string
    {
        if (! $date) {
            return '';
        }

        $date = (clone $date)->setTimezone($timezone ?? config('app.timezone', 'Asia/Dhaka'));

        $day = self::digits($date->format('d'));
        $month = self::MONTH_MAP[(int) $date->format('n')] ?? $date->format('F');
        $year = self::digits($date->format('Y'));

        return sprintf('%s %s %s', $day, $month, $year);
    }

    /**
     * Format the provided time into Bangla digits with পূর্বাহ্ণ/অপরাহ্ণ markers.
     */
    public static function time(?CarbonInterface $date, ?string $timezone = null): string
    {
        if (! $date) {
            return '';
        }

        $date = (clone $date)->setTimezone($timezone ?? config('app.timezone', 'Asia/Dhaka'));

        $time = self::digits($date->format('h:i'));
        $period = $date->format('a') === 'am' ? 'পূর্বাহ্ণ' : 'অপরাহ্ণ';

        return trim($time . ' ' . $period);
    }
}
