<?php

use Illuminate\Support\HtmlString;

class HAsset
{
    const STYLES_CONFIG = 'assets/styles.json';
    const SCRIPTS_CONFIG = 'assets/scripts.json';

    const MINIFIED_CSS_PATH = 'css/minified/';
    const MINIFIED_JS_PATH = 'js/minified/';

    public static function scripts($name)
    {
        if (App::isLocal()) {
            $scripts = self::config(self::SCRIPTS_CONFIG)[$name];
            $includes = '';

            foreach ($scripts as $script) {
                $includes .= Html::script($script.'?'.time());
            }

            return new HtmlString($includes);
        } else {
            $path = self::MINIFIED_JS_PATH."$name.min.js";

            return Html::script(elixir($path));
        }
    }

    public static function styles($name)
    {
        if (App::isLocal()) {
            $styles = self::config(self::STYLES_CONFIG)[$name];
            $includes = '';

            foreach ($styles as $style) {
                $includes .= Html::style($style.'?'.time());
            }

            return new HtmlString($includes);
        } else {
            $path = self::MINIFIED_CSS_PATH."$name.min.css";

            return Html::style(elixir($path));
        }
    }

    private static function config($file)
    {
        return HJson::decode(file_get_contents(config_path($file)));
    }
}
