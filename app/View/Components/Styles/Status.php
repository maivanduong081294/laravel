<?php

namespace App\View\Components\Styles;

use Illuminate\View\Component;

class Status extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $value,$message,$type,$name,$class;
    public function __construct(string $value='',string $name='status',string $message='',string $type='success',string $class='')
    {
        //
        $this->value = $value;
        $this->name = $name;
        $this->class = $class;

        if(!$message) {
            if($name == 'status') {
                switch ($value) {
                    case 0: 
                        $message = 'Không hoạt động';
                        $type = 'danger';
                        break;
                    default:
                        $message = 'Đang hoạt động';
                        $type = 'success';
                }
            }
        }
        $this->message = $message;

        $types = [
            'primary',
            'secondary',
            'success',
            'danger',
            'warning',
            'info',
            'light',
            'dark',
        ];

        if(!in_array($type,$types)) {
            $type = 'success';
        }

        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.styles.status');
    }
}
