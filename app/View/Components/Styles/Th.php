<?php

namespace App\View\Components\Styles;

use Illuminate\View\Component;

class Th extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $orderBy,$orderType,$text,$attrs;
    public function __construct($orderBy="",$orderType="",$text,$class="")
    {
        //
        $orderBy = isset($orderBy) && trim($orderBy)?trim($orderBy):'';
        if($orderBy) {
            $class .= ' data-sorting';
            $orderType = isset($orderType) && trim($orderType) ?trim($orderType):'desc';
            $checkOrderBy = checkSortingTable($orderBy);
            if($checkOrderBy) {
                $class .= ' '.$checkOrderBy;
                $orderType = $checkOrderBy == 'desc'?"asc":"desc";
            }
        }
        else {
            $orderType = '';
        }
        $attrs = array(
            'class' => $class,
            'data-order-by' => $orderBy,
            'data-order-type' => $orderType,
        );
        $this->text = $text;
        $this->attrs = setComponentAttributes($attrs);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.styles.th');
    }
}
