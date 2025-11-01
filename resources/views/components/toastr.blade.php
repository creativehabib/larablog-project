@php
    $toastrSessionMap = [
        'success' => 'success',
        'status' => 'success',
        'fail' => 'error',
        'error' => 'error',
        'warning' => 'warning',
        'info' => 'info',
        'message' => 'info',
    ];

    $queuedToasts = [];
    foreach ($toastrSessionMap as $sessionKey => $toastType) {
        if (session()->has($sessionKey)) {
            $queuedToasts[] = [
                'type' => $toastType,
                'message' => session()->get($sessionKey),
            ];
        }
    }

    $errorToasts = [];

    if (isset($errors)) {
        $viewErrors = $errors;
    } elseif (session()->has('errors')) {
        $viewErrors = session('errors');
    } else {
        $viewErrors = app('view')->shared('errors');
    }
    if ($viewErrors instanceof \Illuminate\Support\ViewErrorBag) {
        $viewErrors = $viewErrors->getBag('default');
    }

    if ($viewErrors instanceof \Illuminate\Support\MessageBag && $viewErrors->any()) {
        foreach ($viewErrors->all() as $errorMessage) {
            $errorToasts[] = [
                'type' => 'error',
                'message' => $errorMessage,
            ];
        }
    }
@endphp
<script>
    (function () {
        const toastOptions = {
            closeButton: true,
            progressBar: true,
            newestOnTop: true,
            timeOut: '5000',
            extendedTimeOut: '2000',
        };

        const ensureFallbackToastr = () => {
            if (typeof window.toastr !== 'undefined') {
                return window.toastr;
            }

            const styleId = 'app-toastr-fallback-styles';
            if (!document.getElementById(styleId)) {
                const style = document.createElement('style');
                style.id = styleId;
                style.textContent = `
                    #app-toastr-fallback-container {
                        position: fixed;
                        top: 1rem;
                        right: 1rem;
                        z-index: 1080;
                        display: flex;
                        flex-direction: column;
                        align-items: flex-end;
                        gap: 0.75rem;
                        pointer-events: none;
                    }

                    .app-toastr {
                        min-width: 240px;
                        max-width: 360px;
                        color: #0f172a;
                        background-color: #ffffff;
                        border-radius: 0.5rem;
                        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.15);
                        border-left: 4px solid #3b82f6;
                        padding: 0.75rem 1rem;
                        font-size: 0.9375rem;
                        line-height: 1.45;
                        position: relative;
                        overflow: hidden;
                        opacity: 0;
                        transform: translateY(12px);
                        transition: opacity 0.2s ease, transform 0.2s ease;
                        pointer-events: auto;
                    }

                    .app-toastr--visible {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    .app-toastr--success { border-color: #16a34a; }
                    .app-toastr--info { border-color: #0ea5e9; }
                    .app-toastr--warning { border-color: #f97316; }
                    .app-toastr--error { border-color: #dc2626; }

                    .app-toastr__message {
                        margin: 0;
                        margin-right: 1.5rem;
                        word-break: break-word;
                    }

                    .app-toastr__close {
                        position: absolute;
                        top: 0.35rem;
                        right: 0.5rem;
                        background: transparent;
                        border: none;
                        color: inherit;
                        font-size: 1rem;
                        cursor: pointer;
                        line-height: 1;
                        opacity: 0.7;
                        transition: opacity 0.2s ease;
                    }

                    .app-toastr__close:hover {
                        opacity: 1;
                    }

                    .app-toastr__progress {
                        position: absolute;
                        left: 0;
                        bottom: 0;
                        height: 3px;
                        background-color: rgba(255, 255, 255, 0.7);
                        width: 100%;
                        transition: width linear;
                    }

                    .app-toastr.is-hiding {
                        opacity: 0;
                        transform: translateY(-6px);
                    }
                `;
                document.head.appendChild(style);
            }

            const defaultOptions = {
                closeButton: true,
                progressBar: true,
                newestOnTop: true,
                timeOut: 5000,
                extendedTimeOut: 2000,
            };

            let activeOptions = { ...defaultOptions };

            const parseDuration = (value, fallback) => {
                const numeric = parseInt(value, 10);
                return Number.isFinite(numeric) && numeric >= 0 ? numeric : fallback;
            };

            const ensureContainer = () => {
                let container = document.getElementById('app-toastr-fallback-container');
                if (container) {
                    return container;
                }

                container = document.createElement('div');
                container.id = 'app-toastr-fallback-container';
                const appendContainer = () => {
                    document.body.appendChild(container);
                };

                if (document.body) {
                    appendContainer();
                } else {
                    document.addEventListener('DOMContentLoaded', appendContainer, { once: true });
                }

                return container;
            };

            const removeToast = (toast) => {
                if (!toast) {
                    return;
                }

                toast.classList.add('is-hiding');
                window.setTimeout(() => {
                    const parent = toast.parentElement;
                    toast.remove();
                    if (parent && parent.children.length === 0) {
                        parent.remove();
                    }
                }, 200);
            };

            const progressAccent = {
                success: '#16a34a',
                info: '#0ea5e9',
                warning: '#f97316',
                error: '#dc2626',
            };

            const showToast = (type, message, overrideOptions = {}) => {
                if (!message) {
                    return;
                }

                const container = ensureContainer();
                const options = { ...activeOptions, ...overrideOptions };
                const toast = document.createElement('div');
                toast.className = `app-toastr app-toastr--${type}`;

                const text = document.createElement('p');
                text.className = 'app-toastr__message';
                text.textContent = String(message);
                toast.appendChild(text);

                if (options.closeButton) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'app-toastr__close';
                    button.setAttribute('aria-label', 'Close notification');
                    button.innerHTML = '&times;';
                    button.addEventListener('click', () => removeToast(toast));
                    toast.appendChild(button);
                }

                let hideTimer = null;
                const timeOut = parseDuration(options.timeOut, defaultOptions.timeOut);
                const extendedTime = parseDuration(options.extendedTimeOut, defaultOptions.extendedTimeOut);

                const startHideTimer = (duration) => {
                    if (duration <= 0) {
                        return;
                    }

                    hideTimer = window.setTimeout(() => removeToast(toast), duration);
                };

                if (options.progressBar && timeOut > 0) {
                    const progress = document.createElement('div');
                    progress.className = 'app-toastr__progress';
                    progress.style.backgroundColor = progressAccent[type] || progressAccent.info;
                    progress.style.transitionDuration = `${timeOut}ms`;
                    toast.appendChild(progress);
                    window.requestAnimationFrame(() => {
                        progress.style.width = '0%';
                    });
                }

                toast.addEventListener('mouseenter', () => {
                    if (hideTimer) {
                        window.clearTimeout(hideTimer);
                        hideTimer = null;
                    }
                });

                toast.addEventListener('mouseleave', () => {
                    if (!hideTimer) {
                        startHideTimer(extendedTime);
                    }
                });

                if (options.newestOnTop) {
                    container.prepend(toast);
                } else {
                    container.appendChild(toast);
                }

                window.requestAnimationFrame(() => {
                    toast.classList.add('app-toastr--visible');
                });

                startHideTimer(timeOut);
            };

            const createMethod = (type) => (message, _title, overrideOptions = {}) => {
                showToast(type, message, overrideOptions);
            };

            const fallbackToastr = createMethod('info');
            fallbackToastr.success = createMethod('success');
            fallbackToastr.info = createMethod('info');
            fallbackToastr.warning = createMethod('warning');
            fallbackToastr.error = createMethod('error');

            fallbackToastr.clear = () => {
                const container = document.getElementById('app-toastr-fallback-container');
                if (container) {
                    container.remove();
                }
            };

            Object.defineProperty(fallbackToastr, 'options', {
                get() {
                    return { ...activeOptions };
                },
                set(value) {
                    activeOptions = { ...defaultOptions, ...(value || {}) };
                },
            });

            window.toastr = fallbackToastr;
            return fallbackToastr;
        };

        const assignOptions = (instance, options) => {
            if (!instance) {
                return;
            }

            try {
                instance.options = options;
            } catch (_error) {
                if (typeof instance.options === 'object' && instance.options !== null) {
                    Object.assign(instance.options, options);
                }
            }
        };

        const dispatchQueuedToasts = () => {
            const toastrInstance = ensureFallbackToastr();
            assignOptions(toastrInstance, toastOptions);

            const queued = @json(array_merge($queuedToasts, $errorToasts));
            queued.forEach((toast) => {
                if (!toast || !toast.message) {
                    return;
                }

                const type = toast.type || 'info';
                const handler = typeof toastrInstance[type] === 'function'
                    ? toastrInstance[type]
                    : toastrInstance.info;

                handler.call(toastrInstance, toast.message);
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', dispatchQueuedToasts, { once: true });
        } else {
            dispatchQueuedToasts();
        }

        window.addEventListener('showToastr', function (event) {
            const detail = event && event.detail ? event.detail : {};
            const message = detail.message || '';
            if (!message) {
                return;
            }

            const type = detail.type || 'info';
            const toastrInstance = ensureFallbackToastr();
            assignOptions(toastrInstance, toastOptions);

            const handler = typeof toastrInstance[type] === 'function'
                ? toastrInstance[type]
                : toastrInstance.info;

            handler.call(toastrInstance, message);
        });
    })();
</script>
