<?php

/**
 * https://www.codingame.com/ide/puzzle/mad-pod-racing
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

interface Action
{
    public function getPriority() : int;

    public function getCommand() : string;
}

abstract class BaseAction implements Action
{
    /** @var int */
    private $priority;

    public function __construct(int $priority = 0)
    {
        $this->priority = $priority;
    }

    public function getPriority() : int
    {
        return $this->priority;
    }
}

class Fly extends BaseAction
{
    const BOOST = -1;
    const BOOST_VALUE = 'BOOST';
    /**
     * @var int
     */
    private $x;
    /**
     * @var int
     */
    private $y;
    /**
     * @var int
     */
    private $thrust;

    public function __construct(int $x, int $y, int $thrust, int $priority = 0)
    {
        parent::__construct($priority);
        if ($thrust !== self::BOOST && ($thrust < 0 || $thrust > 100)) {
            throw new RuntimeException('$thrust should be in range 0-100 included');
        }

        $this->x = $x;
        $this->y = $y;
        $this->thrust = $thrust;
    }


    public function getCommand() : string
    {
        $thrust = $this->thrust === self::BOOST ? self::BOOST_VALUE : $this->thrust;
        return "$this->x $this->y $thrust";
    }
}

class Utils
{
    public static function between(int $value, int $min, int $max) : bool
    {
        return $value >= $min && $value <= $max;
    }

    public static function log(string $message) : void
    {
        error_log(var_export($message, true));
    }
}

class Game
{
    const CHECKPOINT_RADIUS = 600;

    const MAX_BOOST_COUNT = 1;
    const BOOST_MARGIN = self::CHECKPOINT_RADIUS * 3;
    const BOOST_MIN_DISTANCE = 1200 + self::BOOST_MARGIN;

    private $hasStarted = false;
    private $boostCount = 0;
    private $actions = [];

    /**
     * @return bool
     */
    public function isStarted() : bool
    {
        return $this->hasStarted;
    }

    public function addAction(Action $action) : void
    {
        $this->actions[] = $action;
    }

    /**
     * @return array
     */
    public function sendActions() : void
    {
        // Tri et execute les actions
        usort($this->actions, function (Action $actionA, Action $actionB) {
            if ($actionA->getPriority() > $actionB->getPriority()) {
                return -1;
            } elseif ($actionA->getPriority() < $actionB->getPriority()) {
                return 1;
            }

            return 0;
        });

        $output = [];
        foreach ($this->actions as $action) {
            $output[] = $action->getCommand();
        }

        $this->actions = [];
        echo(implode(';', $output)."\n");
    }

    /**
     * @param bool $hasStarted
     */
    public function setHasStarted(bool $hasStarted) : void
    {
        $this->hasStarted = $hasStarted;
    }

    public function canBoost(int $nextCheckpointDistance, int $nextCheckpointAngle) : bool
    {
        return
            $this->boostCount < self::MAX_BOOST_COUNT
            && $nextCheckpointDistance >= self::BOOST_MIN_DISTANCE
            && Utils::between($nextCheckpointAngle, -10, 10);
    }

    public function fly(int $x, int $y, int $thrust) {
        $this->addAction(new Fly($x, $y, $thrust));
    }

    public function boost(int $x, int $y)
    {
        $this->fly($x, $y, Fly::BOOST);
        $this->boostCount++;
    }

    public function loop(
        int $x,
        int $y,
        int $nextCheckpointX,
        int $nextCheckpointY,
        int $nextCheckpointDist,
        int $nextCheckpointAngle,
        int $opponentX,
        int $opponentY
    ) : void
    {
        Utils::log("nextCheckpointDist => $nextCheckpointDist");
        Utils::log("nextCheckpointAngle => $nextCheckpointAngle");

        // Si on peut boost va pas plus loin
        if ($this->canBoost($nextCheckpointDist, $nextCheckpointAngle)) {
            $this->boost($nextCheckpointX, $nextCheckpointY);
            return;
        }

        // Si on se rapproche
        // Si on tourne
        // ne met pas de vitesse et avance avec l'inertie
        if (
            ($nextCheckpointDist < self::CHECKPOINT_RADIUS * 2 && $nextCheckpointAngle === 0)
            || ($nextCheckpointAngle < -90 || $nextCheckpointAngle > 90)
        )
        {
            $this->fly($nextCheckpointX, $nextCheckpointY, 0);
            return;
        }

        // Sinon à fond
        $this->fly($nextCheckpointX, $nextCheckpointY, 100);
    }
}

$game = new Game();

// game loop
while (TRUE) {
    $actions = [];
    // $nextCheckpointX: x position of the next check point
    // $nextCheckpointY: y position of the next check point
    // $nextCheckpointDist: distance to the next checkpoint
    // $nextCheckpointAngle: angle between your pod orientation and the direction of the next checkpoint
    fscanf(STDIN, "%d %d %d %d %d %d", $x, $y, $nextCheckpointX, $nextCheckpointY, $nextCheckpointDist, $nextCheckpointAngle);
    fscanf(STDIN, "%d %d", $opponentX, $opponentY);

    $game->loop($x, $y, $nextCheckpointX, $nextCheckpointY, $nextCheckpointDist, $nextCheckpointAngle, $opponentX, $opponentY);
    $game->setHasStarted(true);
    $game->sendActions();

}
