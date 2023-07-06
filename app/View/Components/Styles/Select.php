<?php

namespace App\View\Components\Styles;

use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $attrs,$values,$default,$keyByValue,$selected;
    public function __construct($values,$onchange="",$class="",$id="",$name='',$selected="",$default="",$keyByValue="0")
    {
        //
        $class = '';
        $attributes = [
            'class' => 'style-select '.$class,
            'id' => $id,
            'onChange' => $onchange,
            'name' => $name,
        ];
        $values = json_decode(htmlspecialchars_decode($values));
        $this->values = $values;
        $this->default = $default;
        $this->selected = $selected;
        $this->keyByValue = (int) $keyByValue;
        $this->attrs = setComponentAttributes($attributes);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.styles.select');
    }
}
