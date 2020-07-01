<?php


namespace App\Processing\Core;


interface Processable
{
    /**
     * Start process
     *
     * @return mixed
     */
    public function start();

    /**
     * Save process but don't start it
     *
     * @return mixed
     */
    public function wait();

    /**
     * Finish process with success
     *
     * @return mixed
     */
    public function successFinish();
}
