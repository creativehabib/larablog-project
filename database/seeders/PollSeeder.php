<?php

namespace Database\Seeders;

use App\Models\Poll;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Poll::query()->firstOrCreate(
            [
                'question' => 'নির্বাচনবিরোধী কথা যে-বলুক, তারা রাষ্ট্রের মাথা থেকে মাইনাস হয়ে যাবেন, সালাহউদ্দিন আহমেদের এ মন্তব্যের সঙ্গে আপনি একমত?',
                'poll_date' => Carbon::create(2025, 8, 26),
            ],
            [
                'image' => 'polls/1756214840-68adb638672b3.jpg',
                'source_url' => 'https://bhorerkagoj.com/poll/152',
                'is_active' => true,
                'yes_votes' => 356,
                'no_votes' => 704,
                'no_opinion_votes' => 40,
            ]
        );
    }
}
