<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $options;
    public $class;
    public $name;
    public $domain;
    public $required;
    public $label_class;
    public $label;
    public $selected;
    public $attributes;
    public $onside;
    public $multiple;
    public $help;
    public $uri;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($domain,$options=null, $uri=null, $multiple =false, $class="", $help="",$required=false, $label_class="", $label=true, $selected=null, $attributes=[], $onside=false)
    {
        $this->options = $options;
        $this->domain = $domain;
        $this->class = $class;
        $this->name = bind($domain);
        $this->required = $required;
        $this->label_class = $label_class;
        $this->label = $label;
        $this->selected = $selected;
        $this->attributes = $attributes;
        $this->onside = $onside;
        $this->multiple = $multiple;
        $this->help = $help;
        $this->uri = $uri;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.select', [
            "domain"=>$this->domain,
            "name"=>$this->name,
            "class"=>$this->class,
            "options"=>$this->options,
            "required"=>$this->required,
            "label_class"=>$this->label_class,
            "label"=>$this->label,
            "selected"=>$this->selected,
            "attributes"=>$this->attributes,
            "onside"=>$this->onside,
            "multiple"=>$this->multiple,
            "help"=>$this->help,
            "uri"=>$this->uri,
        ]);
    }
}
