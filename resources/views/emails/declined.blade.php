@component('mail::message')
# Hello {{ $name }},

We regret to inform you that your **WashWise** owner account application has been **declined**.

If you believe this was a mistake, feel free to reach out to our support team for further assistance.

Thanks for your interest in joining WashWise, and we wish you all the best.

@component('mail::button', ['url' => 'mailto:support@washwise.com'])
Contact Support
@endcomponent

Thanks,
**The WashWise Team**
@endcomponent
