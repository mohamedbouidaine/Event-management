<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Participant;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ParticipantController extends Controller
{
    // إضافة مشارك يدويًا مع QR Code
    public function store(Request $request, Event $event){
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

    public function update(Request $request, Event $event, Participant $participant)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $participant->update($validated);

        return response()->json($participant);
    }




    // حذف جميع المشاركين للحدث الحالي
    public function destroyAll(Event $event){
        $event->participants()->delete();
        return redirect()->route('events.edit', $event->id)
                            ->with('success', 'All participants removed successfully!');
    }


    // حذف مشارك
    public function destroy(Event $event, $participantId){
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
  // إضافة المشاركين من Session إلى قاعدة البيانات مع QR Code
    public function addUploadedParticipants(Event $event)
    {
        $participants = session('uploaded_participants', []);

        // التحقق من وجود بيانات
        if (empty($participants)) {
            return redirect()->route('events.edit', $event->id)
                ->withErrors('No uploaded participants found in session.');
        }

        foreach ($participants as $data) {
            if (!$event->participants()->where('email', $data['email'])->exists()) {
                $participant = $event->participants()->create($data);

                // توليد QR Code وحفظه فقط
                $qrFileName = md5($participant->email . time()) . '.png';
                $qrPath = 'qrcodes/' . $qrFileName;

                // التأكد من وجود مجلد qrcodes
                if (!file_exists(public_path('qrcodes'))) {
                    mkdir(public_path('qrcodes'), 0755, true);
                }

                QrCode::size(200)->generate($participant->email, public_path($qrPath));
            }
        }

        // تنظيف الجلسة بعد الإضافة
        session()->forget('uploaded_participants');

        return redirect()->route('events.edit', $event->id)
                        ->with('success', 'Uploaded participants added successfully with QR Codes!');
    }


    public function updateParticipant(Request $request, $id)
    {
        $participant = Participant::find($id);

        $participant->name = $request->name;
        $participant->email = $request->email;
        $participant->phone = $request->phone;
        $participant->save();

        return redirect()->back()->with('success', 'Participant updated successfully!');
    }

    public function uploadExcel(Request $request, Event $event)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // ✅ تحقق من وجود بيانات في الملف
        if (count($rows) < 2) {
            return redirect()->back()->withErrors('The file must contain data rows after the first row (column headers).');
        }


        session([
            'uploaded_rows' => $rows,
            'first_row' => $rows[1],
            // أول صف (رؤوس الأعمدة)
        ]);

        return redirect()->back()->with('success', 'File uploaded successfully. Please map the columns to add participants.');
    }

    // حفظ الأعمدة المختارة
    public function mapColumns(Request $request, Event $event)
    {
        $nameIndex  = $request->input('name_value');
        $emailIndex = $request->input('email_value');
        $phoneIndex = $request->input('phone_value');

        session(['column_mapping' => [
            'name' => $nameIndex,
            'email' => $emailIndex,
            'phone' => $phoneIndex,
        ]]);

        return redirect()->back()->with('success', 'Column mapping saved.');
    }

    // إضافة المشاركين بناءً على الأعمدة
    public function addMappedParticipants(Event $event)
    {
        $mapping = session('column_mapping', []);
        $rows = session('uploaded_rows', []);

        if (empty($mapping) || empty($rows)) {
            return redirect()->back()->withErrors('No column mapping or uploaded rows found.');
        }

        // تخطي الصف الأول (رؤوس الأعمدة)
        foreach (array_slice($rows, 1) as $rowIndex => $row) {
            $name  = $row[$mapping['name']] ?? null;
            $email = $row[$mapping['email']] ?? null;
            $phone = $row[$mapping['phone']] ?? null;

            if ($name && $email && !$event->participants()->where('email', $email)->exists()) {
                $participant = $event->participants()->create([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                ]);

                // إنشاء QR Code لكل مشارك
                $qrFileName = md5($participant->email . time()) . '.png';
                QrCode::size(200)->generate($participant->email, public_path('qrcodes/'.$qrFileName));
            }
        }

        // تنظيف الجلسة بعد الإضافة
        session()->forget(['uploaded_rows', 'column_mapping', 'first_row']);

        return redirect()->back()->with('success', 'Participants added successfully.');
    }




}
