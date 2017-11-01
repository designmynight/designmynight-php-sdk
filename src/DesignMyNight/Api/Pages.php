<?php
namespace DesignMyNight\Api;

class Pages extends AbstractApi
{
    /**
     * @return array
     */
    public function all(): array
    {
        return $this->get('pages');
    }

    /**
     * @param  $id
     * @return array
     */
    public function show($id): array
    {
        return $this->get('pages/' . rawurlencode($id));
    }
}
