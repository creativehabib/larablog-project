<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Http\Request;

class Settings extends Component
{
    public $tab = 'general_settings';

    protected $queryString = [
        'tab' => ['keep' => true]
    ];

    public function mount(Request $request)
    {
        $this->tab = $request->query('tab', $this->tab);
    }

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
