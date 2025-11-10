class MediaManager {
    constructor() {
        this.queue = [];
        this.currentRequest = null;
        this.modalEventName = 'open-media-modal';
        this.modalOpened = false;
        this.activeModalId = null;
        this.defaultValueSource = 'path';
        this.boundHandleSelection = this.handleSelection.bind(this);
        this.boundHandleModalClosed = this.handleModalClosed.bind(this);
        this.boundHandleModalOpened = this.handleModalOpened.bind(this);

        window.addEventListener('image-selected-browser', this.boundHandleSelection);
        window.addEventListener('media-modal:closed', this.boundHandleModalClosed);
        window.addEventListener('media-modal:opened', this.boundHandleModalOpened);

        this.browseUrl = this.resolveBrowseUrl();
    }

    resolveBrowseUrl() {
        const metaTag = document.querySelector('meta[name="filemanager-browse-url"]');
        if (metaTag && metaTag.content) {
            return metaTag.content;
        }

        const adminBase = document.querySelector('meta[name="admin-base-url"]');
        if (adminBase && adminBase.content) {
            return `${adminBase.content.replace(/\/$/, '')}/media-library`;
        }

        return '/admin/media-library';
    }

    normalize(detail) {
        let payload = detail;

        if (Array.isArray(payload) && payload.length > 0) {
            payload = payload[0];
        }

        if (
            !Array.isArray(payload)
            && typeof payload === 'object'
            && payload !== null
            && !('url' in payload || 'full_url' in payload || 'path' in payload)
            && Object.prototype.hasOwnProperty.call(payload, 0)
        ) {
            payload = payload[0];
        }

        if (typeof payload === 'string') {
            return {
                url: payload,
                path: payload,
            };
        }

        if (typeof payload === 'object' && payload !== null) {
            const resolvedUrl = payload.url ?? payload.full_url ?? payload.path ?? null;
            const resolvedPath = payload.path ?? resolvedUrl ?? null;

            return {
                ...payload,
                ...(resolvedUrl ? { url: resolvedUrl, full_url: resolvedUrl } : {}),
                ...(resolvedPath ? { path: resolvedPath } : {}),
            };
        }

        return null;
    }

    enqueue(request) {
        if (this.currentRequest) {
            this.queue.push(request);
            return request.promise;
        }

        this.currentRequest = request;
        this.modalOpened = false;
        this.openModal(request);

        return request.promise;
    }

    openModal(request) {
        const detail = {
            source: 'media-manager',
            requestId: request.id,
        };

        window.dispatchEvent(new CustomEvent(this.modalEventName, { detail }));
    }

    handleModalOpened(event) {
        if (event?.detail?.id) {
            this.modalOpened = true;
            this.activeModalId = event.detail.id;
        }
    }

    handleModalClosed(event) {
        if (!this.currentRequest) {
            return;
        }

        if (event?.detail?.source === 'media-picker-modal') {
            const request = this.currentRequest;
            this.finishRequest(request, null, true);
        }
    }

    handleSelection(event) {
        if (!this.currentRequest) {
            return;
        }

        const media = this.normalize(event.detail);

        if (!media) {
            console.warn('MediaManager: Unable to resolve media selection detail.', event.detail);
            return;
        }

        const request = this.currentRequest;

        try {
            if (request.type === 'input') {
                this.applyToInput(request, media);
            } else if (request.type === 'callback' && typeof request.callback === 'function') {
                const payload = request.wrapInArray ? [media] : media;
                request.callback(payload, request);
            }

            if (typeof request.onSelect === 'function') {
                request.onSelect(media, request);
            }

            this.finishRequest(request, media, false);
        } catch (error) {
            console.error('MediaManager: Error handling media selection.', error);
            this.finishRequest(request, null, true);
        }
    }

    finishRequest(request, media, cancelled) {
        if (request.resolve && !cancelled) {
            request.resolve(media ?? null);
        }

        if (request.reject && cancelled) {
            request.reject(new Error('Media selection was cancelled.'));
        }

        if (typeof request.onClose === 'function') {
            request.onClose({ cancelled, media, request });
        }

        this.currentRequest = null;
        this.modalOpened = false;
        this.activeModalId = null;

        if (this.queue.length > 0) {
            const nextRequest = this.queue.shift();
            this.currentRequest = nextRequest;
            this.openModal(nextRequest);
        }
    }

    applyToInput(request, media) {
        const input = typeof request.input === 'string'
            ? document.getElementById(request.input)
            : request.input;

        if (!input) {
            console.warn(`MediaManager: Could not find input with id "${request.input}".`);
            return;
        }

        const valueSource = request.valueSource || this.defaultValueSource;
        const value = valueSource === 'url'
            ? (media.url ?? media.full_url ?? media.path)
            : (media.path ?? media.url ?? media.full_url);

        if (value) {
            input.value = value;
            input.dataset.mediaId = media.id ?? '';
            input.dataset.mediaUrl = media.url ?? '';
            input.dataset.mediaPath = media.path ?? '';
        }

        const emitEvent = (eventName) => {
            const evt = new Event(eventName, { bubbles: true });
            input.dispatchEvent(evt);
        };

        emitEvent('input');
        emitEvent('change');

        const previewSelector = request.previewSelector
            || `[data-filemanager-preview-for="${input.id}"]`;

        if (previewSelector) {
            const previewElement = document.querySelector(previewSelector);
            if (previewElement && 'src' in previewElement) {
                previewElement.src = media.url ?? media.full_url ?? media.path ?? '';
                previewElement.classList.remove('d-none');
            }
        }

        if (typeof request.afterApply === 'function') {
            request.afterApply(media, request);
        }
    }

    makeRequest(config) {
        const uniqueId = typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function'
            ? crypto.randomUUID()
            : `media-${Date.now()}-${Math.random().toString(16).slice(2)}`;

        const request = {
            id: uniqueId,
            ...config,
        };

        request.promise = new Promise((resolve, reject) => {
            request.resolve = resolve;
            request.reject = reject;
        });

        return request;
    }

    selectFile(input, options = {}) {
        const request = this.makeRequest({
            type: 'input',
            input,
            valueSource: options.valueSource ?? this.defaultValueSource,
            previewSelector: options.previewSelector ?? null,
            afterApply: options.afterApply ?? null,
            onSelect: options.onSelect ?? null,
            onClose: options.onClose ?? null,
        });

        this.enqueue(request);
        return false;
    }

    bulkSelectFile(callback, options = {}) {
        const resolvedCallback = typeof callback === 'function'
            ? callback
            : (typeof window !== 'undefined' ? window[callback] : null);

        if (typeof resolvedCallback !== 'function') {
            console.warn('MediaManager: bulkSelectFile requires a valid callback.');
            return false;
        }

        const request = this.makeRequest({
            type: 'callback',
            callback: resolvedCallback,
            wrapInArray: options.wrapInArray !== undefined ? options.wrapInArray : true,
            onSelect: options.onSelect ?? null,
            onClose: options.onClose ?? null,
        });

        this.enqueue(request);
        return false;
    }

    open(options = {}) {
        const request = this.makeRequest({
            type: 'open',
            onSelect: options.onSelect ?? null,
            onClose: options.onClose ?? null,
        });

        this.enqueue(request);
        return request.promise;
    }
}

const mediaManagerInstance = new MediaManager();

const publicApi = {
    selectFile: (input, options = {}) => mediaManagerInstance.selectFile(input, options),
    bulkSelectFile: (callback, options = {}) => mediaManagerInstance.bulkSelectFile(callback, options),
    open: (options = {}) => mediaManagerInstance.open(options),
    get ckBrowseUrl() {
        return mediaManagerInstance.browseUrl;
    },
    setBrowseUrl(url) {
        mediaManagerInstance.browseUrl = url;
    },
};

if (typeof window !== 'undefined') {
    window.filemanager = publicApi;
}

export default publicApi;
