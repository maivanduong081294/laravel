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
    
    public $value,$name,$id,$label,$checked,$class,$labelAttrs,$checkboxAttrs,$type;
    public function __construct($value='',$class='',$name='',$id='',$label='',$checked=false,$type="checkbox",$inputClass='')
    {
        $checked = $checked==='true'?$checked:'';
        $labelAttrs = [
            'class' => 'style-checkbox '.$class,
            'id' => $id,
        ];

        $checkboxAttrs = [
            'name' => $name,
            'id' => $id,
            'class' => $inputClass,
            'checked' => $checked,
        ];

        if($type!="radio") {
            $type="checkbox";
        }

        $this->labelAttrs = setComponentAttributes($labelAttrs);
        $this->checkboxAttrs = setComponentAttributes($checkboxAttrs);
        $this->label = $label;
        $this->checked = $checked?'true':'false';
        $this->type = $type;
        $this->value = $value;
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
