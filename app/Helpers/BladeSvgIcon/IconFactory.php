<?php namespace Jakten\Helpers\BladeSvgIcon;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\{Collection, Facades\Blade, HtmlString};

/**
 * Class IconFactory
 * @package Jakten\Helpers\BladeSvgIcon
 */
class IconFactory
{
    /**
     * @var Filesystem
     */
    private $files;

    /**
     * @var static
     */
    private $svgCache;

    /**
     * @var array|static
     */
    private $config = [
        'inline' => true,
        'class' => 'ico',
        'sprite_prefix' => '',
    ];

    /**
     * IconFactory constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = Collection::make(array_merge($this->config, $config));
        $this->svgCache = Collection::make();
        $this->files = new Filesystem();
    }

    /**
     * registerBladeTag
     */
    public function registerBladeTag()
    {
        Blade::directive('icon', function ($expression) {
            return "<?php echo e(svg_icon($expression)); ?>";
        });
    }

    /**
     * @return mixed
     */
    private function iconPath()
    {
        return $this->config->get('icon_path', function () {
            throw new Exception('No icon_path set!');
        });
    }

    /**
     * @return mixed
     */
    private function spritesheetPath()
    {
        return $this->config->get('spritesheet_path', function () {
            throw new Exception('No spritesheet_path set!');
        });
    }

    /**
     * @return mixed
     */
    public function spritesheetUrl()
    {
        return $this->config->get('spritesheet_url', '');
    }

    /**
     * @return HtmlString
     */
    public function spritesheet()
    {
        return new HtmlString(
            sprintf(
                '<div style="height: 0; width: 0; position: absolute; visibility: hidden;">%s</div>',
                file_get_contents($this->spritesheetPath())
            )
        );
    }

    /**
     * @param $name
     * @param string $class
     * @param array $attrs
     * @return Icon
     */
    public function icon($name, $class = '', $attrs = [])
    {
        $css = '';
        if (in_array($class, ['sm', 'md', 'lg'])) {
            $css = 'ico-' . $class;
        }

        $attrs = array_merge([
            'class' => $this->buildClass($name, $class),
        ], $attrs);

        return new Icon($name, $this->renderMode(), $this, $attrs, $css);
    }

    /**
     * @param $icon
     * @return string
     */
    public function spriteId($icon)
    {
        return "{$this->spritePrefix()}{$icon}";
    }

    /**
     * @return mixed
     */
    private function spritePrefix()
    {
        return $this->config->get('sprite_prefix');
    }

    /**
     * @return string
     */
    private function renderMode()
    {
        return $this->config['inline'] ? 'inline' : 'sprite';
    }

    /**
     * @param $name
     * @param $class
     * @return string
     */
    private function buildClass($name, $class)
    {
        return trim(sprintf('%s ico-%s ico-%s', $this->config['class'], $name, $class));
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getSvg($name)
    {
        return $this->svgCache->get($name, function () use ($name) {
            return $this->svgCache[$name] = $this->files->get(sprintf('%s/%s.svg', rtrim($this->iconPath()), $name));
        });
    }
}
