<?php

echo $this->scripts();
echo $this->element('flash_box', array('app' => 'init') + compact('content_for_flash'));
echo $content_for_body;
debug(Datasource::$queries);