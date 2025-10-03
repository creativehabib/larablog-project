@extends('layouts.front')

@section('content')
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-bold text-slate-900 sm:text-4xl">সর্বশেষ মতামত জরিপ</h1>
                <p class="mt-4 text-lg text-slate-600">
                    {{ $seo['description'] ?? 'জনমতের ভিত্তিতে ভোট দিন এবং ফলাফল তাৎক্ষণিক দেখুন।' }}
                </p>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @php($latestPoll = $polls->first())
        @php($displayPoll = $activePoll ?? $latestPoll)

        @if($displayPoll)
            <div class="grid gap-8 lg:grid-cols-[2fr,3fr] items-start">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
                    <h2 class="text-2xl font-semibold text-slate-900">{{ $displayPoll->question }}</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ optional($displayPoll->poll_date)->format('F d, Y') }}</p>
                    @if($displayPoll->source_url)
                        <p class="mt-1 text-xs">
                            <a href="{{ $displayPoll->source_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-700">সূত্র দেখুন</a>
                        </p>
                    @endif

                    @if($activePoll && $displayPoll->id === $activePoll->id)
                        <form action="{{ route('polls.vote', $activePoll) }}" method="POST" class="mt-6 space-y-3">
                            @csrf
                            <label class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 hover:border-indigo-500">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="option" value="yes" class="h-4 w-4 text-indigo-600" required>
                                    <span class="font-medium text-slate-800">হ্যাঁ ({{ $activePoll->yes_vote_bangla }} ভোট)</span>
                                </div>
                                <span class="text-sm text-slate-500">{{ $activePoll->yes_vote_percent_bangla }}%</span>
                            </label>
                            <label class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 hover:border-indigo-500">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="option" value="no" class="h-4 w-4 text-indigo-600" required>
                                    <span class="font-medium text-slate-800">না ({{ $activePoll->no_vote_bangla }} ভোট)</span>
                                </div>
                                <span class="text-sm text-slate-500">{{ $activePoll->no_vote_percent_bangla }}%</span>
                            </label>
                            <label class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 hover:border-indigo-500">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="option" value="no_opinion" class="h-4 w-4 text-indigo-600" required>
                                    <span class="font-medium text-slate-800">মতামত নেই ({{ $activePoll->no_opinion_bangla }} ভোট)</span>
                                </div>
                                <span class="text-sm text-slate-500">{{ $activePoll->no_opinion_vote_percent_bangla }}%</span>
                            </label>
                            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                                এখনই ভোট দিন
                            </button>
                        </form>
                    @else
                        <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            এই জরিপের ভোট গ্রহণ বন্ধ রয়েছে। ফলাফল নিচে দেখুন।
                        </div>
                    @endif

                    <p class="mt-4 text-xs text-slate-500">মোট ভোট: {{ $displayPoll->total_vote_bangla }} | প্রকাশিত: {{ $displayPoll->poll_date_bangla }}</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-slate-900">ফলাফল</h3>
                    <ul class="mt-6 space-y-4">
                        <li>
                            <div class="flex items-center justify-between text-sm font-medium text-slate-700">
                                <span>হ্যাঁ</span>
                                <span>{{ $displayPoll->yes_vote_percent }}%</span>
                            </div>
                            <div class="mt-2 h-2 w-full rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $displayPoll->yes_vote_percent }}%"></div>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center justify-between text-sm font-medium text-slate-700">
                                <span>না</span>
                                <span>{{ $displayPoll->no_vote_percent }}%</span>
                            </div>
                            <div class="mt-2 h-2 w-full rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-rose-500" style="width: {{ $displayPoll->no_vote_percent }}%"></div>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center justify-between text-sm font-medium text-slate-700">
                                <span>মতামত নেই</span>
                                <span>{{ $displayPoll->no_opinion_vote_percent }}%</span>
                            </div>
                            <div class="mt-2 h-2 w-full rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-amber-500" style="width: {{ $displayPoll->no_opinion_vote_percent }}%"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-slate-200 bg-white px-6 py-8 text-center text-slate-600">
                বর্তমানে কোনো জরিপ উপলব্ধ নেই।
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">ছবি</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">প্রশ্ন</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">তারিখ</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">স্থিতি</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">মোট ভোট</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">হ্যাঁ</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">না</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">মতামত নেই</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($polls as $poll)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if($poll->image)
                                        <img src="{{ asset('storage/'.$poll->image) }}" alt="{{ $poll->question }}" class="h-12 w-12 rounded object-cover">
                                    @else
                                        <span class="text-xs text-slate-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                    {{ $poll->question }}
                                    @if($poll->source_url)
                                        <div class="mt-1 text-xs font-normal">
                                            <a href="{{ $poll->source_url }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-700">সূত্র</a>
                                        </div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $poll->poll_date_bangla }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $poll->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $poll->is_active ? 'সক্রিয়' : 'বন্ধ' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                    <div>{{ $poll->total_votes }} ({{ $poll->total_vote_bangla }})</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                    <div>{{ $poll->yes_votes }} ({{ $poll->yes_vote_bangla }})</div>
                                    <div class="text-xs text-slate-400">{{ $poll->yes_vote_percent }}% / {{ $poll->yes_vote_percent_bangla }}%</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                    <div>{{ $poll->no_votes }} ({{ $poll->no_vote_bangla }})</div>
                                    <div class="text-xs text-slate-400">{{ $poll->no_vote_percent }}% / {{ $poll->no_vote_percent_bangla }}%</div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">
                                    <div>{{ $poll->no_opinion_votes }} ({{ $poll->no_opinion_bangla }})</div>
                                    <div class="text-xs text-slate-400">{{ $poll->no_opinion_vote_percent }}% / {{ $poll->no_opinion_vote_percent_bangla }}%</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">কোনো জরিপ পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-4 py-4">
                {{ $polls->links() }}
            </div>
        </div>
    </section>
@endsection
