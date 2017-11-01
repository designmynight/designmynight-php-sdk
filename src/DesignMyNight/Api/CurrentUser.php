<?php
namespace DesignMyNight\Api;

use DesignMyNight\Api\Users;

class CurrentUser extends Users
{
    /**
     * @param  $id
     * @return array
     */
    public function show($id = null): array
    {
        return $this->me();
    }
}
