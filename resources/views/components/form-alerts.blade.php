<div>
    <div class="mb-3">
        @if(Session::get('info'))
            <div class="alert alert-secondary text-left">
                <span class="fa fa-lg fa-info-circle text-muted mr-2"></span> {!! Session::get('info') !!}
                <button class="close" data-dismiss="alert" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
            @if(Session::get('fail'))
                <div class="alert alert-danger text-left">
                    <span class="fa fa-lg fa-exclamation-triangle text-muted mr-2"></span> {!! Session::get('fail') !!}
                    <button class="close" data-dismiss="alert" aria-label="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(Session::get('success'))
                <div class="alert alert-success text-left">
                    <span class="fa fa-lg fa-exclamation-triangle text-muted mr-2"></span> {!! Session::get('success') !!}
                    <button class="close" data-dismiss="alert" aria-label="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
    </div>
</div>
