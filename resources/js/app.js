import './bootstrap';
import 'jquery';
import 'pace-progress';
import '@fortawesome/fontawesome-free/js/all.js';
import feather from 'feather-icons';
import "toastr";
import './media-manager';
// পেজ প্রথমবার লোড হওয়ার জন্য
document.addEventListener('DOMContentLoaded', () => {
    feather.replace();

    const pollVoteForm = document.querySelector('#pollVoteForm');

    if (pollVoteForm) {
        const statusElement = document.querySelector('[data-poll-status]');
        const submitButton = pollVoteForm.querySelector('button[type="submit"]');
        const csrfInput = pollVoteForm.querySelector('input[name="_token"]');
        const csrfToken = csrfInput?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const pollId = pollVoteForm.dataset.pollId;
        const successMessage = pollVoteForm.dataset.successMessage || '';
        const errorMessage = pollVoteForm.dataset.errorMessage || '';
        const alreadyVotedMessage = pollVoteForm.dataset.alreadyVotedMessage || '';
        const autoSubmitEnabled = pollVoteForm.dataset.autoSubmit === 'true';
        const statusClasses = {
            base: ['rounded-lg', 'border', 'px-4', 'py-3', 'text-sm'],
            success: ['border-green-200', 'bg-green-50', 'text-green-700'],
            error: ['border-red-200', 'bg-red-50', 'text-red-700'],
        };

        const countsBangla = {
            yes: document.querySelector('[data-poll-count-bangla="yes"]'),
            no: document.querySelector('[data-poll-count-bangla="no"]'),
            no_opinion: document.querySelector('[data-poll-count-bangla="no_opinion"]'),
        };

        const percentagesBangla = {
            yes: document.querySelector('[data-poll-percent-bangla="yes"]'),
            no: document.querySelector('[data-poll-percent-bangla="no"]'),
            no_opinion: document.querySelector('[data-poll-percent-bangla="no_opinion"]'),
        };

        const percentages = {
            yes: document.querySelector('[data-poll-percent="yes"]'),
            no: document.querySelector('[data-poll-percent="no"]'),
            no_opinion: document.querySelector('[data-poll-percent="no_opinion"]'),
        };

        const progressBars = {
            yes: document.querySelector('[data-poll-progress="yes"]'),
            no: document.querySelector('[data-poll-progress="no"]'),
            no_opinion: document.querySelector('[data-poll-progress="no_opinion"]'),
        };

        const totalBangla = document.querySelector('[data-poll-total-bangla]');
        const pollInputs = pollVoteForm.querySelectorAll('input[name="option"], input[name="poll_vote"]');
        let isSubmitting = false;

        const normalizeOption = (value) => {
            if (!value) {
                return value;
            }

            if (value === 'no_comment') {
                return 'no_opinion';
            }

            return value;
        };

        const showStatus = (message, type = 'success') => {
            if (!statusElement) {
                return;
            }

            Object.values(statusClasses).forEach((classes) => {
                statusElement.classList.remove(...classes);
            });

            statusElement.classList.add(...statusClasses.base);

            if (type === 'error') {
                statusElement.classList.add(...statusClasses.error);
            } else {
                statusElement.classList.add(...statusClasses.success);
            }

            statusElement.textContent = message;
            statusElement.classList.remove('hidden');
        };

        const disableForm = () => {
            pollInputs.forEach((input) => {
                input.disabled = true;
            });

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-60', 'cursor-not-allowed');
            }
        };

        const enableForm = () => {
            pollInputs.forEach((input) => {
                input.disabled = false;
            });

            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-60', 'cursor-not-allowed');
            }

            isSubmitting = false;
        };

        const updatePollStats = (poll) => {
            if (!poll) {
                return;
            }

            if (countsBangla.yes) {
                countsBangla.yes.textContent = poll.totals_bangla.yes;
            }

            if (countsBangla.no) {
                countsBangla.no.textContent = poll.totals_bangla.no;
            }

            if (countsBangla.no_opinion) {
                countsBangla.no_opinion.textContent = poll.totals_bangla.no_opinion;
            }

            if (percentagesBangla.yes) {
                percentagesBangla.yes.textContent = `${poll.percentages_bangla.yes}%`;
            }

            if (percentagesBangla.no) {
                percentagesBangla.no.textContent = `${poll.percentages_bangla.no}%`;
            }

            if (percentagesBangla.no_opinion) {
                percentagesBangla.no_opinion.textContent = `${poll.percentages_bangla.no_opinion}%`;
            }

            if (percentages.yes) {
                percentages.yes.textContent = `${poll.percentages.yes}%`;
            }

            if (percentages.no) {
                percentages.no.textContent = `${poll.percentages.no}%`;
            }

            if (percentages.no_opinion) {
                percentages.no_opinion.textContent = `${poll.percentages.no_opinion}%`;
            }

            if (progressBars.yes) {
                progressBars.yes.style.width = `${poll.percentages.yes}%`;
            }

            if (progressBars.no) {
                progressBars.no.style.width = `${poll.percentages.no}%`;
            }

            if (progressBars.no_opinion) {
                progressBars.no_opinion.style.width = `${poll.percentages.no_opinion}%`;
            }

            if (totalBangla) {
                totalBangla.textContent = poll.totals_bangla.total;
            }
        };

        const markAsVoted = () => {
            if (pollId) {
                window.localStorage.setItem(`poll_voted_${pollId}`, '1');
            }
        };

        const hasVoted = pollId && window.localStorage.getItem(`poll_voted_${pollId}`) === '1';

        if (hasVoted) {
            disableForm();

            if (alreadyVotedMessage) {
                showStatus(alreadyVotedMessage, 'success');
            }
        }

        if (autoSubmitEnabled) {
            pollInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    if (input.disabled || isSubmitting) {
                        return;
                    }

                    pollVoteForm.requestSubmit();
                });
            });
        }

        const handleSubmit = async (event) => {
            event.preventDefault();

            if (isSubmitting) {
                return;
            }

            if (!submitButton) {
                return;
            }

            const formData = new FormData(pollVoteForm);
            let selectedOption = formData.get('option') || formData.get('poll_vote');
            selectedOption = normalizeOption(selectedOption);

            if (!selectedOption) {
                return;
            }

            formData.set('option', selectedOption);
            if (formData.has('poll_vote')) {
                formData.delete('poll_vote');
            }

            isSubmitting = true;
            disableForm();

            try {
                const response = await fetch(pollVoteForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    },
                    body: formData,
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    const message = errorData?.message || errorMessage;
                    showStatus(message, 'error');
                    enableForm();
                    return;
                }

                const data = await response.json();
                updatePollStats(data.poll);
                markAsVoted();
                showStatus(data.message || successMessage, 'success');
            } catch (error) {
                enableForm();
                pollVoteForm.removeEventListener('submit', handleSubmit);
                pollVoteForm.submit();
            }
        };

        pollVoteForm.addEventListener('submit', handleSubmit);
    }
});

// Livewire পেজের কোনো অংশ আপডেট করা শেষ করলে এই ইভেন্টটি কাজ করবে
document.addEventListener('livewire:morph.end', () => {
    feather.replace();
});

