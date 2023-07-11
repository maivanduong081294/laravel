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

    public $id,$label,$attrs,$inputAttrs,$name;
    public function __construct($value='',$name,$id,$label,$checked=false,$type='checkbox',$readonly='',$class='')
    {
        $type = $type==='radio'?'radio':'checkbox';
        $checked = $checked==='true'?'true':'';
        $readonly = $readonly != 'true'?"true":'';

        $inputAttrs = [
            'type' => $type,
            'id' => $id,
            'value' => $value,
            'name' => $name,
            'readonly' => $readonly,
        ];

        $attrs = [
            'class'=> 'form-field form-input-check '.$class,
        ];
        
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
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
        return view('components.form.check');
    }
}
