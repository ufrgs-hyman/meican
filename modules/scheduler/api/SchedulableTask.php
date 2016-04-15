<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican2#license
 */

namespace meican\scheduler\api;

/**
 * Interface implemented by classes with scheduled execution support.
 *
 * @author Maurício Quatrin Guerreiro @mqgmaster
 */
interface SchedulableTask {

    public function execute($obj_data);

}
