<td class="flowchart-cell" data-order-id="{{ $order->id }}" style="display: none;">
    <div class="mermaid">
        @php            
            $completedNodes = [];
            $statusClass = 'pending';

            if ($order->isFullDelivered) {
                $completedNodes = ['Start', ($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Printed', 'Cut', 'OutForDelivery', 'Delivered'];
                $statusClass = 'completed';
            } elseif ($order->isPartDelivered) {
                $completedNodes = ['Start', ($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Printed', 'Cut', 'OutForDelivery', 'Delivered'];
                $statusClass = 'completed';
            } elseif ($order->isOutForDelivery) {
                $completedNodes = ['Start', ($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Printed', 'Cut', 'OutForDelivery'];
                $statusClass = 'delivery';
            } elseif ($order->isFullBookFullPrintFullCut) {
                $completedNodes = ['Start', 'FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookFullPrintPartCut) {
                $completedNodes = ['Start', 'FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookPartPrintFullCut) {
                $completedNodes = ['Start', 'FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookPartPrintPartCut) {
                $completedNodes = ['Start', 'FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookFullPrintFullCut) {
                $completedNodes = ['Start', 'PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookFullPrintPartCut) {
                $completedNodes = ['Start', 'PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookPartPrintFullCut) {
                $completedNodes = ['Start', 'PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookPartPrintPartCut) {
                $completedNodes = ['Start', 'PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookFullPrint) {
                $completedNodes = ['Start', 'FullBooking', 'Printed'];
                $statusClass = 'printing';
            } elseif ($order->isPartBookPartPrint) {
                $completedNodes = ['Start', 'PartBooking', 'Printed'];
                $statusClass = 'printing';
            } elseif ($order->isFullBook) {
                $completedNodes = ['Start', 'FullBooking'];
                $statusClass = 'booked';
            } elseif ($order->isPartBook) {
                $completedNodes = ['Start', 'PartBooking'];
                $statusClass = 'booked';
            } elseif ($order->isRollBook) {
                $completedNodes = ['Start'];
                $statusClass = 'rollbooked';
            }
        @endphp
        
        graph LR;
            @php
                foreach ($completedNodes as $index => $node) {
                    if (isset($completedNodes[$index + 1])) {
                        echo "$node --> {$completedNodes[$index + 1]};\n";
                    }
                }
            @endphp

        %% Define color classes
        classDef completed fill:#28a745,stroke:#0f5132,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef printing fill:#ffc107,stroke:#856404,stroke-width:3px,color:black,font-weight:bold,font-size:16px;
        classDef booked fill:#007bff,stroke:#0056b3,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef rollbooked fill:#6c757d,stroke:#343a40,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef pending fill:#dc3545,stroke:#721c24,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef delivery fill:#17a2b8,stroke:#0c5460,stroke-width:3px,color:white,font-weight:bold,font-size:16px;

        %% Apply colors dynamically based on order status
        @php
            foreach ($completedNodes as $node) {
                echo "class $node $statusClass;\n";
            }
        @endphp
    </div>
</td>
