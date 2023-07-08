<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $name,$id,$label,$icon,$attrs,$inputAttrs;
    public function __construct($name,$value='',$type='text',$placeholder='',$label='',$id='',$icon='',$class='',$onchange='',$inputClass="")
    {
        //

        if($placeholder == 'true') {
            $placeholder = trim($label)?trim($label):"";
        }
        $inputAttrs = [
            'class' => $inputClass,
            'id' => $id,
            'onChange' => $onchange,
            'type'=>$type,
            'value' => $value,
            'placeholder' => $placeholder,
            'name' => $name,
        ];

        $attrs = [
            'class' => 'form-input '.$class,
        ];
        $this->name = $name;
        $this->label = $label;
        $this->id = $id;
        $this->icon = $icon;
        $this->attrs = setComponentAttributes($attrs);
        $this->inputAttrs = setComponentAttributes($inputAttrs);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.input');
    }
}
