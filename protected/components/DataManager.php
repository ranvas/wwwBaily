<?php
/**
 * Class DataManager
 * @property membershipRepository membership
 * @property APIRepository api
 * @property searchTourRepository searchTour
 * @property helpRepository help
 * @property sqlRepository sql
 * @property wunderGroundRepository wunder
 */
class DataManager extends CApplicationComponent
{
    private $membership;
    private $api;
    private $searchTour;
    private $help;
    private $sql;
    private $wunder;

    public function getWunder()
    {
        if (!isset($this->wunder))
        {
            $this->wunder = new wunderGroundRepository();
        }
        return $this->wunder;
    }

    /**
     * @return SQLRepository
     */
    public function getSql()
    {
        if (!isset($this->sql))
        {
            $this->sql = new SQLRepository();
        }
        return $this->sql;
    }

    /**
     * @return membershipRepository
     */
    public function getMembership()
    {
        if (!isset($this->membership))
        {
            $this->membership = new membershipRepository();
        }
        return $this->membership;
    }


    /**
     * @return APIRepository
     */
    public function getApi()
    {
        if (!isset($this->api))
        {
            $this->api = new APIRepository();
        }
        return $this->api;
    }

    /**
     * @return searchTourRepository
     */
    public function getSearchTour()
    {
        if (!isset($this->searchTour))
        {
            $this->searchTour = new searchTourRepository();
        }
        return $this->searchTour;
    }

    /**
     * @return helpRepository
     */
    public function getHelp()
    {
        if (!isset($this->help))
        {
            $this->help = new helpRepository();
        }
        return $this->help;
    }

}