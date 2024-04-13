<?php

it('will not use debugging functions')
    ->actingAs(\Workbench\App\Models\User::create())
    ->assertAuthenticated()
;
