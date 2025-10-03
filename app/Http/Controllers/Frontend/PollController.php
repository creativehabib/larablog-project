<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Poll;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PollController extends Controller
{
    /**
     * Display a listing of the polls.
     */
    public function index(): View
    {
        $settings = $this->settings();
        $polls = Poll::query()
            ->orderByDesc('poll_date')
            ->orderByDesc('created_at')
            ->paginate(10);

        $activePoll = Poll::query()
            ->where('is_active', true)
            ->orderByDesc('poll_date')
            ->orderByDesc('created_at')
            ->first();

        $seo = [
            'title' => 'Opinion Polls | ' . ($settings?->site_title ?? config('app.name')),
            'description' => 'Read the latest opinion polls and cast your vote on current issues.',
            'type' => 'website',
            'canonical' => route('polls.index'),
            'indexable' => true,
        ];

        return view('front.polls.index', compact('polls', 'seo', 'settings', 'activePoll'));
    }

    /**
     * Store the vote for the provided poll.
     */
    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        $validated = $request->validate([
            'option' => ['required', 'in:yes,no,no_opinion'],
        ]);

        $column = match ($validated['option']) {
            'yes' => 'yes_votes',
            'no' => 'no_votes',
            'no_opinion' => 'no_opinion_votes',
        };

        if (! $poll->is_active) {
            return back()->with('status', 'এই জরিপের ভোট গ্রহণ বন্ধ রয়েছে।');
        }

        $poll->increment($column);

        return back()->with('status', 'আপনার ভোটের জন্য ধন্যবাদ!');
    }

    /**
     * Retrieve the cached general settings instance.
     */
    protected function settings(): ?GeneralSetting
    {
        return Cache::remember('general_settings', now()->addHour(), function () {
            return GeneralSetting::query()->first();
        });
    }
}
