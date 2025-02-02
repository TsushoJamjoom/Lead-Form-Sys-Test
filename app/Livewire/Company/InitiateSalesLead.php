<?php

namespace App\Livewire\Company;

use Livewire\Component;

class InitiateSalesLead extends Component
{
    public $index;
    public $key;

    public function mount($index, $key)
    {
        $this->index = $index;
        $this->key = $key;
    }

    public function render()
    {
        return view('livewire.company.initiate-sales-lead');
    }
}
