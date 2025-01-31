<?php

namespace App\Http\Livewire\Bottles;


use Livewire\Component;
use App\Models\Bottle;
use App\Models\Cellar;
use Illuminate\Support\Facades\Auth;

class SingleBottle extends Component
{
    public $bottleId;
    public $bottle;
    public $fromCatalogue;
    public $quantityInCellar;
    public $quantityFromCatalogue;
    public $cellar_id ;
    public $showSelect = true;
    public $cellars;
    public $myCellarId;
    public $quantity;
    public $numberOfBottle;

  

    // Handle the passed parameter
    public function mount($quantity = 1,$myCellarId = null,$bottle_id=null, $quantityFromCatalogue = 1, $fromCatalogue = false, $showSelect = true, $cellars=null)
    {
    
        $this->bottleId = $bottle_id;
        $this->fromCatalogue = $fromCatalogue;
       $this->quantityInCellar;
        $this->quantityFromCatalogue = $quantityFromCatalogue;
        $this->showSelect = $showSelect;
        $this->cellars = $cellars;
        if(empty($cellar_id)){
            $this->cellar_id = $myCellarId;
            if(!isset($myCellarId)){
                $this->cellar_id = $cellars[0]->id;
            }
        }
        $this->quantity = $quantity;
        
       
        
        
    }

    public function render()
    {
        //Exemple, tu peux l'utiliser où tu en as de besoin pour accéder à l'id c'est $cellar['id'] et le nom $cellar['name']
        $cellar = session('cellar_inf');
        $this->bottle = Bottle::find($this->bottleId);

        return view('livewire.Bottles.single-bottle', [

            'bottle' => $this->bottle,
            'fromCatalogue' => $this->fromCatalogue,
            'quantityInCellar' => $this->quantityInCellar,  // Pass the quantityInCellar to the view
            'quantityFromCatalogue' => $this->quantityFromCatalogue,
            'quantity' => $this->quantity
        ]);
    }




    public function addToCellar()
    {
        $user = Auth::user();
        $selectedBottle = $this->bottle;
        dd($this->quantityInCellar);
        if ($user) {
            $userCellar = Cellar::find($this->cellar_id);
           /*  dd($userCellar); */
            if ($userCellar) {
               /*  dd("hi"); */
                $existingBottle = $userCellar->bottles()->where('bottle_id', $selectedBottle->id)->first();
               /*  dd($existingBottle->pivot->quantity); */
                // Comportement si la bouteille se trouve déjà dans le cellier
                if ($existingBottle) {
                   /*  dd("salut"); */
                    // comportement si l'usager modifie les bouteilles dans son cellier
                    if ($this->quantityInCellar >= 0) {
                       /*  DD("Plus grand ou égal à 0 "); */
                        // lance la fonction de suppression si la valeur est 0
                        // dd($this->quantityInCellar);
                        if ($this->quantityInCellar == '0') {
                            dd("Égal à zéro");
                            // Emit an event to trigger the deleteBottle function in DeleteBottle component
                            $this->emit('triggerDeleteBottle', $selectedBottle->id, $userCellar->id);
                        }

                        $existingBottle->pivot->quantity = $this->quantityInCellar;
                          dd( "Quantity : ". $existingBottle );
                        
                        $existingBottle->pivot->save();
                    // comportement si l'usager ajoute une bouteille du catalogue
                    } elseif ($this->quantityFromCatalogue) {
                        dd("FromCatalogue");
                        $existingBottle->pivot->quantity += $this->quantityFromCatalogue;
                        $existingBottle->pivot->save();
                    }
                // Ajouter la bouteille au cellier si elle n'y existe pas
                } else {
                    //dd("Bottle doesn't exist");
                    $userCellar->bottles()->attach($selectedBottle->id, ['quantity' => $this->quantityFromCatalogue]);

                }
            }
        }
    }

    public function deleteFromCellar(){

    }

    public function increment()
    {
        if ($this->quantityInCellar) {
            $this->quantityInCellar++;
        } elseif ($this->quantityFromCatalogue) {
            $this->quantityFromCatalogue++;
        }
    }

    public function decrement()
    {
        if ($this->quantityInCellar > 0) {
            $this->quantityInCellar--;
        } elseif ($this->quantityFromCatalogue > 0) {
            $this->quantityFromCatalogue--;
        }
    }
}
