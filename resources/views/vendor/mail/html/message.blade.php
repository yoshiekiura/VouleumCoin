@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            @if(! empty(site_logo('site_logo')))
            <img height="40" src="{{ site_logo('site_logo') }}" alt="{{ site_info('name') }}">
            @else
            {{ config('settings.site_name', config('app.name', 'TokenLite')) }}
            @endif
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            Copyright © {{ date('Y') }} {{ config('settings.site_name', config('app.name', 'TokenLite')) }}. {{ get_setting('site_copyright', 'All rights reserved.') }}
        @endcomponent
    @endslot
@endcomponent
