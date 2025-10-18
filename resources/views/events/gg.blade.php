
            <!-- {{-- زر فتح الـ Modal --}}
            <button type="button"
                    class="bg-blue-600 text-white px-2 py-1 rounded shadow hover:bg-blue-700 transition"
                    data-bs-toggle="modal"
                    data-bs-target="#participantModal{{ $loop->index }}">
                Details
            </button>
        </div>
    </li>

    {{-- Modal لكل مشارك --}}
    <div class="modal fade" id="participantModal{{ $loop->index }}" tabindex="-1" aria-labelledby="participantModalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="participantModalLabel{{ $loop->index }}">Participant Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    @php
                        $manualRow = session('manual_uploaded_rows')[$loop->index] ?? ['names' => [], 'emails' => [], 'phones' => []];
                    @endphp

                    <form method="POST" action="{{ route('participants.update', $participant->id) }}">
                        @csrf

                        {{-- Dropdown Name --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Name:</strong></label>
                            <select class="form-select" name="name">
                                <option selected>{{ $participant->name }}</option>
                                @foreach($manualRow['names'] ?? [] as $name)
                                    @if($name != $participant->name)
                                        <option>{{ $name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Email --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Email:</strong></label>
                            <select class="form-select" name="email">
                                <option selected>{{ $participant->email }}</option>
                                @foreach($manualRow['emails'] ?? [] as $email)
                                    @if($email != $participant->email)
                                        <option>{{ $email }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Phone --}}
                        <div class="mb-3">
                            <label class="form-label"><strong>Phone:</strong></label>
                            <select class="form-select" name="phone">
                                <option selected>{{ $participant->phone ?? 'No phone' }}</option>
                                @foreach($manualRow['phones'] ?? [] as $phone)
                                    @if($phone != $participant->phone)
                                        <option>{{ $phone }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- QR Code --}}
                        <div class="mb-3">
                            <p><strong>QR Code:</strong></p>
                            <div>{!! QrCode::size(120)->generate($participant->email) !!}</div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endforeach -->
<!-- </ul> -->






<!-- @if(session()->has('first_row') && count(session('first_row')) > 0)
    <-- الزر الذي يفتح النافذة -->
    <!-- <button id="showMappingModal"
        style="background:#2563eb;color:white;padding:8px 16px;border:none;border-radius:8px;cursor:pointer;">
        Select Columns
    </button> -->

    <!-- النافذة المنبثقة -->
    <!-- <div id="mappingModal"
        style="display:none;position:fixed;inset:0;align-items:center;justify-content:center;
               background:rgba(0,0,0,0.5);z-index:9999;">

        <div style="background:white;padding:20px;border-radius:12px;width:400px;
                    box-shadow:0 0 10px rgba(0,0,0,0.3);color:black;">
            <h2 style="font-size:18px;font-weight:bold;margin-bottom:16px;text-align:center;color:black;">
                Select Columns from First Row
            </h2>

            @php
                $firstRow = session('first_row', []);

                // إذا كانت المصفوفة داخل مصفوفة (كما في Excel غالبًا)
                if (isset($firstRow[0]) && is_array($firstRow[0])) {
                    $firstRow = $firstRow[0];
                }
            @endphp

            {{-- لعرض البيانات داخل النافذة (اختياري للتأكد فقط) --}}
            <pre style="color:black;background:#f3f3f3;padding:6px;border-radius:6px;max-height:100px;overflow:auto;">
{{ print_r($firstRow, true) }}
            </pre> -->

            <!-- نموذج اختيار الأعمدة -->
            <!-- <form method="POST" action="{{ route('participants.mapColumns', $event->id) }}">
                @csrf

                <label style="display:block;margin-bottom:6px;font-weight:bold;color:black;">Name:</label>
                <select name="name_value"
                        style="width:100%;margin-bottom:12px;padding:6px;border:1px solid #ccc;border-radius:6px;">
                    @foreach($firstRow as $cell)
                        <option value="{{ $cell }}">{{ $cell }}</option>
                    @endforeach
                </select>

                <label style="display:block;margin-bottom:6px;font-weight:bold;color:black;">Email:</label>
                <select name="email_value"
                        style="width:100%;margin-bottom:12px;padding:6px;border:1px solid #ccc;border-radius:6px;">
                    @foreach($firstRow as $cell)
                        <option value="{{ $cell }}">{{ $cell }}</option>
                    @endforeach
                </select>

                <label style="display:block;margin-bottom:6px;font-weight:bold;color:black;">Phone:</label>
                <select name="phone_value"
                        style="width:100%;margin-bottom:18px;padding:6px;border:1px solid #ccc;border-radius:6px;">
                    @foreach($firstRow as $cell)
                        <option value="{{ $cell }}">{{ $cell }}</option>
                    @endforeach
                </select>

                <div style="display:flex;justify-content:space-between;margin-top:10px;">
                    <button type="submit"
                        style="background:#16a34a;color:white;padding:8px 16px;border:none;
                               border-radius:8px;cursor:pointer;">
                        Save
                    </button>
                    <button type="button" id="closeMappingModal"
                        style="background:gray;color:white;padding:8px 16px;border:none;
                               border-radius:8px;cursor:pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div> -->

    <!-- سكريبت فتح وإغلاق النافذة
    <script>
        document.getElementById('showMappingModal').addEventListener('click', function() {
            document.getElementById('mappingModal').style.display = 'flex';
        });
        document.getElementById('closeMappingModal').addEventListener('click', function() {
            document.getElementById('mappingModal').style.display = 'none';
        });
    </script>
@endif -->



    <!-- إضافة المشاركين من الملف المرفوع
    @if(session()->has('uploaded_participants') && count(session('uploaded_participants')) > 0)
        <form action="{{ route('participants.addUploaded', $event->id) }}" method="POST" class="inline-block">
            @csrf
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
                Add Participants from Uploaded File
            </button>
        </form>
    @endif -->




    
