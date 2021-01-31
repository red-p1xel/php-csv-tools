<?php

namespace Http;

use View\BaseTemplate;

class Response
{
    /**
     * @param array $data
     * @param BaseTemplate|null $template
     * @return string
     */
    public static function view(array $data = [], BaseTemplate $template = null)
    {
        if (!$template) {
            if (isset($data['title']) && isset($data['content'])) {
                $template = BaseTemplate::renderRawHtml($data['title'], $data['content']);
            } elseif (isset($data['error'])) {
                $template = BaseTemplate::renderRawHtml(
                    'Error',
                    "<h1 class='error' style='background-color: #a11a24;'>{$data['error']}</h1>"
                );
            }
        }

        return $template;
    }
}
