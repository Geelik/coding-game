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

class Checkpoint {
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
    private $index;
    /**
     * @var int
     */
    private $closestX;
    /**
     * @var int
     */
    private $closestY;

    public function __construct(
        int $x,
        int $y,
        int $index,
        int $closestX,
        int $closestY
    )
    {

        $this->x = $x;
        $this->y = $y;
        $this->index = $index;
        $this->closestX = $closestX;
        $this->closestY = $closestY;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return int
     */
    public function getClosestX(): int
    {
        return $this->closestX;
    }

    /**
     * @return int
     */
    public function getClosestY(): int
    {
        return $this->closestY;
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

    /**
     * @see https://www.reddit.com/r/gamemaker/comments/m38s5j/line_circle_intersect_function_to_share/
     */
    public static function findClosestIntersectPoint($cx, $cy, $r, $x1, $y1, $x2, $y2) : array
    {
        // Find intersect points with a line and a circle
        // Circle origin [$cx, $cy] with radius $r
        // Line of [$x1, $y1] to [$x2, $y2]

        $cx = $x1 - $cx;
        $cy = $y1 - $cy;

        $vx = $x2 - $x1;
        $vy = $y2 - $y1;
        $a = $vx * $vx + $vy * $vy;
        $b = 2 * ($vx * $cx + $vy * $cy);
        $c = $cx * $cx + $cy * $cy - $r * $r;
        $det = $b * $b - 4 * $a * $c;

        // Line intersects circle
        $det = sqrt($det);
        $t1 = (-$b - $det) / (2 * $a);

        return [
            'x' => $x1 + $t1 * $vx,
            'y' => $y1 + $t1 * $vy
        ];
    }

    public static function findDistanceBetweenPoints($x1, $y1, $x2, $y2) : float
    {
        return ((($x2 - $x1) ** 2 + ($y2 - $y1) ** 2) ** 0.5);
    }

}

class Game
{
    const CHECKPOINT_RADIUS = 600;
    const FLY_DISTANCE_APPROX = 594;

    const MAX_BOOST_COUNT = 1;
    const BOOST_MARGIN = self::CHECKPOINT_RADIUS * 2;
    const BOOST_MIN_DISTANCE = 1200 + self::BOOST_MARGIN;

    private $actions = [];
    private $loop = 1;

    private $boostCount = 0;
    private $checkpoints = [];


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
            }

            if ($actionA->getPriority() < $actionB->getPriority()) {
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


    public function canBoost() : bool
    {
        return
            // empêche de booster directement car cela n'apporte pas d'avantage
            $this->loop > 5
            && $this->boostCount < self::MAX_BOOST_COUNT;
    }

    public function fly(int $x, int $y, int $thrust) {
        $this->addAction(new Fly($x, $y, $thrust));
    }

    public function boost(int $x, int $y)
    {
        Utils::log('BOOOOOOOOOOOOOOSSSSSSSSSSSSSTTTTTTTTTTTT');
        $this->fly($x, $y, Fly::BOOST);
        $this->boostCount++;
    }

    public function incrementLoop() : void
    {
        $this->loop++;
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

        ['x' => $closestX, 'y' => $closestY] = Utils::findClosestIntersectPoint(
            $nextCheckpointX,
            $nextCheckpointY,
            Game::CHECKPOINT_RADIUS,
            $x,
            $y,
            $nextCheckpointX,
            $nextCheckpointY
        );
        $closestX = (int) round($closestX);
        $closestY = (int) round($closestY);

        if (!array_key_exists("{$nextCheckpointX}_{$nextCheckpointY}", $this->checkpoints)) {
            $checkpoint = new Checkpoint(
                $nextCheckpointX,
                $nextCheckpointY,
                $this->loop - 1,
                $closestX,
                $closestY
            );

            $this->checkpoints["{$nextCheckpointX}_{$nextCheckpointY}"] = $checkpoint;
        }
        else {
            $checkpoint = $this->checkpoints["{$nextCheckpointX}_{$nextCheckpointY}"];
        }

        $closestPointDist = (int) round(Utils::findDistanceBetweenPoints(
            $x,
            $y,
            $closestX,
            $closestY
        ));

        $opponentDist = Utils::findDistanceBetweenPoints($x, $y, $opponentX, $opponentY);
        if ($this->canBoost())
        {
            $this->boost($closestX, $closestY);
            return;
        }

        // Si on se rapproche
        // Si on tourne
        // ne met pas de vitesse et avance avec l'inertie
        if ($nextCheckpointAngle < -90 || $nextCheckpointAngle > 90)
        {
            $this->fly($closestX, $closestY, 0);
            return;
        }

        // Sinon à fond
        $this->fly($closestX, $closestY, 100);
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
    $game->incrementLoop();
    $game->sendActions();

}
