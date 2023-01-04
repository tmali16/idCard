<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Submit extends Component
{   private $domain;
    private $name;
    private $class;
    private $type;
    private $div_class;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($domain=null, $type="submit", $class="", $div_class="")
    {
        $this->domain = $domain;
        $this->type = $type;
        $this->class = $class;
        $this->div_class = $div_class;
        $this->name = bind($domain);
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.submit',[
                'domain'=>$this->domain,
                'name'=>$this->name,
                'class'=>$this->class,
                'type'=>$this->type,
                'div_class'=>$this->div_class,
        ]);
    }

}
