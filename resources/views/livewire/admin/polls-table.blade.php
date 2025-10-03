<div>
    <div class="card card-fluid">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                <div class="w-100 w-lg-50">
                    <input type="search" wire:model.live.debounce.500ms="search" class="form-control" placeholder="Search polls by question or source">
                </div>
                <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2 w-100 w-lg-auto">
                    <select wire:model.live="status" class="form-control w-100 w-lg-auto">
                        <option value="all">All statuses</option>
                        <option value="active">Active polls</option>
                        <option value="closed">Closed polls</option>
                    </select>
                    <div class="text-muted small text-lg-right">
                        Showing {{ $polls->firstItem() ?? 0 }} - {{ $polls->lastItem() ?? 0 }} of {{ $polls->total() }} polls
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Poll date</th>
                            <th>Status</th>
                            <th>Votes</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($polls as $poll)
                            <tr wire:key="poll-{{ $poll->id }}">
                                <td>{{ $loop->iteration + ($polls->currentPage() - 1) * $polls->perPage() }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $poll->question }}</div>
                                    @if ($poll->source_url)
                                        <div class="text-muted small">
                                            <a href="{{ $poll->source_url }}" target="_blank" rel="noopener">{{ $poll->source_url }}</a>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ optional($poll->poll_date)->format('d M, Y') ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $poll->is_active ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $poll->is_active ? 'Active' : 'Closed' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-muted small">Total: {{ $poll->total_votes }}</div>
                                    <div class="small">Yes: {{ $poll->yes_votes }} ({{ $poll->yes_vote_percent }}%)</div>
                                    <div class="small">No: {{ $poll->no_votes }} ({{ $poll->no_vote_percent }}%)</div>
                                    <div class="small">No opinion: {{ $poll->no_opinion_votes }} ({{ $poll->no_opinion_vote_percent }}%)</div>
                                </td>
                                <td>{{ $poll->updated_at?->format('d M, Y H:i') ?? '—' }}</td>
                                <td class="text-right">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('poll.edit')
                                            <button type="button" class="btn {{ $poll->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" wire:click="toggleStatus({{ $poll->id }})">
                                                {{ $poll->is_active ? 'Close voting' : 'Reopen voting' }}
                                            </button>
                                            <a href="{{ route('admin.polls.edit', $poll) }}" class="btn btn-outline-secondary">Edit</a>
                                        @endcan
                                        @can('poll.delete')
                                            <button type="button" class="btn btn-outline-danger" wire:click="deletePoll({{ $poll->id }})" onclick="confirm('Are you sure you want to delete this poll?') || event.stopImmediatePropagation()">Delete</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No polls found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $polls->links() }}
            </div>
        </div>
    </div>
</div>
