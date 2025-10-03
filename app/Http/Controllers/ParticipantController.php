<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Event;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantController extends Controller
{
    // إضافة مشارك يدويًا مع QR Code
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email',
            'phone' => 'nullable',
        ]);

        $participant = $event->participants()->create($request->only('name', 'email', 'phone'));

        // توليد QR Code وحفظه كملف فقط بدون حفظه في قاعدة البيانات
        $qrFileName = md5($participant->email . time()) . '.png';
        $qrPath = 'qrcodes/' . $qrFileName;
        QrCode::size(200)->generate($participant->email, public_path($qrPath));

        return redirect()->route('events.edit', $event->id)
                        ->with('success', 'Participant added successfully with QR Code!');
    }

    // حذف مشارك
    public function destroy(Event $event, $participantId)
    {
        $participant = $event->participants()->findOrFail($participantId);

        // حذف ملف QR إذا موجود (نبحث عنه باسم email بشكل تقريبي)
        $files = glob(public_path('qrcodes/*.png'));
        foreach ($files as $file) {
            if (strpos($file, md5($participant->email)) !== false) {
                unlink($file);
            }
        }

        $participant->delete();

        return redirect()->route('events.edit', $event->id)
                         ->with('success', 'Participant removed successfully!');
    }

    // حذف جميع المشاركين للحدث الحالي
    public function destroyAll(Event $event)
    {
        $event->participants()->delete();
        return redirect()->route('events.edit', $event->id)
                         ->with('success', 'All participants removed successfully!');
    }

    // تصدير المشاركين إلى Excel
    public function exportExcel(Event $event)
    {
        $participants = $event->participants;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Name')
              ->setCellValue('B1', 'Email')
              ->setCellValue('C1', 'Phone');

        $rowNum = 2;
        foreach ($participants as $p) {
            $sheet->setCellValue('A' . $rowNum, $p->name)
                  ->setCellValue('B' . $rowNum, $p->email)
                  ->setCellValue('C' . $rowNum, $p->phone ?? '');
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'participants_event_' . $event->id . '.xlsx';

        return Response::streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }

    // رفع ملف Excel مؤقتًا في Session
    public function uploadExcel(Request $request, Event $event)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        if (!$file) return redirect()->back()->withErrors('No file uploaded.');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) return redirect()->back()->withErrors('The file is empty or missing headers.');

        $headers = array_map('strtolower', $rows[0]);
        $participants = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $data = [];
            if (($key = array_search('name', $headers)) !== false) $data['name'] = $row[$key];
            if (($key = array_search('email', $headers)) !== false) $data['email'] = $row[$key];
            if (($key = array_search('phone', $headers)) !== false) $data['phone'] = $row[$key] ?? null;

            if (!empty($data['name']) && !empty($data['email'])) {
                $participants[] = $data;
            }
        }

        session(['uploaded_participants' => $participants]);

        return redirect()->route('events.edit', $event->id)
                        ->with('success', 'File uploaded! Press "Add Participants" to insert them.');
    }

    // إضافة المشاركين من Session إلى قاعدة البيانات مع QR Code
    public function addUploadedParticipants(Event $event)
    {
        $participants = session('uploaded_participants', []);

        foreach ($participants as $data) {
            if (!$event->participants()->where('email', $data['email'])->exists()) {
                $participant = $event->participants()->create($data);

                // توليد QR Code وحفظه فقط
                $qrFileName = md5($participant->email . time()) . '.png';
                $qrPath = 'qrcodes/' . $qrFileName;
                QrCode::size(200)->generate($participant->email, public_path($qrPath));
            }
        }

        session()->forget('uploaded_participants');

        return redirect()->route('events.edit', $event->id)
                        ->with('success', 'Uploaded participants added successfully with QR Codes!');
    }
}
