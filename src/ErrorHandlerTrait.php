<?php

namespace MediaMonks\SonataMediaBundle;

trait ErrorHandlerTrait
{
    protected function disableErrorHandler(): void
    {
        set_error_handler(function () { });
    }

    protected function restoreErrorHandler(): void
    {
        restore_error_handler();
    }
}
