<?php

namespace Framework\Support;

use Closure;
use Exception;
use Framework\Component\Container;
use Framework\Http\Pipe;

/**
 * The Pipeline class allows for the execution of a sequence of operations (pipes) on an object.
 *
 * This class is used to run a through an array of functions.
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
     * The container implementation.
     *
     * @var Container|null
     */
    private ?Container $container;

    /**
     * The method to be called when class is run through the pipeline.
     *
     * @var string
     */
    private string $method;

    /**
     * Create a new class instance.
     *
     * @param Container|null $container
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

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
     * Set the method to call on the pipes.
     *
     * @param string $method
     * @return $this
     */
    public function via(string $method): Pipeline
    {
        $this->method = $method;

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
                try {
                    $pipe = $this->container->get($pipe);

                    if (is_a($pipe, Pipe::class)) {
                        return $pipe->handle($passable, $stack);
                    }

                    return method_exists($pipe, $this->method) ? $pipe->{$this->method}($passable, $stack) : $pipe($passable, $stack);
                } catch (Exception $exception) {
                    return $this->handle_exception($passable, $exception);
                }
            };
        };
    }

    /**
     * Handle an exception by rethrowing it.
     *
     * @param mixed $passable The data being passed through the pipeline.
     * @param Exception $exception The exception to be handled.
     *
     * @throws Exception The exception is rethrown.
     */
    private function handle_exception($passable, Exception $exception)
    {
        throw $exception;
    }
}