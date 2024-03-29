<?php namespace Jakten\Helpers\BladeSvgIcon;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * Class Icon
 * @package Jakten\Helpers\BladeSvgIcon
 */
class Icon implements Htmlable
{
    private $icon;
    private $renderMode;
    private $factory;
    private $attrs = [];
    private $css;

    public function __construct($icon, $renderMode, $factory, $attrs = [], $css = '')
    {
        $this->icon = $icon;
        $this->renderMode = $renderMode;
        $this->factory = $factory;
        $this->attrs = $attrs;
        $this->css = $css;
    }

    public function toHtml()
    {
        return new HtmlString(call_user_func([
            'inline' => [$this, 'renderInline'],
            'sprite' => [$this, 'renderFromSprite'],
        ][$this->renderMode]));
    }

    public function __call($method, $args)
    {
        if (count($args) === 0) {
            $this->attrs[] = snake_case($method, '-');
        } else {
            $this->attrs[snake_case($method, '-')] = $args[0];
        }

        return $this;
    }

    public function inline()
    {
        $this->renderMode = 'inline';

        return $this;
    }

    public function sprite()
    {
        $this->renderMode = 'sprite';

        return $this;
    }

    public function renderInline()
    {
        $icon = str_replace(
            '<svg',
            sprintf('<svg%s', $this->renderAttributes()),
            $this->factory->getSvg($this->icon)
        );

        if ($this->css === '') {
            return $icon;
        }

        return str_replace(
            'class="ico',
            sprintf('class="%s ico', $this->css),
            $this->factory->getSvg($this->icon)
        );
    }

    public function renderFromSprite()
    {
        return vsprintf('<svg%s><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="%s#%s"></use></svg>', [
            $this->renderAttributes(),
            $this->factory->spritesheetUrl(),
            $this->factory->spriteId($this->icon),
        ]);
    }

    private function renderAttributes()
    {
        if (count($this->attrs) == 0) {
            return '';
        }

        return ' ' . collect($this->attrs)->map(function ($value, $attr) {
            if (is_int($attr)) {
                return $value;
            }

            return sprintf('%s="%s"', $attr, $value);
        })->implode(' ');
    }
}
