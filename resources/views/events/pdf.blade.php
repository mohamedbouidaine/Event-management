<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Participants PDF</title>
    <style>
        .page-break { page-break-after: always; }
        .participant { text-align: center; margin-top: 100px; }
    </style>
</head>
<body>
@foreach($participants as $participant)
    <div class="participant">
        <h1>{{ $participant->name }}</h1>
        <div>
            <img src="data:image/svg+xml;base64,{{ $participant->qrCodeBase64 }}" alt="QR Code">
        </div>
    </div>
    <div class="page-break"></div>
@endforeach
</body>
</html>
