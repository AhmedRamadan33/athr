<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\SubscribeNewsletterRequest;
use App\Models\NewsletterSubscriber;
use App\Services\Storefront\NewsletterService;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function __construct(protected NewsletterService $newsletter) {}

    public function store(SubscribeNewsletterRequest $request): RedirectResponse
    {
        $this->newsletter->subscribe($request->validated()['email']);

        return back()->with('success', 'تم تسجيل بريدك بنجاح — أهلاً بك فى نادى أثر.');
    }

    public function unsubscribe(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->newsletter->unsubscribe($subscriber);

        return redirect()->route('storefront.home')->with('success', 'تم إلغاء اشتراكك فى النشرة البريدية بنجاح.');
    }
}
