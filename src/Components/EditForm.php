<?php

namespace ItHealer\Telegram\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ItHealer\Telegram\EditForm\BaseForm;

class EditForm extends Component
{
    public function __construct(
        public readonly BaseForm $form,
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('telegram::components.edit-form', [
            'form' => $this->form
        ]);
    }
}
