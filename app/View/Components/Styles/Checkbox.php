<?php

namespace App\View\Components\Styles;

use Illuminate\View\Component;

class Checkbox extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */ 
    
    public $value,$name,$id,$label,$checked,$class;
    public function __construct($value='',$class='',$name='',$id='',$label='',$checked=false)
    {
        $this->value = $value;
        $this->name = $name;
        $this->id = $id;
        $this->label = $label;
        $this->checked = $checked?'true':'false';
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.styles.checkbox');
    }
}
