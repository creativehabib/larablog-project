@extends('back.layout.pages-layout')
@section('pageTitle', 'Menu Management')

@section('content')
    <div class="page-section">
        <div class="section-block">
            <div class="row">
                <div class="col-12 mb-3">
                    <h3 class="section-title">Menu management</h3>
                    <p class="text-muted mb-0">Create, organise and publish navigation menus across your site.</p>
                </div>
                <div class="col-12">
                    <livewire:admin.menu-management />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.css" referrerpolicy="no-referrer" />
    <style>
        .dd {
            max-width: 100%;
        }

        .dd3-content {
            margin: 5px 0;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem;
            background: #fff;
            box-shadow: 0 1px 1px rgba(15, 23, 42, 0.08);
            cursor: move;
        }

        .dd3-content .dd-nodrag {
            cursor: auto;
        }

        .menu-picker {
            max-height: 240px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem;
            padding: 0.75rem;
        }

        .menu-picker .form-check {
            margin-bottom: 0.5rem;
        }

        .menu-picker .form-check:last-child {
            margin-bottom: 0;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js" referrerpolicy="no-referrer"></script>
    <script>
        const initMenuNestable = () => {
            const $el = $('#menuNestable');

            if (!$el.length || typeof $el.nestable !== 'function') {
                return;
            }

            if ($el.data('nestable')) {
                $el.nestable('destroy');
            }

            $el.nestable({
                maxDepth: 3,
                expandBtnHTML: '',
                collapseBtnHTML: ''
            }).on('change', function (e) {
                const list = e.length ? e : $(e.target);
                const structure = list.nestable('serialize');
                Livewire.dispatch('menuOrderUpdated', { items: structure });
            });
        };

        document.addEventListener('livewire:init', () => {
            initMenuNestable();

            Livewire.on('refreshNestable', () => {
                setTimeout(initMenuNestable, 100);
            });
        });
    </script>
@endpush
