<?php

namespace app\helpers\sorting;

use League\Url\Url;

/**
 * Description of Sorting
 *
 * @author Andriy Ivanchenko
 */
class Sorting {

    /**
     * Current direction
     *
     * @var string
     */
    private $currDirection = '';

    /**
     * Current order
     *
     * @var string
     */
    private $currOrder = '';

    /**
     * Data
     *
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param Url $url
     * @param string $order
     * @param string $direction
     * @param array $data
     */
    public function __construct(Url $url, string $order, string $direction, array $data) {
        $this->data = $data;
        if (array_key_exists($order, $this->data)) {
            $this->currOrder = $order;
            if ('asc' === $direction) {
                $this->currDirection = 'asc';
                $nextDirection = 'desc';
            } else {
                $this->currDirection = 'desc';
                $nextDirection = 'asc';
            }

            $this->data[$order]['direction'] = $nextDirection;
            $this->data[$order]['class'] = 'sorting_' . $this->currDirection;
        }

        // The code make urls for the sorting
        $query = $url->getQuery();
        foreach ($this->data as $order => &$data) {
            $query->modify([
                'order' => $order,
                'direction' => $data['direction']
            ]);

            $this->data[$order]['url'] = (string) $url;
        }
    }

    /**
     * Has sorting
     *
     * @return bool
     */
    public function hasSorting(): string {
        return '' !== $this->currDirection && '' !== $this->currOrder;
    }

    /**
     * Get current order
     *
     * @return string
     */
    public function getCurrentOrder(): string {
        return $this->currOrder;
    }

    /**
     * Get current direction
     *
     * @return string
     */
    public function getCurrentDirection(): string {
        return $this->currDirection;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

}
