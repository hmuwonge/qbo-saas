{{--<script>--}}
    @if (session('failed'))
    <div class="alert alert-danger mb-0 col-6" role="alert">
    <span class="alert-inner--icon"><i class="fe fe-slash me-2"></i></span>
    <span class="alert-inner--text"><strong>Danger!</strong>
            {{ session('failed') }}</span>
    </div>
    @endif

    @if (session('errors'))
        <div class="alert alert-danger mb-0" role="alert">
            <span class="alert-inner--icon"><i class="fe fe-slash me-2"></i></span>
            <span class="alert-inner--text"><strong>Danger!</strong>
            {{ session('errors') }}</span>
        </div>
    @endif

    @if (session('successful'))
        <div class="alert alert-success" role="alert">
                                                        <span class="alert-inner--icon"><i
                                                            class="fe fe-thumbs-up me-2"></i></span>
            <span class="alert-inner--text"><strong>Success!</strong>
            {{ session('successful') }}</span>
        </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success" role="alert">
    <span class="alert-inner--icon"><i
        class="fe fe-thumbs-up me-2"></i></span>
    <span class="alert-inner--text"><strong>Success!</strong>
            {{ session('successful') }}</span>
    </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning" role="alert">
            <span class="alert-inner--icon"><i class="fe fe-info me-2"></i></span>
            <span class="alert-inner--text"><strong>Warning!</strong>
            {{ session('warning') }}</span>
        </div>
    @endif
{{--</script>--}}

{{--<script>--}}
{{--    @if (session('status'))--}}
{{--    notifier.show('Great!', '{{ session('status') }}', 'info',--}}
{{--        '{{ asset('assets/images/notification/survey-48.png') }}', 4000);--}}
{{--    @endif--}}
{{--</script>--}}
