<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoController extends Controller
{
    public function show(Request $request): View
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $contactName = config('hospital.outreach.contact_name', 'Shashank');
        $contactEmail = config('hospital.outreach.contact_email', 'sunnyns60@gmail.com');
        $recipientName = trim((string) $request->query('to', ''));

        $greeting = $recipientName !== '' ? "Hi {$recipientName}," : 'Hi,';

        $shareText = implode("\n", [
            "{$greeting}",
            '',
            "I'm *{$contactName}*. We built *".config('hospital.name')."* — ".config('hospital.tagline').'.',
            '',
            'What it does:',
            '✅ Online OPD booking (76+ cities in demo)',
            '✅ Token + QR for every patient',
            '✅ Live queue position & ETA on phone',
            '✅ Admin desk for hospitals & clinics',
            '',
            "I'd love to show you a quick 10-minute demo.",
            '',
            "Reply on WhatsApp or email me at *{$contactEmail}*",
            '',
            'Thanks,',
            $contactName,
        ]);

        return view('patient.promo', [
            'shareUrl' => $baseUrl.'/promo',
            'bookUrl' => $baseUrl.'/book',
            'adminUrl' => $baseUrl.'/admin/login',
            'shareText' => $shareText,
            'contactName' => $contactName,
            'contactEmail' => $contactEmail,
            'recipientName' => $recipientName,
            'whatsappShareUrl' => 'https://wa.me/?text='.rawurlencode($shareText),
        ]);
    }
}
