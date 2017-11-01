<?php
namespace DesignMyNight\Api;

use DesignMyNight\Api\Venues\BookingAvailability;

class Venues extends AbstractApi
{
    /**
     * @return array
     */
    public function all()
    {
        return $this->get('Venues');
    }

    /**
     * @param  $id
     * @return array
     */
    public function show($id)
    {
        return $this->get('pages/' . rawurlencode($id));
    }

    public function bookingAvailability()
    {
        return new BookingAvailability($this->client);
    }
}
