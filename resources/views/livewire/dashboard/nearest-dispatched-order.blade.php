<style>
    .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        width: 300px;
        /* Adjust the width as needed */
        height: 400px;
        /* Adjust the height as needed */
    }

    .chart-card {
        position: absolute;
        width: 300px;
        height: 400px;
        background-color: white;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s ease, z-index 0.3s ease;
    }

    .chart-card img {
        width: 100%;
        height: 70%;
        object-fit: cover;
    }

    .chart-title {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        padding: 5px 0;
        background-color: #007BFF;
        color: white;
        margin: 0;
    }

    /* Stack effect: Each card shifted down and right */

    .chart-card:nth-child(1) {
        transform: translate(0px, 0px);
        z-index: 7;
    }

    .chart-card:nth-child(2) {
        transform: translate(20px, 20px);
        z-index: 6;
    }

    .chart-card:nth-child(3) {
        transform: translate(40px, 40px);
        z-index: 5;
    }

    .chart-card:nth-child(4) {
        transform: translate(60px, 60px);
        z-index: 4;
    }

    .chart-card:nth-child(5) {
        transform: translate(80px, 80px);
        z-index: 3;
    }

    .chart-card:nth-child(6) {
        transform: translate(60px, 60px);
        z-index: 2;
    }

    .chart-card:nth-child(7) {
        transform: translate(80px, 80px);
        z-index: 1;
    }

    
    /* Center title above cards */
    .chart-header {
        text-align: center;
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }
</style>

<div style="width: 30%; margin: auto;">
    <h6 class="chart-header">Interactive Stacked Cards</h6>
    <div class="chart-container">
        <!-- Cards -->
        @foreach($cards as $index=>$card)
        <div class="chart-card" data-order="{{$index+1}}">
            <p class="chart-title">{{ $card['date'] }} <span style="float:right;">[{{$index+1}}]</span></p>
            <table class="table table-striped" style="font-size: xx-small;">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Bag Size</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($card["data"] as $val)
                    <tr>
                        <td>{{$val->id}}</td>
                        <td>{{$val->bag_w}} x {{$val->bag_l}} x {{$val->bag_g ? $val->bag_g : 0.00}}</td>
                        <td>{{$val->total_units}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
        
    </div>

</div>

<script>
    // JavaScript to handle card interactions
    const cards = document.querySelectorAll('.chart-card');
    const container = document.querySelector('.chart-container'); // The chart container

    // Store the original transform and z-index for all cards
    const originalStates = Array.from(cards).map(card => ({
      zIndex: card.style.zIndex,
      transform: card.style.transform
    }));

    cards.forEach(card => {
      // On click, bring the card to the front
      card.addEventListener('click', () => {
        cards.forEach(c => c.style.zIndex = 1); // Reset all cards' z-index
        card.style.zIndex = 10; // Bring clicked card to front
        card.style.transform = 'translate(-10px, -10px) scale(1.1)'; // Highlight card
      });

      // On mouse out from a card, reset the card to its original position
      card.addEventListener('mouseleave', () => {
        card.style.zIndex = originalStates[index].zIndex;
        card.style.transform = originalStates[index].transform;
      });
    });

    // On mouse out from the container, reset all cards to their original positions
    container.addEventListener('mouseleave', () => {
      cards.forEach((card, index) => {
        card.style.zIndex = originalStates[index].zIndex;
        card.style.transform = originalStates[index].transform;
      });
    });
  </script>