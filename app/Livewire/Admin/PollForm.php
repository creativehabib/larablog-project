<?php

namespace App\Livewire\Admin;

use App\Models\Poll;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PollForm extends Component
{
    use WithFileUploads;

    public ?Poll $poll = null;

    public ?string $question = '';
    public ?string $poll_date = null;
    public ?string $source_url = null;
    public bool $is_active = true;
    public string $yes_votes = '0';
    public string $no_votes = '0';
    public string $no_opinion_votes = '0';
    public $image;
    public ?string $existingImage = null;

    public function mount(?Poll $poll = null): void
    {
        $this->poll = $poll;

        if ($poll) {
            $this->question = $poll->question;
            $this->poll_date = optional($poll->poll_date)->format('Y-m-d');
            $this->source_url = $poll->source_url;
            $this->is_active = (bool) $poll->is_active;
            $this->yes_votes = (string) $poll->yes_votes;
            $this->no_votes = (string) $poll->no_votes;
            $this->no_opinion_votes = (string) $poll->no_opinion_votes;
            $this->existingImage = $poll->image;
        }
    }

    public function removeExistingImage(): void
    {
        if (! $this->existingImage) {
            return;
        }

        Gate::authorize('poll.edit');

        Storage::disk('public')->delete($this->existingImage);

        if ($this->poll) {
            $this->poll->update(['image' => null]);
        }

        $this->existingImage = null;

        $this->dispatch('showToastr', type: 'success', message: 'Poll image removed successfully.');
    }

    public function save()
    {
        if ($this->poll) {
            Gate::authorize('poll.edit');
        } else {
            Gate::authorize('poll.create');
        }

        $data = $this->validate($this->rules());

        $data['question'] = trim($data['question']);
        $this->question = $data['question'];
        $data['poll_date'] = $data['poll_date'] ?: null;
        $data['source_url'] = $data['source_url'] ? trim($data['source_url']) : null;
        $data['is_active'] = $this->is_active;
        $data['yes_votes'] = (int) ($data['yes_votes'] ?? 0);
        $data['no_votes'] = (int) ($data['no_votes'] ?? 0);
        $data['no_opinion_votes'] = (int) ($data['no_opinion_votes'] ?? 0);

        if ($this->image) {
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }

            $data['image'] = $this->image->store('polls', 'public');
        } else {
            unset($data['image']);
        }

        if ($this->poll && $this->poll->exists) {
            $this->poll->update($data);
            $message = 'Poll updated successfully.';
        } else {
            $this->poll = Poll::create($data);
            $message = 'Poll created successfully.';
        }

        $this->dispatch('showToastr', type: 'success', message: $message);

        return redirect()->route('admin.polls.index')->with('success', $message);
    }

    protected function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:255'],
            'poll_date' => ['nullable', 'date'],
            'source_url' => ['nullable', 'url'],
            'is_active' => ['boolean'],
            'yes_votes' => ['nullable', 'integer', 'min:0'],
            'no_votes' => ['nullable', 'integer', 'min:0'],
            'no_opinion_votes' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.poll-form');
    }
}
