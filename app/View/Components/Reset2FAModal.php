<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Reset2FAModal extends Component
{
    public $userId;

    /**
     * Create a new component instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.reset2-f-a-modal');
    }
}
