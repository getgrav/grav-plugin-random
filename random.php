<?php
namespace Grav\Plugin;

use Grav\Common\Page\Collection;
use Grav\Common\Plugin;
use Grav\Common\Uri;
use Grav\Common\Taxonomy;

class RandomPlugin extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents() {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Activate plugin if path matches to the configured one.
     */
    public function onPluginsInitialized()
    {
        /** @var Uri $uri */
        $uri = $this->grav['uri'];
        $route = $this->config->get('plugins.random.route');

        if ($route && $route == $uri->path()) {
            $this->enable([
                'onPageInitialized' => ['onPageInitialized', 0]
            ]);
        }
    }

    /**
     * Display random page.
     */
    public function onPageInitialized()
    {
        /** @var Taxonomy $taxonomy_map */
        $taxonomy_map = $this->grav['taxonomy'];

        $filters = (array) $this->config->get('plugins.random.filters');

        if (count($filters)) {
            $collection = new Collection();
            foreach ($filters as $taxonomy => $items) {
                if (isset($items)) {
                    $collection->append($taxonomy_map->findTaxonomy([$taxonomy => $items])->toArray());
                }
            }
            if (count($collection)) {
                unset($this->grav['page']);
                $this->grav['page'] = $collection->random()->current();
            }
        }
    }
}
