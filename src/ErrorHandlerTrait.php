<?php

namespace MediaMonks\SonataMediaBundle;

trait ErrorHandlerTrait
{
    protected function disableErrorHandler()
    {
        set_error_handler(function () {});
    }

    protected function restoreErrorHandler()
    {
        restore_error_handler();
    }
}
