<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantPDFController extends Controller
{
    /**
     * Export participants of an event to PDF.
     * Each participant gets a separate page with their name and QR code.
     */
    public function exportPdf(Event $event)
    {
        // احصل على المشاركين
        $participants = $event->participants->map(function ($participant) {
            // توليد QR Code كـ raw PNG string
           $qrSvg = QrCode::format('svg')->size(200)->generate($participant->email);
$participant->qrCodeBase64 = base64_encode($qrSvg);


            return $participant;
        });

        // تمرير البيانات إلى Blade الخاصة بالـ PDF
        $pdf = Pdf::loadView('events.pdf', [
            'event' => $event,
            'participants' => $participants,
        ])->setPaper('A4', 'portrait');

        // تحميل الملف مباشرة
        $fileName = 'participants_event_' . $event->id . '.pdf';
        return $pdf->stream($fileName);
    }
}
