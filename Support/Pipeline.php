<?php

namespace Framework\Support;

use Closure;
use Error;

/**
 * The Pipeline class allows for the execution of a sequence of operations (pipes) on an object.
 *
 * @package Framework\Support
 */
class Pipeline
{
    /**
     * The array of pipes.
     *
     * @var array
     */
    protected array $pipes = [];

    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected $passable;

    /**
     * Set the object being sent through the pipeline.
     *
     * @param mixed $passable The object being sent through the pipeline.
     * @return $this
     */
    public function send($passable): Pipeline
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Set the array of pipes.
     *
     * @param mixed $pipes The array of pipes or variadic arguments of pipes.
     * @return $this
     */
    public function through($pipes): Pipeline
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Run the pipeline with a final destination callback.
     *
     * @param mixed $destination The final destination callback.
     * @return mixed The result of the pipeline execution.
     */
    public function then($destination)
    {
        $callback = array_reduce(array_reverse($this->pipes), $this->carry(), $this->prepare_destination($destination));

        return $callback($this->passable);
    }

    /**
     * Prepare a Closure based destination callable for the pipeline.
     *
     * @param mixed $destination The final destination callback.
     * @return Closure A Closure for the final destination callback.
     */
    protected function prepare_destination($destination): Closure
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * Get the final Closure to be passed to array_reduce.
     *
     * @return Closure A Closure for carrying out pipeline operations.
     */
    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                $middleware = app($pipe);

                if (method_exists($middleware, 'handle')) {
                    return $middleware->handle($passable, $stack);
                } else {
                    throw new Error('This class needs to have a handle method');
                }
            };
        };
    }
}