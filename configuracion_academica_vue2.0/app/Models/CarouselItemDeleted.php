<?php
// app/Events/CarouselItemUpdated.php
namespace App\Events;

use App\Models\CarouselItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarouselItemUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $carouselItem;

    public function __construct(CarouselItem $carouselItem)
    {
        $this->carouselItem = $carouselItem;
    }

    public function broadcastOn()
    {
        return new Channel('carousel');
    }

    public function broadcastAs()
    {
        return 'carousel.updated';
    }
}

// app/Events/CarouselItemDeleted.php
namespace App\Events;

use App\Models\CarouselItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarouselItemDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $carouselItemId;

    public function __construct($carouselItemId)
    {
        $this->carouselItemId = $carouselItemId;
    }

    public function broadcastOn()
    {
        return new Channel('carousel');
    }

    public function broadcastAs()
    {
        return 'carousel.deleted';
    }
}
