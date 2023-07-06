<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Check extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $value,$name,$id,$label,$checked;
    public function __construct($value='',$name,$id,$label,$checked=false)
    {
        $checked = $checked==='true'?true:false;
        $this->value = $value;
        $this->name = $name;
        $this->id = $id;
        $this->label = $label;
        $this->checked = $checked;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.check');
    }
}
