<?php


namespace App\Photos\Seasons;


class SeasonRoutesGenerator
{
    /** @var Season */
    protected $season;

    /**
     * SeasonRoutesGenerator constructor.
     *
     * @param Season $season
     */
    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    /**
     * @return string
     */
    public function zipPreparingStart()
    {
        return route('dashboard::season-export.zip.preparing-start', $this->season);
    }

    /**
     * @return string
     */
    public function zipPreparingStatus()
    {
        return route('dashboard::season-export.zip.preparing-status', $this->season);
    }

    /**
     * @return string
     */
    public function zipPreparingChoice()
    {
        return route('dashboard::season-export.zip.preparing-choice', $this->season);
    }
}
