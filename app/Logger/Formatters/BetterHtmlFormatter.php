<?php

namespace App\Logger\Formatters;

/**
 * @deprecated no alternative. In current version not used correctly
 */
class BetterHtmlFormatter extends BetterFormatter
{
    protected $messageTemplate = "<span class='class'>%s</span><span class='where'> in %s line %s</span><br><br><span class='message'>%s</span>";
    protected $frameTemplate = "<span class='frame'><b>%s</b>: %s<span class='frame-class'>%s</span>%s<span class='frame-function'>%s</span>%s</span> <span class='frame-where'>in %s on line %s</span><br>";
    protected $argsTemplate = '(<em>%s</em>)';
    protected $extraTemplate = "<span class='extra'><span class='extra-key'>%s:</span> %s<br><hr>";
    protected $outputTemplate = "<div class='title'>%s</div><div class='details'>%s<br>%s</div>";

    protected $escapeHtml = true;

    /**
     * Formats a log record.
     *
     * @param array $record A record to format
     *
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $css = $this->getStylesheet();
        $content = parent::format($record);

        return $this->decorate($content, $css);
    }

    private function getStylesheet()
    {
        return <<<'EOF'
body { font: 11px Verdana, Arial, sans-serif; color: #333;}
.content { width: 1100px; margin: 0 auto; border: 1px solid #CCCCCC; border-radius: 10px; word-wrap: break-word}
.title {background-color: #DDDDDD; padding: 15px; border-radius: 10px 10px 0 0}
.details {padding: 10px}
th {padding: 5px}
td {padding: 8px !important;}
.class {font-size: 20px;}
.where {font-size: 20px; color: #868688}
.message {font-size: 18px; font-weight:bold; }
.frame {font-size: 15px;}
.frame-class {text-decoration: underline }
.frame-where {color: #868688}
.frame-function {font-weight: bold}
.extra {font-size: 15px}
.extra-key {font-weight: bold}
EOF;
    }

    private function decorate($content, $css)
    {
        return <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="noindex,nofollow" />
        <style>
            $css
        </style>
    </head>
    <body>
        <div class="content">
            $content
        </div>
    </body>
</html>
EOF;
    }
}
