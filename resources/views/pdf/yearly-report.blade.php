<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tahunan {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-item {
            text-align: center;
            padding: 20px;
            background-color: #f8fafc;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .summary-item h3 {
            margin: 0 0 15px 0;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .summary-item:nth-child(1) {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-color: #3b82f6;
        }
        .summary-item:nth-child(2) {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border-color: #10b981;
        }
        .summary-item:nth-child(3) {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: #f59e0b;
        }
        .summary-item:nth-child(4) {
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
            border-color: #ec4899;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .table th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #374151;
        }
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-paid {
            color: #059669;
            font-weight: bold;
        }
        .status-pending {
            color: #d97706;
            font-weight: bold;
        }
        .status-overdue {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Tahunan {{ $year }}</h1>
        <p>Dashboard IT Pro</p>
        <p>Digenerate pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <h3>Total Invoice</h3>
            <div class="value">{{ $totalInvoices }}</div>
        </div>
        <div class="summary-item">
            <h3>Total Pendapatan</h3>
            <div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <h3>Invoice Terbayar</h3>
            <div class="value">{{ $paidInvoices }}</div>
        </div>
        <div class="summary-item">
            <h3>Rata-rata per Bulan</h3>
            <div class="value">Rp {{ number_format($averageMonthly, 0, ',', '.') }}</div>
        </div>
    </div>

    <h2>Detail Invoice Tahun {{ $year }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Klien</th>
                <th>Jenis Layanan</th>
                <th>Deskripsi</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                <td>{{ $invoice->client_name }}</td>
                <td>{{ $invoice->service_type_label }}</td>
                <td>{{ Str::limit($invoice->description, 30) }}</td>
                <td>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                <td class="status-{{ $invoice->status }}">{{ $invoice->status_label }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem Dashboard IT Pro</p>
    </div>
</body>
</html>