<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonRepo;
use App\Photos\Seasons\SeasonStorageManager;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Laracasts\Presenter\Exceptions\PresenterException;

class MoveSeasonZipToRemoteProcess extends ProcessableProcess
{
    /**
     * ZipForSeasonPreparingProcess constructor.
     * @param int $processable_id
     * @param string|null $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $processable_id,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($processable_id, Season::class, $initialStatus, $scenario);
    }

    /**
     * Do process logic
     *
     * @return mixed
     * @throws FileNotFoundException
     * @throws PresenterException
     */
    protected function processLogic()
    {
        /** @var Season $season */
        $season = (new SeasonRepo())->getByID($this->processable_id);
        (new SeasonStorageManager())->moveSeasonZipToRemote($season);
    }
}
