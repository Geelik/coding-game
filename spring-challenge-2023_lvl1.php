<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

// $numberOfCells: amount of hexagonal cells in this map
fscanf(STDIN, "%d", $numberOfCells);

/**
 *
 * Classes
 *
 */

class Cell
{
    private $index;
    private $type;
    private $initialResources;
    private $neigh0;
    private $neigh1;
    private $neigh2;
    private $neigh3;
    private $neigh4;
    private $neigh5;
    /** @var int[] */
    private $neighbors;
    /** @var int*/
    private $resources;
    /** @var int*/
    private $weight;

    public function __construct(
        int $index,
        int $type,
        int $initialResources,
        int $neigh0,
        int $neigh1,
        int $neigh2,
        int $neigh3,
        int $neigh4,
        int $neigh5
    )
    {
        $this->neigh5 = $neigh5;
        $this->neigh4 = $neigh4;
        $this->neigh3 = $neigh3;
        $this->neigh2 = $neigh2;
        $this->neigh1 = $neigh1;
        $this->neigh0 = $neigh0;
        $this->initialResources = $initialResources;
        $this->resources = $initialResources;
        $this->type = $type;
        $this->index = $index;

        $neighbors = [];
        if ($neigh0  >= 0) {
            $neighbors[] = $neigh0;
        }
        if ($neigh1 >= 0) {
            $neighbors[] = $neigh1;
        }
        if ($neigh2 >= 0) {
            $neighbors[] = $neigh2;
        }
        if ($neigh3 >= 0) {
            $neighbors[] = $neigh3;
        }
        if ($neigh4 >= 0) {
            $neighbors[] = $neigh4;
        }
        if ($neigh5 >= 0) {
            $neighbors[] = $neigh5;
        }

        $this->neighbors = $neighbors;
        $this->weight = 0;
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
    public function getInitialResources(): int
    {
        return $this->initialResources;
    }

    /**
     * @return int
     */
    public function getResources(): int
    {
        return $this->resources;
    }

    /**
     * @return int
     */
    public function getNeigh0(): int
    {
        return $this->neigh0;
    }

    /**
     * @return int
     */
    public function getNeigh1(): int
    {
        return $this->neigh1;
    }

    /**
     * @return int
     */
    public function getNeigh2(): int
    {
        return $this->neigh2;
    }

    /**
     * @return int
     */
    public function getNeigh3(): int
    {
        return $this->neigh3;
    }

    /**
     * @return int
     */
    public function getNeigh4(): int
    {
        return $this->neigh4;
    }

    /**
     * @return int
     */
    public function getNeigh5(): int
    {
        return $this->neigh5;
    }

    public function isEmpty(): bool
    {
        return $this->type === 0;
    }

    public function isEgg(): bool
    {
        return $this->type === 1;
    }

    public function isResource(): bool
    {
        return $this->type === 2;
    }

    public function hasNeighbor(Cell $cell): bool
    {
        return in_array($cell->getIndex(), $this->neighbors, true);
    }

    /**
     * @return int[]
     */
    public function getNeighbors(): array
    {
        return $this->neighbors;
    }

    /**
     * @param int $resources
     */
    public function setResources(int $resources) : void
    {
        $this->resources = $resources;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function is(Cell $cell) : bool
    {
        return $cell->getIndex() === $this->getIndex();
    }
}

class Path {

    /** @var string */
    private $id;
    /** @var int[] */
    private $indexes;
    /** @var Cell  */
    private $origin;
    /** @var Cell */
    private $target;
    /** @var int */
    private $creationTurn;


    public function __construct(string $id, Cell $target, Cell $origin, array $indexes, int $creationTurn)
    {
        $this->id = $id;
        $this->indexes = $indexes;
        $this->origin = $origin;
        $this->target = $target;
        $this->creationTurn = $creationTurn;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Cell
     */
    public function getOrigin(): Cell
    {
        return $this->origin;
    }

    /**
     * @return Cell
     */
    public function getTarget(): Cell
    {
        return $this->target;
    }
}

/**
 *
 * Moves
 *
 */

interface Move
{
    public function getPriority() : int;
    public function getCommand() : string;
}

abstract class BaseMove implements Move
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

class Wait extends BaseMove
{
    public function getCommand() : string
    {
        return "WAIT";
    }
}

class Line extends BaseMove
{
    /** @var int */
    private $sourceIndex;
    /** @var int */
    private $targetIndex;
    /** @var int */
    private $strength;

    public function __construct(int $sourceIndex, int $targetIndex, int $strength, int $priority = 0)
    {
        parent::__construct($priority);
        $this->sourceIndex = $sourceIndex;
        $this->targetIndex = $targetIndex;
        $this->strength = $strength;
    }

    public function getCommand(): string
    {
        return "LINE $this->sourceIndex $this->targetIndex $this->strength";
    }
}

class Beacon extends BaseMove
{
    /** @var int */
    private $cellIndex;
    /** @var int */
    private $strength;

    public function __construct(int $cellIndex, int $strength, int $priority = 0)
    {
        parent::__construct($priority);
        $this->strength = $strength;
        $this->cellIndex = $cellIndex;
    }

    public function getCommand(): string
    {
        return "BEACON $this->cellIndex $this->strength";
    }
}


/**
 *
 * Setup
 *
 */

/** @var Cell[] $map */
$map = [];
/** @var Cell[] $cellsWithResources */
$cellsWithResources = [];

for ($i = 0; $i < $numberOfCells; $i++) {
    // $type: 0 for empty, 1 for eggs, 2 for crystal
    // $initialResources: the initial amount of eggs/crystals on this cell
    // $neigh0: the index of the neighbouring cell for each direction
    fscanf(STDIN, "%d %d %d %d %d %d %d %d", $type, $initialResources, $neigh0, $neigh1, $neigh2, $neigh3, $neigh4, $neigh5);

    $cell = new Cell($i, $type, $initialResources, $neigh0, $neigh1, $neigh2, $neigh3, $neigh4, $neigh5);
    $map[$i] = $cell;
    if ($cell->isResource() || $cell->isEgg()) {
        $cellsWithResources[$i] = $cell;
    }
}

// Pour phpstorm
$myBaseIndex = 0;
$oppBaseIndex = 0;

fscanf(STDIN, "%d", $numberOfBases);
$inputs = explode(" ", fgets(STDIN));
for ($i = 0; $i < $numberOfBases; $i++) {
    $myBaseIndex = intval($inputs[$i]);
}
$inputs = explode(" ", fgets(STDIN));
for ($i = 0; $i < $numberOfBases; $i++) {
    $oppBaseIndex = intval($inputs[$i]);
}

/** @var Cell $baseCell */
$baseCell = $map[$myBaseIndex];
/** @var Cell $oppBaseCell */
$oppBaseCell = $map[$oppBaseIndex];

$totalAnts = 0;
/** @var Path[] $paths */
$paths = [];
$turn = 0;


/**
 *
 * Fonctions
 *
 */
function calculateCellWeight(Cell $baseCell, Cell $cell) {
    $weight = 0;

    if ($baseCell->hasNeighbor($cell)) {
        $weight += 2;
    }

    if ($baseCell->isResource()) {
        $weight += 2;
    }

    if ($baseCell->isEgg()) {
        $weight += 1;
    }

    $cell->setWeight($weight);
}

function searchPath(array $map, Cell $origin, Cell $target, $path = []) : array
{
    $path[] = $origin->getIndex();

    // Dans le cas où l'on trouve la cible
    if ($target->hasNeighbor($origin)) {
        $path[] = $target->getIndex();
        return $path;
    }

    // Filtre les voisins pour ne pas faire de boucle ou de retour en arrière
    /** @var int[] $possibleNeighbor */
    $possibleNeighbor = array_filter($origin->getNeighbors(), function (int $neighborIndex) use ($path) {
        return !in_array($neighborIndex, $path, true);
    });

    // Si on a plus de possibilité s'arrête
    if (count($possibleNeighbor) === 0) {
        throw new RuntimeException('Dead end !');
    }

    $shortestPath = [];
    foreach ($possibleNeighbor as $neighbor) {
        try {
            $foundPath = searchPath($map, $map[$neighbor], $target, $path);
            if (count($foundPath) > 0 && (count($foundPath) < count($shortestPath) || count($shortestPath) === 0)) {
                $shortestPath = $foundPath;
            }
        } catch (Exception $exception) {

        }
    }

    return $shortestPath;
}

function generatePathId(Cell $origin, Cell $target) : string
{
    return implode('_', [
        $origin->getIndex(),
        $target->getIndex(),
    ]);
}
// game loop
while (TRUE) {
    // incrémente le tour
    ++$turn;
    $totalAnts = 0;
    // Permet de debug une variable à un tour spécifique
    if ($turn === 1) {
        //error_log(var_export($map[$baseCell->getIndex()], true));
    }

    /** @var Move[] $moves */
    $moves = [];
    $indexToDeleteFromResources = [];
    for ($i = 0; $i < $numberOfCells; $i++) {
        // $resources: the current amount of eggs/crystals on this cell
        // $myAnts: the amount of your ants on this cell
        // $oppAnts: the amount of opponent ants on this cell
        fscanf(STDIN, "%d %d %d", $resources, $myAnts, $oppAnts);

        $totalAnts += $myAnts;

        /** @var Cell $currentCell */
        $currentCell = $map[$i];

        if (array_key_exists($i, $cellsWithResources)) {
            // Met à jour la quantité de ressource
            $currentCell->setResources($resources);
            // S'il n'y a plus de ressources
            if ($currentCell->getResources() === 0) {
                $indexToDeleteFromResources[] = $i;
                $currentCell->setWeight(0);
                continue;
            }
            calculateCellWeight($baseCell, $currentCell);
        }
    }

    foreach ($indexToDeleteFromResources as $indexToDeleteFromResource) {
        unset($cellsWithResources[$indexToDeleteFromResource]);
    }

    usort($cellsWithResources, function (Cell $cellA, Cell $cellB) {
        if ($cellA->getWeight() > $cellB->getWeight()) {
            return -1;
        } elseif ($cellA->getWeight() < $cellB->getWeight()) {
            return 1;
        }

        return 0;
    });

    $antsRemaining = $totalAnts % count($cellsWithResources);
    $minAntsPerPath = floor($totalAnts % count($cellsWithResources));

    for ($j = 0; $j < count($cellsWithResources); $j++) {
        /** @var Cell $cell */
        $cell = $cellsWithResources[$j];

        try {
            /*
             * Version avec chemin le plus cours custom
             * Permettant de pondérer sur la taille du chemin (en faisant un ratio entre gain et le nombre de tour pour l'atteindre
             * Permettra également de mieux calculer le nombre de fourmis nécessaire
             * @todo faire avancer les beacon au fur et à mesure des tours
             *
            $pathId = generatePathId($baseCell, $cell);
            if (array_key_exists($pathId, $paths) && !$paths[$pathId]->needRefresh($turn)) {
                continue;
            }

            $pathIndexes = searchPath($map, $baseCell, $cell);
            $path = new Path($pathId, $baseCell, $cell, $pathIndexes, $turn);
            $paths[$pathId] = $path;
            foreach ($pathIndexes as $index) {
                $moves[] = new Beacon($index, 1, $cell->getWeight());
            }*/

            $moves[] = new Line($baseCell->getIndex(), $cell->getIndex(), $totalAnts);

        } catch (Exception $e) {
            error_log(var_export($e->getMessage(), true));
        }
    }

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug: error_log(var_export($var, true)); (equivalent to var_dump)


    // WAIT | LINE <sourceIdx> <targetIdx> <strength> | BEACON <cellIdx> <strength> | MESSAGE

    // Tri et execute les actions
    usort($moves, function(Move $moveA, Move $moveB) {
        if ($moveA->getPriority() > $moveB->getPriority()) {
            return -1;
        }
        elseif ($moveA->getPriority() < $moveB->getPriority()) {
            return 1;
        }

        return 0;
    });

    if (count($moves) === 0) {
        $moves[] = new Wait();
    }

    $output = [];
    foreach ($moves as $move) {
        $output[] = $move->getCommand();
    }

    echo (implode(';', $output)."\n");
}
?>
