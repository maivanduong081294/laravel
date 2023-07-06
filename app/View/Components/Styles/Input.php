<?php

namespace App\View\Components\Styles;

use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $attrs;
    public function __construct($value,$onchange="",$class="",$id="",$type="",$placeholder="",$name='')
    {
        //
        $class = '';
        $attributes = [
            'class' => 'style-input '.$class,
            'id' => $id,
            'onChange' => $onchange,
            'type'=>$type,
            'value' => $value,
            'placeholder' => $placeholder,
            'name' => $name,
        ];

        $this->attrs = setComponentAttributes($attributes);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.styles.input');
    }
}
