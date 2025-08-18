<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quote->id }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 30px;
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .company-section {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .logo-section {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
        }
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 10pt;
            line-height: 1.3;
        }
        .logo-text {
            color: #E91E63;
            font-size: 36pt;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .tagline {
            font-size: 9pt;
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }
        .quotation-title {
            font-size: 16pt;
            text-align: center;
            text-decoration: underline;
            margin: 30px 0 20px 0;
        }
        .quote-info {
            margin-bottom: 25px;
            font-size: 10pt;
            line-height: 1.6;
        }
        .quote-info strong {
            display: inline-block;
            min-width: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table th {
            background-color: #e8f4f8;
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
            font-weight: bold;
        }
        table td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 10pt;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .product-family-header {
            background-color: #f5f5f5;
            font-weight: bold;
            font-style: italic;
        }
        .product-descriptions {
            margin-top: 30px;
            font-size: 10pt;
            line-height: 1.5;
        }
        .product-descriptions h3 {
            font-size: 12pt;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #333;
        }
        .product-descriptions p {
            margin: 5px 0;
            text-align: justify;
        }
        .terms-section {
            margin-top: 30px;
            font-size: 10pt;
            line-height: 1.5;
        }
        .terms-section p {
            margin: 5px 0;
        }
        .note-section {
            margin-top: 30px;
            font-size: 10pt;
            line-height: 1.4;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @media print {
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="company-section">
                <div class="company-name">Tapis Corporation</div>
                <div class="company-details">
                    53 Old Route 22<br>
                    Armonk, NY, 10504<br>
                    Phone: +1 9142732737
                </div>
            </div>
            <div class="logo-section">
                <div class="logo-text">TAPIS</div>
                <div class="tagline">Elevating the Passenger Experience</div>
            </div>
        </div>
    </div>

    <div class="quotation-title">Quotation {{ $quote->id }}</div>

    <div class="quote-info">
        <p><strong>Date issued:</strong> {{ \Carbon\Carbon::parse($quote->date_entry)->format('F jS, Y') }}</p>
        <p><strong>Quoted to:</strong> {{ $quote->customer->contact_name }} - {{ $quote->customer->company_name }}</p>
        <p><strong>Quote validity:</strong> {{ $quote->validity_days ?? 30 }} days</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Product</th>
                <th style="width: 30%;">Product Reference</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 20%;" class="text-center">Lead-time</th>
                <th style="width: 15%;" class="text-right">Price per {{ $quote->quoteLines->first()->unit ?? 'LY' }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Group products by root_code (product family)
                $groupedLines = $quote->quoteLines->groupBy('root_code');
            @endphp
            
            @foreach($groupedLines as $rootCode => $lines)
                @php
                    $productRoot = $lines->first()->productRoot;
                    $familyName = $productRoot ? $productRoot->root_name : 'Standard Products';
                @endphp
                
                {{-- Product Family Header Row --}}
                @if(count($groupedLines) > 1)
                <tr class="product-family-header">
                    <td colspan="5">{{ $familyName }}</td>
                </tr>
                @endif
                
                {{-- Product Lines --}}
                @foreach($lines as $line)
                    @php 
                        $unitPrice = $line->final_price / 100;
                    @endphp
                    <tr>
                        <td>{{ $line->description ?: $line->part_number }}</td>
                        <td>{{ $line->part_number }}</td>
                        <td class="text-center">{{ number_format($line->quantity) }} {{ $line->unit }}</td>
                        <td class="text-center">{{ $line->lead_time ?: $quote->lead_time_weeks ?: '12 to 14 weeks' }}</td>
                        <td class="text-right">${{ number_format($unitPrice, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- Product Descriptions Section --}}
    <div class="product-descriptions">
        @foreach($groupedLines as $rootCode => $lines)
            @php
                $productRoot = $lines->first()->productRoot;
                if (!$productRoot || !$productRoot->description) continue;
            @endphp
            
            <h3>{{ $productRoot->root_name }}</h3>
            <p>{!! nl2br(e($productRoot->description)) !!}</p>
            
            {{-- Add technical specifications if available --}}
            @if($productRoot->has_ink_resist || $productRoot->is_bio)
                <p><strong>Features:</strong> 
                    @if($productRoot->is_bio) Bio-based material @endif
                    @if($productRoot->has_ink_resist) @if($productRoot->is_bio), @endif Ink resistant treatment @endif
                </p>
            @endif
            
            {{-- Add certification info if not already shown --}}
            @if($loop->first && !$quote->certifications)
                <p><strong>Passes:</strong> Heat Release and Smoke Density: FAR25.853, Appendix F, Part IV and Part V, as well as 12 and 60 Second Vertical Flammability: FAR25.853, Appendix F, Part I (ii) and (i).</p>
            @endif
        @endforeach
    </div>

    {{-- Terms and Specifications Section --}}
    <div class="terms-section">
        @if($quote->roll_width || $quote->roll_length)
            <p><strong>Material Specifications:</strong></p>
            @if($quote->roll_width)
                <p>Roll Width: {{ $quote->roll_width }}</p>
            @else
                <p>Roll Width: 54"</p>
            @endif
            @if($quote->roll_length)
                <p>Average Roll Length: {{ $quote->roll_length }}</p>
            @else
                <p>Average Roll Length: 33 LY</p>
            @endif
        @endif
        
        <p><strong>Shipping Terms:</strong> {{ $quote->shipping_terms ?? 'ExWorks Dallas Texas' }}</p>
        @if($quote->quantity_variance)
            <p><strong>Quantity varies with:</strong> {{ $quote->quantity_variance }}</p>
        @else
            <p><strong>Quantity varies with:</strong> +/- 10%</p>
        @endif
        <p><strong>Payment terms:</strong> {{ $quote->payment_terms ?? 'Net 30' }}</p>
    </div>

    @if($quote->comments)
        <div class="note-section">
            <p><strong>Note:</strong> {{ $quote->comments }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Tapis Terms and Conditions apply to all orders unless otherwise specified in writing.</p>
    </div>
</body>
</html>