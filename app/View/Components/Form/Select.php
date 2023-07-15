<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $attrs,$selectAttrs,$values,$default,$keyByValue,$selected,$label,$name,$id,$multiple,$disabled;
    public function __construct($values,$name,$id="",$onchange="",$class="",$selected="",$default="",$keyByValue="0",$label='',$multiple = false,$placeholder='',$disabled="")
    {
        //
        $attributes = [
            'class' => 'form-field '.$class,
        ];

        if($placeholder == 'true') {
            $placeholder = trim($label)?trim($label):"";
        }
        $selectClass = '';
        if($multiple == 'true') {
            $selectClass .= ' multiple';
            $selected = htmlspecialchars_decode($selected);
        }
        else {
            $multiple = '';
        }
        $newSelected = json_decode($selected,true);
        if(is_array($newSelected)) {
            $selected = array_filter($newSelected);
        }
        else {
            $selected = [$selected];
        }

        $selectAttrs = [
            'class' => 'select2'.$selectClass,
            'id' => $id,
            'onChange' => $onchange,
            'name' => $name,
            'data-placeholder' => $placeholder,
            'multiple' => $multiple,
            'aria-labelledby' => $name,
        ];

        if(strpos($name,"[")!==false) {
            $name=substr($name,0,strpos($name,"["));
        }

        $values = json_decode(htmlspecialchars_decode($values));
        if($disabled) {
            $disabled = json_decode(htmlspecialchars_decode($disabled));
        }
        else {
            $disabled = [];
        }
        $this->label = $label;
        $this->disabled = $disabled;
        $this->id = $id;
        $this->values = $values;
        $this->name = $name;
        $this->default = $default;
        $this->selected = $selected;
        $this->keyByValue = (int) $keyByValue;
        $this->attrs = setComponentAttributes($attributes);
        $this->selectAttrs = setComponentAttributes($selectAttrs);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form.select');
    }
}
