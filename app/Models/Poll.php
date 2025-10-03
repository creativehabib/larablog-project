<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Poll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'image',
        'source_url',
        'poll_date',
        'is_active',
        'yes_votes',
        'no_votes',
        'no_opinion_votes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'poll_date' => 'date',
        'is_active' => 'bool',
    ];

    /**
     * The computed attributes that should be appended to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'total_votes',
        'yes_vote_percent',
        'no_vote_percent',
        'no_opinion_vote_percent',
        'total_vote_bangla',
        'yes_vote_bangla',
        'no_vote_bangla',
        'no_opinion_bangla',
        'poll_date_bangla',
        'yes_vote_percent_bangla',
        'no_vote_percent_bangla',
        'no_opinion_vote_percent_bangla',
    ];

    /**
     * Get the total number of votes.
     */
    protected function totalVotes(): Attribute
    {
        return Attribute::get(function (): int {
            return (int) $this->yes_votes + (int) $this->no_votes + (int) $this->no_opinion_votes;
        });
    }

    /**
     * Get the percentage of "yes" votes.
     */
    protected function yesVotePercent(): Attribute
    {
        return Attribute::get(fn () => $this->calculatePercentage($this->yes_votes));
    }

    /**
     * Get the percentage of "no" votes.
     */
    protected function noVotePercent(): Attribute
    {
        return Attribute::get(fn () => $this->calculatePercentage($this->no_votes));
    }

    /**
     * Get the percentage of "no opinion" votes.
     */
    protected function noOpinionVotePercent(): Attribute
    {
        return Attribute::get(fn () => $this->calculatePercentage($this->no_opinion_votes));
    }

    /**
     * Get the Bangla formatted total vote count.
     */
    protected function totalVoteBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->total_votes));
    }

    /**
     * Get the Bangla formatted yes vote count.
     */
    protected function yesVoteBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->yes_votes));
    }

    /**
     * Get the Bangla formatted no vote count.
     */
    protected function noVoteBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->no_votes));
    }

    /**
     * Get the Bangla formatted no opinion vote count.
     */
    protected function noOpinionBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->no_opinion_votes));
    }

    /**
     * Get the Bangla formatted poll date.
     */
    protected function pollDateBangla(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->poll_date === null) {
                return '';
            }

            $months = [
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

            $date = $this->poll_date instanceof CarbonInterface
                ? $this->poll_date
                : Carbon::parse($this->poll_date);

            return sprintf(
                '%s %s %s',
                $this->toBanglaDigits($date->day),
                $months[$date->month] ?? '',
                $this->toBanglaDigits($date->year)
            );
        });
    }

    /**
     * Get the Bangla formatted yes vote percentage.
     */
    protected function yesVotePercentBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->yes_vote_percent));
    }

    /**
     * Get the Bangla formatted no vote percentage.
     */
    protected function noVotePercentBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->no_vote_percent));
    }

    /**
     * Get the Bangla formatted no opinion vote percentage.
     */
    protected function noOpinionVotePercentBangla(): Attribute
    {
        return Attribute::get(fn () => $this->toBanglaDigits($this->no_opinion_vote_percent));
    }

    /**
     * Convert the supplied number into Bangla digits.
     */
    protected function toBanglaDigits(int|string|null $number): string
    {
        $number = $number ?? 0;

        $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($digits, $banglaDigits, (string) $number);
    }

    /**
     * Calculate the percentage value for the given vote count.
     */
    protected function calculatePercentage(?int $value): int
    {
        $value = $value ?? 0;

        if ($this->total_votes === 0) {
            return 0;
        }

        return (int) round(($value / $this->total_votes) * 100);
    }
}
