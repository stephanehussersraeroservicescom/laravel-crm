<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote #{{ $quote->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }
        .company-info {
            text-align: right;
            margin-bottom: 30px;
        }
        .quote-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .customer-info, .quote-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .quote-lines {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .quote-lines th, .quote-lines td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .quote-lines th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 0.9em;
            color: #666;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUOTATION</h1>
        <div class="company-info">
            <h2>Tapis Corporation</h2>
            <p>
                [Company Address]<br>
                [City, State ZIP]<br>
                [Phone] | [Email]<br>
                [Website]
            </p>
        </div>
    </div>

    <div class="quote-details">
        <div class="customer-info">
            <h3>Bill To:</h3>
            <p>
                <strong>{{ $quote->customer->company_name }}</strong><br>
                {{ $quote->customer->contact_name }}<br>
                @if($quote->customer->email)
                    {{ $quote->customer->email }}<br>
                @endif
                @if($quote->customer->phone)
                    {{ $quote->customer->phone }}<br>
                @endif
                @if($quote->customer->address)
                    {{ $quote->customer->address }}
                @endif
            </p>
        </div>

        <div class="quote-info">
            <h3>Quote Information:</h3>
            <p>
                <strong>Quote #:</strong> {{ $quote->id }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($quote->date_entry)->format('M d, Y') }}<br>
                <strong>Valid Until:</strong> {{ \Carbon\Carbon::parse($quote->date_valid)->format('M d, Y') }}<br>
                <strong>Prepared by:</strong> {{ $quote->user->name ?? 'N/A' }}<br>
                @if($quote->airline)
                    <strong>Project:</strong> {{ $quote->airline->name }}<br>
                @endif
            </p>
        </div>
    </div>

    <table class="quote-lines">
        <thead>
            <tr>
                <th style="width: 20%;">Part Number</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">Unit</th>
                <th style="width: 12%;">Unit Price</th>
                <th style="width: 13%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($quote->quoteLines as $line)
                @php 
                    $unitPrice = $line->final_price / 100;
                    $lineTotal = ($line->final_price * $line->quantity) / 100;
                    $grandTotal += $lineTotal;
                @endphp
                <tr>
                    <td>{{ $line->part_number }}</td>
                    <td>{{ $line->description }}</td>
                    <td class="text-right">{{ number_format($line->quantity) }}</td>
                    <td>{{ $line->unit }}</td>
                    <td class="text-right">${{ number_format($unitPrice, 2) }}</td>
                    <td class="text-right">${{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>${{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-bottom: 30px;">
        <h3>Terms & Conditions:</h3>
        <p><strong>Shipping Terms:</strong> {{ $quote->shipping_terms }}</p>
        <p><strong>Payment Terms:</strong> {{ $quote->payment_terms }}</p>
        @if($quote->lead_time_weeks)
            <p><strong>Lead Time:</strong> {{ $quote->lead_time_weeks }}</p>
        @endif
    </div>

    @if($quote->comments)
        <div style="margin-bottom: 30px;">
            <h3>Comments:</h3>
            <p>{{ $quote->comments }}</p>
        </div>
    @endif

    <div class="footer">
        <p>This quotation is valid until {{ \Carbon\Carbon::parse($quote->date_valid)->format('M d, Y') }}. 
           Prices and availability subject to change without notice.</p>
        <p style="margin-top: 20px;">
            <em>Thank you for considering Tapis Corporation for your aerospace textile needs.</em>
        </p>
    </div>
</body>
</html>