@if (!empty($socialLinks))
    @foreach ($socialLinks as $platform => $url)
        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $platform }}">
            @switch($platform)
                @case('facebook')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M13.5 21v-7.5h2.5l.5-3h-3V8.5c0-.87.24-1.5 1.53-1.5H16.5V4.32c-.26-.035-1.16-.12-2.2-.12-2.18 0-3.68 1.33-3.68 3.78V10.5h-2.5v3h2.5V21h2.88z"/></svg>
                    @break
                @case('twitter')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M6 18L18 6" /></svg>
                    @break
                @case('instagram')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17" cy="7" r="1" fill="currentColor" stroke="none" /></svg>
                    @break
                @case('tiktok')
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M16.5 3c.3 2.5 2 4.3 4.5 4.6v3c-1.6 0-3.1-.5-4.5-1.4v6.6c0 3.5-2.8 6.2-6.3 6.2S4 18.7 4 15.2s2.8-6.2 6.2-6.2c.4 0 .8 0 1.2.1v3.1c-.4-.1-.8-.2-1.2-.2-1.7 0-3.1 1.4-3.1 3.1S8.5 18.2 10.2 18.2s3.1-1.4 3.1-3.1V3h3.2z"/></svg>
                    @break
            @endswitch
        </a>
    @endforeach
@endif
