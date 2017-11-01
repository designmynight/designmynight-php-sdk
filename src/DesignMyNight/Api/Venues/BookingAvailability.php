<?php
namespace DesignMyNight\Api\Venues;

class BookingAvailability extends AbstractApi
{
    /**
     * @param $venueId
     * @param array $params
     * @return array
     */
    public function show($venueId, $params = []):array
    {
        return $this->get('venues/' . rawurlencode($venueId) . '/booking-availability', $params);
    }
}
