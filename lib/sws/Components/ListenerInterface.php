<?php

namespace Sws\Components;

use Sws\Application;

interface ListenerInterface
{
    public function listen(Application $app);
}
