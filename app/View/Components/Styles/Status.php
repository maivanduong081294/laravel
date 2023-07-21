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

    public $attrs,$value,$message,$type,$name,$class;
    public function __construct(string $value='',string $name='status',string $message='',string $type='success',string $class='',$update="")
    {
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
            elseif($name == 'hidden') {
                switch ($value) {
                    case 1: 
                        $message = 'Ẩn';
                        $type = 'danger';
                        break;
                    default:
                        $message = 'Hiển thị';
                        $type = 'success';
                }
            }
            elseif($name == 'limit') {
                switch ($value) {
                    case 1: 
                        $message = 'Đã tới giới hạn';
                        $type = 'danger';
                        break;
                    default:
                        $message = 'Chưa tới giới hạn';
                        $type = 'success';
                }
            }
        }

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

        $class = $class.' btn btn-'.$type;
        if($update != "" || $update != null) {
            $class.= " update-data";
        }

        $attrs = [
            'class' => $class,
            'value' => $value,
            'data-update' => $update,
            'data-name' => $name
        ];

        $this->attrs = setComponentAttributes($attrs);
        $this->message = $message;
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
