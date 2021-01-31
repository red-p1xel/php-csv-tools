<?php

namespace View;

class BaseTemplate
{
    public $rawHtml;
    public $title;
    public $content;

    /**
     * @param string|null $title
     * @param string|null $content
     * @param string|null $rawHtml
     */
    public function __construct(string $title = null, string $content = null, string $rawHtml = null)
    {
        $this->title = $title;
        $this->content = $content;

        if (is_null($rawHtml)) {
            $this->rawHtml = self::renderRawHtml($title, $content);
        } else {
            $this->rawHtml = $rawHtml;
        }
    }

    public static function renderRawHtml(string $title, string $content)
    {
        return <<<HTML
        <!doctype html><html><head><title>$title</title><style>
        body { background-color: #fcfcfc; color: #333333; margin: 0; padding:0; }
        h1 { font-size: 1.5em; font-weight: normal; background-color: #9999cc; min-height:2em; line-height:2em; border-bottom: 1px inset black; margin: 0; }
        h1, p { padding-left: 10px; }
        .error {background-color: #a11a24;}
        code.url { background-color: #eeeeee; font-family:monospace; padding:0 2px;}
        </style>
        </head><body>$content</body></html>
HTML;
    }
}
