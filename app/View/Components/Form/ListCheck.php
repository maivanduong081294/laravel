<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class ListCheck extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $id,$label,$attrs,$inputAttrs,$name,$selected,$values,$keyByValue,$all;
    public function __construct($values,$name,$id,$label,$type='checkbox',$selected="",$readonly='',$class='',$all='',$keyByValue="0")
    {
        $type = $type==='radio'?'radio':'checkbox';
        $readonly = $readonly == 'true'?"true":'';
        $all = $all?true:false;

        $inputAttrs = [
            'type' => $type,
            'name' => $name,
            'disabled' => $readonly,
        ];

        $attrs = [
            'class'=> 'form-field form-list-check '.$class,
        ];

        $newSelected = json_decode(htmlspecialchars_decode($selected),true);
        if(is_array($newSelected)) {
            $selected = array_filter($newSelected);
        }
        else {
            $selected = [$selected];
        }

        if(strpos($name,"[")!==false) {
            $name=substr($name,0,strpos($name,"["));
        }

        $values = json_decode(htmlspecialchars_decode($values));
        
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->selected = $selected;
        $this->keyByValue = (int) $keyByValue;
        $this->all = (int) $all;
        $this->values = $values;
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
        return view('components.form.list-check');
    }
}
