<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Yesno extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $id,$label,$attrs,$inputAttrs,$hiddenAttrs,$name,$yes,$no;
    public function __construct($value='',$name,$id,$label,$readonly='',$class='',$yes="Có",$no="Không")
    {
        $value = $value==='1'?1:0;
        $checked = $value?'true':'';
        $disabled = $readonly=="true"?"true":false;
        $inputAttrs = [
            'id' => $id.'-option-yes',
            'value' => 1,
            'name' => $name,
            'disabled' => $disabled,
            'aria-labelledby' => $name,
            'type' => 'checkbox',
            'checked' => $checked,
        ];

        $hiddenAttrs = array_merge($inputAttrs,[
            'value' => 0,
            'id' => $id.'-option-no',
            'checked' => $checked?'':true,
        ]);

        $attrs = [
            'class'=> 'form-field form-input-yes-no '.$class,
        ];
        
        $this->id = $inputAttrs['id'];
        $this->name = $name;
        $this->label = $label;
        $this->yes = $yes;
        $this->no = $no;
        $this->attrs = setComponentAttributes($attrs);
        $this->inputAttrs = setComponentAttributes($inputAttrs);
        $this->hiddenAttrs = setComponentAttributes($hiddenAttrs);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.yesno');
    }
}
