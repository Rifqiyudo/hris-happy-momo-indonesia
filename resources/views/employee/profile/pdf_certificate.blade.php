<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Keterangan Kerja - {{ $user->name }}</title>
    <style>
        /* Pengaturan Kertas A4 & Margin dioptimalkan agar DIJAMIN muat 1 halaman */
        @page {
            size: A4;
            margin: 1cm 1.5cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            line-height: 1.35;
            font-size: 10.5pt;
            margin: 0;
            padding: 0;
        }

        /* 1. KOP SURAT - RATA TENGAH SEMPURNA */
        .header {
            width: 100%;
            text-align: center; /* Memaksa semua isi kop surat menjadi rata tengah */
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo-img {
            width: 75px;
            height: auto;
            margin: 0 auto 8px auto; /* Margin auto membuat logo berada persis di tengah */
            display: block;
        }
        .company-title {
            font-size: 16pt;
            font-weight: bold;
            color: #4f46e5;
            margin: 0 0 4px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .company-address {
            font-size: 8.5pt;
            color: #444;
            margin: 0;
            font-style: italic;
            line-height: 1.3;
        }

        /* 2. JUDUL SURAT */
        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title {
            text-decoration: underline;
            font-size: 13pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 9.5pt;
            color: #333;
            margin: 0;
        }

        /* 3. ISI SURAT */
        .content-section {
            margin: 0;
            text-align: justify;
        }
        .paragraph {
            margin-bottom: 12px;
            text-align: justify;
        }

        /* Tabel Biodata */
        .biodata-table {
            width: 85%;
            margin: 12px auto;
            border-collapse: collapse;
        }
        .biodata-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10.5pt;
        }
        .col-label {
            width: 160px;
        }
        .col-separator {
            width: 20px;
            text-align: center;
        }
        .col-value {
            font-weight: bold;
            color: #000;
        }

        /* 4. TANDA TANGAN & STEMPEL */
        .footer-section {
            margin-top: 25px;
            width: 100%;
            position: relative;
        }
        .signature-box {
            float: right;
            width: 230px;
            text-align: center;
        }
        .signature-date {
            margin: 0 0 3px 0;
        }
        .signature-role {
            margin: 0 0 3px 0;
            font-weight: bold;
        }
        .signature-space {
            height: 65px;
            position: relative;
            margin: 5px 0;
        }
        
        /* Stempel Elektronik / Watermark */
        .stempel-digital {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-10deg);
            color: rgba(79, 70, 229, 0.15); /* Transparansi warna */
            font-size: 30px;
            font-weight: bold;
            border: 3px solid rgba(79, 70, 229, 0.15);
            padding: 4px 12px;
            border-radius: 8px;
            text-transform: uppercase;
            z-index: -1;
        }
        .signed-note {
            color: #888;
            font-size: 8pt;
            font-style: italic;
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
        }
        .signature-company {
            margin: 0;
            font-weight: bold;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    {{-- 1. KOP SURAT --}}
    <div class="header">
        {{-- Gunakan public_path() untuk render ke PDF via DomPDF/Snappy --}}
        <img src="{{ public_path('images/PT-Happy-Momo-Indonesia-removebg-preview.png') }}" alt="Logo Happy Momo" class="logo-img">
        
        <h1 class="company-title">PT. HAPPY MOMO INDONESIA</h1>
        <p class="company-address">
            Jl. Pergudangan Margomulyo Permai, Surabaya<br>
            Email: corporate@happymomo.id | Website: www.happymomo.id
        </p>
    </div>

    {{-- 2. JUDUL SURAT --}}
    <div class="title-section">
        <p class="doc-title">SURAT KETERANGAN KERJA</p>
        <p class="doc-number">Nomor: {{ $ref_number ?? 'SKK/STF002/09/2026' }}</p>
    </div>

    {{-- 3. ISI SURAT --}}
    <div class="content-section">
        <p class="paragraph">
            Yang bertanda tangan di bawah ini, selaku <strong>HR Manager</strong> PT. Happy Momo Indonesia, menerangkan dengan sesungguhnya bahwa:
        </p>

        <table class="biodata-table">
            <tr>
                <td class="col-label">Nama Lengkap</td>
                <td class="col-separator">:</td>
                <td class="col-value">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="col-label">Nomor Induk Karyawan</td>
                <td class="col-separator">:</td>
                <td class="col-value">{{ $user->nik }}</td>
            </tr>
            <tr>
                <td class="col-label">Jabatan Terakhir</td>
                <td class="col-separator">:</td>
                <td class="col-value">{{ $user->position->name ?? 'Staff' }}</td>
            </tr>
            <tr>
                <td class="col-label">Departemen</td>
                <td class="col-separator">:</td>
                <td class="col-value">{{ $user->department->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Tanggal Bergabung</td>
                <td class="col-separator">:</td>
                <td class="col-value">{{ \Carbon\Carbon::parse($user->join_date)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <p class="paragraph">
            Sdr/i. <strong>{{ $user->name }}</strong> merupakan salah satu talenta terbaik yang telah berkontribusi aktif di <strong>PT. Happy Momo Indonesia</strong> dengan penempatan kerja pada unit <strong>{{ $user->branch->name ?? 'Kantor Pusat' }}</strong>. Selama masa baktinya, yang bersangkutan senantiasa menunjukkan dedikasi, integritas, serta tingkat profesionalisme yang sangat tinggi dalam menjalankan setiap tugas dan tanggung jawabnya.
        </p>

        <p class="paragraph">
            Beliau memiliki etos kerja yang teladan, kemampuan adaptasi yang sangat baik, serta mampu membangun kerja sama tim yang solid guna mencapai target perusahaan. Kami sangat mengapresiasi seluruh kontribusi positif yang telah diberikan selama berkarir bersama kami.
        </p>

        <p class="paragraph">
            Surat keterangan ini sekaligus berfungsi sebagai surat rekomendasi resmi. Kami sangat meyakini bahwa Sdr/i. <strong>{{ $user->name }}</strong> akan menjadi aset berharga serta memberikan dampak positif yang signifikan bagi perusahaan manapun tempat beliau mendedikasikan keahliannya di masa mendatang.
        </p>

        <p class="paragraph">
            Demikian surat keterangan dan rekomendasi ini dibuat dengan sebenarnya, agar dapat dipergunakan sebagai dokumen pendukung administrasi dan referensi profesional bagi pihak-pihak yang berkepentingan.
        </p>
    </div>

    {{-- 4. TANDA TANGAN & STEMPEL --}}
    <div class="footer-section clearfix">
        <div class="signature-box">
            <p class="signature-date">Surabaya, {{ $date ?? date('d F Y') }}</p>
            <p class="signature-role">HR Manager</p>
            
            <div class="signature-space">
                {{-- Watermark Stempel Digital Tembus Pandang --}}
                <div class="stempel-digital">HAPPY</div>
                <p class="signed-note">(Signed Electronically)</p>
            </div>

            <p class="signature-company">PT. Happy Momo Indonesia</p>
        </div>
    </div>

</body>
</html>