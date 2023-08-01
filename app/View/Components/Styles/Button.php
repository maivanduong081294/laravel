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
    public function __construct($content,$onclick="",$class="",$id="",$href="",$target="",$type="",$leftIcon="",$rightIcon="",$dataPopper='',$dataToggle='',$download="")
    {
        //
        if(trim($dataToggle)) {
            $class.= " btn-toggle";
        }
        $download = ($download!=='false' && trim($download))?$download:'';
        $attributes = [
            'class' => 'style-btn '.trim($class),
            'id' => $id,
            'href' => $href,
            'onclick' => $onclick,
            'target' => $target,
            'type'=>$type,
            'data-popper'=>$dataPopper,
            'data-toggle'=>$dataToggle,
            'download'=>$download,
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
