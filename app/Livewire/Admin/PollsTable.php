<?php

namespace App\Livewire\Admin;

use App\Models\Poll;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class PollsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = 'all';

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $pollId): void
    {
        Gate::authorize('poll.edit');

        $poll = Poll::findOrFail($pollId);
        $poll->update(['is_active' => ! $poll->is_active]);

        $message = $poll->is_active
            ? 'Poll reopened for voting.'
            : 'Poll voting has been closed.';

        $this->dispatch('showToastr', type: 'success', message: $message);
    }

    public function deletePoll(int $pollId): void
    {
        Gate::authorize('poll.delete');

        $poll = Poll::findOrFail($pollId);

        if ($poll->image) {
            Storage::disk('public')->delete($poll->image);
        }

        $poll->delete();

        $this->dispatch('showToastr', type: 'success', message: 'Poll removed successfully.');

        $this->resetPage();
    }

    public function render()
    {
        $polls = Poll::query()
            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($nested) use ($search) {
                    $nested->where('question', 'like', $search)
                        ->orWhere('source_url', 'like', $search);
                });
            })
            ->when($this->status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->status === 'closed', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('poll_date')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.polls-table', [
            'polls' => $polls,
        ]);
    }
}
