<?php

namespace app;

/**
 * View
 *
 * @author Andriy Ivanchenko
 */
class View {

    /**
     * Render view
     * @param string $view Path to view (with .php)
     * @param array $params Parameters are passed
     * @return string
     */
    public function render(string $view, array $params = []) {
        extract($params, EXTR_REFS | EXTR_SKIP);

        ob_start();

        include(VIEWS_PATH . $view);

        return ob_get_clean();
    }

}
