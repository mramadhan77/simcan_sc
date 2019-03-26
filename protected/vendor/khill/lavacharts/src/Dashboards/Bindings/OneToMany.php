<?php

namespace Khill\Lavacharts\Dashboards\Bindings;

use Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper;

/**
 * OneToMany Binding Class
 *
 * Binds a single ControlWrapper to multiple ChartWrappers for use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Bindings
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class OneToMany extends Binding
{
    /**
     * Type of binding.
     *
     * @var string
     */
    const TYPE = 'OneToMany';

    /**
     * Creates the new Binding.
     *
     * @param \Khill\Lavacharts\Dashboards\Wrappers\ControlWrapper $controlWrapper
     * @param array                                                $chartWrappers
     */
    public function __construct(ControlWrapper $controlWrapper, array $chartWrappers)
    {
        parent::__construct([$controlWrapper], $chartWrappers);
    }
}
