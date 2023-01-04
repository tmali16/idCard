<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Label extends Component
{
    private $domain;
    private $name;
    private $class;
    private $required;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($domain, $required = false, $class = "")
    {
        $this->domain = $domain;
        $this->required = $required;
        $this->class = $class;
        $this->name = bind($domain);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.label', [
            'required' => $this->required,
            'name' => $this->name,
            'domain' => $this->domain,
            'class' => $this->class,
        ]);
    }
}
