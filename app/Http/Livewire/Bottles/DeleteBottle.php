<?php

namespace App\Http\Livewire\Bottles;
use Livewire\Component;
use App\Models\BottleInCellar;

class DeleteBottle extends Component
{
    public $bottleId;
    public $cellarId;

    public function mount()
    {
        $this->listenForEvents(['triggerDeleteBottle']);
    }

    public function triggerDeleteBottle($bottleId, $cellarId)
    {
        $this->bottleId = $bottleId;
        $this->cellarId = $cellarId;

        $this->deleteBottle(); // Call the deleteBottle function
    }

    public function deleteBottle()
    {
        $bottleInCellar = BottleInCellar::where('bottle_id', $this->bottleId)
            ->where('cellar_id', $this->cellarId)
            ->first();

        if ($bottleInCellar) {
            $bottleInCellar->delete();
        }

        $this->emit('bottleDeleted');  // emit an event to notify that a bottle was deleted
    }

    // public function render()
    // {
    //     return view('livewire.Bottles.delete-bottle');
    // }
    
    protected $listeners = ['bottleDeleted' => 'handleBottleDeleted'];

    public function handleBottleDeleted()
    {
        session()->flash('message', 'Bottle successfully deleted.');
    }
}
