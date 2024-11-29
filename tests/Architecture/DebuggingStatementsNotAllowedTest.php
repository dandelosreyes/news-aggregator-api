<?php

test('No Debugging statements allowed on codebase')
    ->expect(['dd', 'ray', 'dump'])
    ->each
    ->not
    ->toBeUsed();
