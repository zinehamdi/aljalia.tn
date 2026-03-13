<x-mail::message>
# مرحباً بك يا {{ explode(' ', $user->name)[0] }}! / Welcome! 🇹🇳

شكراً لانضمامك إلى **الجالية.tn**! نحن سعداء جداً بوجودك معنا.

Thank you for joining **Aljalia.tn**! We are very happy to have you with us.

يمكنك الآن طرح الأسئلة، الإجابة عليها، البيع، الشراء أو حتى التعرف على أبناء بلدك في منطقتك!
You can now ask questions, sell, buy, and meet your fellow countrymen in your area!

<x-mail::button :url="url('/dashboard')">
ادخل للحي - Enter the neighborhood
</x-mail::button>

مع تحياتنا - Best regards,<br>
فريق {{ config('app.name') }}
</x-mail::message>
