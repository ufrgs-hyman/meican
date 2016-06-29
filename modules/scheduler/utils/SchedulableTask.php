<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\scheduler\utils;

/**
 * Interface implemented by classes with scheduled execution support.
 *
 * @author Maurício Quatrin Guerreiro
 */
interface SchedulableTask {

    public function execute($data);
}
