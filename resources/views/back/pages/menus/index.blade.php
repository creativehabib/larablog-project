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

        .dd3-handle {
            height: 40px;
            width: 40px;
            margin: 5px 0;
            background: #4c6ef5;
            border-radius: 0.25rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: move;
        }

        .dd3-handle:before {
            content: '\2630';
            font-size: 18px;
        }

        .dd3-content {
            margin: 5px 0;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.25rem;
            background: #fff;
            box-shadow: 0 1px 1px rgba(15, 23, 42, 0.08);
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

            const serializeItems = (items) => {
                return items.map(item => {
                    const children = Array.isArray(item.children) ? serializeItems(item.children) : [];

                    return {
                        id: item.id,
                        ...(children.length ? { children } : {})
                    };
                });
            };

            $el.nestable({
                maxDepth: 3,
                expandBtnHTML: '',
                collapseBtnHTML: ''
            }).on('change', function (e) {
                const list = e.length ? e : $(e.target);
                const structure = list.nestable('serialize');
                const serialized = Array.isArray(structure) ? serializeItems(structure) : [];
                Livewire.dispatch('menuOrderUpdated', { items: serialized });
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
