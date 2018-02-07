<?php

namespace app\models;

/**
 * Site page
 *
 * @author Andriy Ivanchenko
 */
class SitePage {

    /**
     * Page title
     *
     * @var string
     */
    private $title = '';

    /**
     * Page description
     *
     * @var string
     */
    private $description = '';

    /**
     * Bread crumbs chain
     *
     * @var array
     */
    private $breadcrumbs = [];

    /**
     * Constructor
     *
     * @param string $title
     * @param string $description
     * @param array $breadcrumbs
     */
    public function __construct(string $title, string $description, array $breadcrumbs = []) {
        $this->title = $title;
        $this->description = $description;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Get bread crumbs
     *
     * @return string
     */
    public function getBreadcrumbs(): array {
        return $this->breadcrumbs;
    }

}
