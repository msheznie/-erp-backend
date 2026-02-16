<p>Dear {{ $supplierName }},</p>

<p>We are pleased to inform you that your bid for Tender Code {{ $tenderCode }} titled "{{ $tenderTitle }}" has been successfully awarded. We appreciate the time and effort you invested in preparing your proposal.</p>

<p>Below are the details of the items awarded to your company:</p>

<p><strong>Tender Code:</strong> {{ $tenderCode }}</p>
<p><strong>Tender Title:</strong> {{ $tenderTitle }}</p>
<p><strong>Supplier Name:</strong> {{ $supplierName }}</p>

<p><strong>Items Awarded:</strong></p>
@if(!empty($items))
<ol>
@foreach($items as $index => $item)
<li>{{ $item['description'] ?? '-' }}, Quantity: {{ $item['quantity'] ?? '-' }}, Price: {{ $item['price'] ?? '-' }} {{ $currency ?? '' }}</li>
@endforeach
</ol>
@else
<p>No items.</p>
@endif

<p>Please review the awarded items and prices. If there are any discrepancies or if you require further clarification, please do not hesitate to contact us.</p>

<p>We expect delivery of the awarded items as per the terms and conditions outlined in the tender document. Kindly ensure that all contractual obligations are met within the stipulated timeframe.</p>

<p>Once again, congratulations on winning the tender, and we look forward to a successful partnership.</p>

<p>Best regards,<br />
Procurement Department.<br />
{{ $companyName }}</p>
