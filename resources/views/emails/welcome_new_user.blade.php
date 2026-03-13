<x-mail::message>
# New User Registration

A new user just registered on **الجالية - Aljalia**.

**Name:** {{ $user->name }}
**Email:** {{ $user->email }}
**Country:** {{ $user->country?->name ?? 'Not set yet' }}
**City:** {{ $user->city?->name ?? 'Not set yet' }}
**Joined at:** {{ $user->created_at->format('d M Y, H:i') }}

<x-mail::button :url="url('/admin/users')">
View in Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
