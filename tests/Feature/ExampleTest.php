<?php

test('the application redirects from home page to login', function () {
    $response = $this->get('/');

    $response->assertStatus(302)->assertRedirect(route('login'));
});
