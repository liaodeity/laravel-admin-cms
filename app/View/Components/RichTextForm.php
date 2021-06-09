<?php

namespace App\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class RichTextForm extends Component
{
    private $name;
    /**
     * @var string
     */
    private $value;
    /**
     * @var bool
     */
    private $js;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct ($name, $value = '', $js = false)
    {
        $this->name  = $name;
        $this->value = $value;
        $this->js    = $js;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render ()
    {
        $rich_editor = config ('gui.rich_editor');
        $name        = $this->name;
        $value       = $this->value;
        $js          = $this->js;
        $_id         = str_replace (['[', ']'], ['_', ''], $name);
        $id          = strtolower ('text_' . $_id);
        $id          = Str::snake ($id);
        $url         = '';
        if ($js) {
            $source_id   = $value->id ?? '';
            $source_type =  is_object($value) ? get_class ($value) : '';
            $url         = url ('/admin/upload') . '?' . '_token=' . csrf_token () . '&id=' . $source_id . '&type=' . urlencode ($source_type);
        }

        return view ('components.rich-text-form', compact ('id', 'name', 'value', 'js', 'url', 'rich_editor'));
    }
}
