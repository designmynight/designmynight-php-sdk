<?php
namespace DesignMyNight\Api;

use DesignMyNight\Api\AbstractApi;

class Bookings extends AbstractApi
{
    /**
     * @param  $params
     * @return array
     */
    public function create(array $params)
    {
        return $this->post('bookings', $params);
    }
}
