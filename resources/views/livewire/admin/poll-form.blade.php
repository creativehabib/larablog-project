<div>
    <form wire:submit.prevent="save" class="card card-fluid">
        <div class="card-body">
            <div class="form-group">
                <label for="pollQuestion">Poll question <span class="text-danger">*</span></label>
                <input type="text" id="pollQuestion" class="form-control @error('question') is-invalid @enderror" wire:model.defer="question" placeholder="What question are you asking?">
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="pollDate">Poll date</label>
                    <input type="date" id="pollDate" class="form-control @error('poll_date') is-invalid @enderror" wire:model.defer="poll_date">
                    @error('poll_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-8">
                    <label for="pollSource">Source URL</label>
                    <input type="url" id="pollSource" class="form-control @error('source_url') is-invalid @enderror" wire:model.defer="source_url" placeholder="https://example.com/poll">
                    @error('source_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="pollIsActive" wire:model.defer="is_active">
                    <label class="custom-control-label" for="pollIsActive">Poll is open for voting</label>
                </div>
                <small class="form-text text-muted">Turn this off to stop accepting votes instantly.</small>
            </div>

            <div class="card card-body border">
                <h5 class="card-title">Initial vote counts</h5>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="yesVotes">Yes votes</label>
                        <input type="number" min="0" id="yesVotes" class="form-control @error('yes_votes') is-invalid @enderror" wire:model.defer="yes_votes">
                        @error('yes_votes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="noVotes">No votes</label>
                        <input type="number" min="0" id="noVotes" class="form-control @error('no_votes') is-invalid @enderror" wire:model.defer="no_votes">
                        @error('no_votes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="noOpinionVotes">No opinion votes</label>
                        <input type="number" min="0" id="noOpinionVotes" class="form-control @error('no_opinion_votes') is-invalid @enderror" wire:model.defer="no_opinion_votes">
                        @error('no_opinion_votes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mt-4">
                <label for="pollImage">Poll image</label>
                <input type="file" id="pollImage" class="form-control-file @error('image') is-invalid @enderror" wire:model="image" accept="image/*">
                @error('image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                <div class="mt-3">
                    @if ($image)
                        <p class="text-muted small mb-2">Preview:</p>
                        <img src="{{ $image->temporaryUrl() }}" alt="Poll image preview" class="img-thumbnail" style="max-height: 180px;">
                    @elseif ($existingImage)
                        <p class="text-muted small mb-2">Current image:</p>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('storage/' . $existingImage) }}" alt="Poll image" class="img-thumbnail" style="max-height: 180px;">
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeExistingImage">Remove</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('admin.polls.index') }}" class="btn btn-link">Cancel</a>
            <button type="submit" class="btn btn-primary ml-2">
                {{ $poll ? 'Update Poll' : 'Create Poll' }}
            </button>
        </div>
    </form>
</div>
