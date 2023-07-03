<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class Breadcrumb extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */


    public $breadcrumb;
    public function __construct($data='')
    {
        $data = json_decode(html_entity_decode($data));
        $this->breadcrumb = $data;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.breadcrumb');
    }
}
