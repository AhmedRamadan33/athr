<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNewsletterAnnouncementRequest;
use App\Models\NewsletterSubscriber;
use App\Services\Admin\NewsletterAnnouncementService;
use App\Services\Admin\NewsletterSubscriberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function __construct(
        protected NewsletterSubscriberService $subscribers,
        protected NewsletterAnnouncementService $announcements,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.newsletter-subscribers.index', [
            'subscribers' => $this->subscribers->paginate($request->only(['search'])),
            'activeCount' => $this->announcements->activeCount(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->subscribers->allFiltered($request->only(['search']));

        return Response::streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['البريد الإلكتروني', 'الحالة', 'تاريخ الاشتراك']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->email,
                    $row->unsubscribed_at ? 'ملغى الاشتراك' : 'مشترك',
                    $row->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'newsletter-subscribers.csv');
    }

    public function compose(): View
    {
        return view('admin.newsletter-subscribers.compose', [
            'activeCount' => $this->announcements->activeCount(),
        ]);
    }

    public function send(SendNewsletterAnnouncementRequest $request): RedirectResponse
    {
        $count = $this->announcements->send($request->validated()['subject'], $request->validated()['body']);

        return redirect()->route('admin.newsletter-subscribers.index')
            ->with('success', 'جارٍ إرسال الإعلان إلى '.$count.' مشترك.');
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->subscribers->delete($subscriber);

        return back()->with('success', 'تم حذف المشترك بنجاح.');
    }
}
