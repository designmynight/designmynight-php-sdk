<?php
namespace DesignMyNight\Api;

class Users extends AbstractApi
{
    /**
     * @return array
     */
    public function all(): array
    {
        return $this->get('users');
    }

    /**
     * @return array
     */
    public function me(): array
    {
        return $this->get('users/me');
    }

    /**
     * @param  $id
     * @return array
     */
    public function show($id): array
    {
        return $this->get('users/' . rawurlencode($id));
    }
}
