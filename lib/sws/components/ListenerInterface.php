<?php

namespace sws\components;

use sws\Application;

interface ListenerInterface
{
    public function listen(Application $app);
}
