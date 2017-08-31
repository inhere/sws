<?php

namespace Sws\components;

use Sws\Application;

interface ListenerInterface
{
    public function listen(Application $app);
}
