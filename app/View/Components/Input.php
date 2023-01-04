<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    private $domain;
    private $name;
    private $class;
    private $required;
    private $type;
    private $onside;
    private $div_class;
    private $lableClass;
    private $lable;
    private $value;
    private $attribute;
    private $help;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($domain, $type = "text", $required = false, $class = "", $onside = false, $div_class = "", $lableClass = "", $lable = true, $value = null, $attribute = [], $help="")
    {
        $this->domain = $domain;
        $this->name = bind($domain);
        $this->type = $type;
        $this->required = $required;
        $this->class = $class;
        $this->onside = $onside;
        $this->div_class = $div_class;
        $this->lableClass = $lableClass;
        $this->lable = $lable;
        $this->value = $value;
        $this->attribute = $attribute;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.input', [
            'required' => $this->required,
            'name' => $this->name,
            'domain' => $this->domain,
            'class' => $this->class,
            'type' => $this->type,
            'onside' => $this->onside,
            'lableClass' => $this->lableClass,
            'lable' => $this->lable,
            'div_class' => $this->div_class,
            'value' => $this->value,
            'attributes' => $this->attribute,
        ]);
    }
}
