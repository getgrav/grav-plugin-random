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
            'onAfterInitPlugins' => ['onAfterInitPlugins', 0],
        ];
    }

    /**
     * Activate plugin if path matches to the configured one.
     */
    public function onAfterInitPlugins()
    {
        /** @var Uri $uri */
        $uri = $this->grav['uri'];
        $route = $this->config->get('plugins.random.route');

        if ($route && $route == $uri->path()) {
            $this->enable([
                'onAfterGetPage' => ['onAfterGetPage', 0]
            ]);
        }
    }

    /**
     * Display random page.
     */
    public function onAfterGetPage()
    {
        /** @var Taxonomy $taxonomy_map */
        $taxonomy_map = $this->grav['taxonomy'];

        $filters = (array) $this->config->get('plugins.random.filters');

        if (count($filters) > 0) {
            $collection = new Collection();
            foreach ($filters as $taxonomy => $items) {
                if (isset($items)) {
                    $collection->append($taxonomy_map->findTaxonomy([$taxonomy => $items])->toArray());
                }
            }

            $this->grav['page'] = $collection->random()->current();
        }
    }
}
