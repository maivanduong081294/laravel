<?php

namespace App\View\Components\Styles;

use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $content,$attrs,$comp,$leftIcon,$rightIcon;
    public function __construct($content,$onclick="",$class="",$id="",$href="",$target="",$type="",$leftIcon="",$rightIcon="")
    {
        //
        $attributes = [
            'class' => 'style-btn '.$class,
            'id' => $id,
            'href' => $href,
            'onclick' => $onclick,
            'target' => $target,
            'type'=>$type
        ];
        
        if(trim($href)) {
            $comp = "a";
        }
        else {
            $comp = "button";
        }

        $this->attrs = setComponentAttributes($attributes);
        $this->content = $content;
        $this->comp = $comp;
        $this->leftIcon = $leftIcon;
        $this->rightIcon = $rightIcon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.styles.button');
    }
}
