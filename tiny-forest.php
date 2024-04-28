<?php
/**
 * @see https://www.codingame.com/ide/puzzle/tiny-forest
 */

/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

class Debug
{
    public static function log($message) : void
    {
        error_log(var_export($message, true));
    }
}

class Action
{
    public static function do(array $actions): void
    {
        $output = [];
        foreach ($actions as $action) {
            $output[] = $action;
        }

        echo(implode(';', $output)."\n");
    }
}

fscanf(STDIN, "%d", $W);
fscanf(STDIN, "%d", $H);

class Cell {
    public const TYPE_SEED = 1;
    public const TYPE_TREE = 2;
    public const TYPE_GRASS = 3;
    /** @var int */
    private $type;
    /** @var int */
    private $x;
    /** @var int */
    private $y;
    /** @var int|null  */
    private $plantedYear = null;

    private $invalidForFirstSeed = false;

    /**
     * @param $type
     * @param $x
     * @param $y
     */
    public function __construct($type, $x, $y)
    {
        $this->type = $type;
        $this->x = $x;
        $this->y = $y;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function isGrass() : bool
    {
        return $this->type === self::TYPE_GRASS;
    }

    public function isSeed() : bool
    {
        return $this->type === self::TYPE_SEED;
    }

    public function isTree() : bool
    {
        return $this->type === self::TYPE_TREE;
    }

    public function plantSeed(int $year) : void
    {
        if ($this->isGrass() === false) {
            return;
        }
        $this->plantedYear = $year;
        $this->type = self::TYPE_SEED;
    }

    public function growTree(int $year) : void
    {
        if ($this->isTree() === true) {
            return;
        }
        $this->plantedYear = $year;
        $this->type = self::TYPE_TREE;
    }

    public function getPlantedYear(): ?int
    {
        return $this->plantedYear;
    }

    public function getSymbol() : string
    {
        if ($this->isInvalidForFirstSeed() && $this->isGrass()) {
            return 'O';
        }

        switch ($this->type) {
            case self::TYPE_GRASS: {

                return '.';
            }
            case self::TYPE_TREE: {
                return 'Y';
            }
            case self::TYPE_SEED: {
                return 'X';
            }
        }

        throw new RuntimeException('Invalid type');
    }

    public function isInvalidForFirstSeed(): bool
    {
        return $this->invalidForFirstSeed;
    }

    public function setInvalidForFirstSeed(bool $invalidForFirstSeed): void
    {
        $this->invalidForFirstSeed = $invalidForFirstSeed;
    }

    public static function getTypeFromSymbol(string $symbol) : int
    {
        switch ($symbol) {
            case '.': {
                return self::TYPE_GRASS;
            }
            case 'Y': {
                return self::TYPE_TREE;
            }
            case 'X': {
                return self::TYPE_SEED;
            }
        }

        throw new RuntimeException('Invalid symbol');
    }
}

class Field {
    /** @var array  */
    private $cells;
    /** @var int */
    private $width;
    /** @var int */
    private $height;
    /** @var int */
    private $currentYear;
    /** @var int */
    private $simulateYearCount;

    /**
     * @param array $cells
     * @param int $width
     * @param int $height
     * @param int $simulateYearCount
     */
    public function __construct(array $cells, int $width, int $height, int $simulateYearCount)
    {
        $this->cells = $cells;
        $this->width = $width;
        $this->height = $height;
        $this->simulateYearCount = $simulateYearCount;
        $this->currentYear = 0;
    }

    /**
     * @return Cell[]
     */
    public function getTrees() : array
    {
        return array_reduce($this->cells, static function (array $carry, array $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                if ($cell->isTree()) {
                    $carry[] = $cell;
                }
            }

            return $carry;
        }, []);
    }

    /**
     * @return Cell[]
     */
    public function getSeeds() : array
    {
        return array_reduce($this->cells, static function (array $carry, array $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                if ($cell->isSeed()) {
                    $carry[] = $cell;
                }
            }

            return $carry;
        }, []);
    }

    /**
     * @return Cell[]
     */
    public function getValidCellsForFirstSeed() : array
    {
        return array_reduce($this->cells, static function (array $carry, array $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                if ($cell->isInvalidForFirstSeed() === false) {
                    $carry[] = $cell;
                }
            }

            return $carry;
        }, []);
    }

    public function getCell(int $x, int $y) : ?Cell
    {
        if (
            $y < 0
            || $y > ($this->height - 1)
            || $x < 0
            || $x > ($this->width - 1)
        )
        {
            return null;
        }

        return $this->cells[$y][$x];
    }

    public function getCenterCell() : Cell
    {
        $centerCellCoordinates = $this->getCenterCellCoordinates();
        return $this->getCell($centerCellCoordinates['x'], $centerCellCoordinates['y']);
    }

    public function getLeftCell(Cell $cell) : ?Cell
    {
        $x = $cell->getX() - 1;
        $y = $cell->getY();
        return $this->getCell($x, $y);
    }

    public function getRightCell(Cell $cell) : ?Cell
    {
        $x = $cell->getX() + 1;
        $y = $cell->getY();
        return $this->getCell($x, $y);
    }

    public function getTopCell(Cell $cell) : ?Cell
    {
        $x = $cell->getX();
        $y = $cell->getY() - 1;
        return $this->getCell($x, $y);
    }

    public function getBottomCell(Cell $cell) : ?Cell
    {
        $x = $cell->getX();
        $y = $cell->getY() + 1;
        return $this->getCell($x, $y);
    }

    public function getOppositeCell(Cell $cell) : Cell
    {
        $centerCell = $this->getCenterCell();
        if ($cell->getX() > $centerCell->getX()) {
            $centerDistanceX = $cell->getX() - $centerCell->getX();
            $x = $centerCell->getX() - $centerDistanceX;
        }
        elseif ($cell->getX() < $centerCell->getX()) {
            $centerDistanceX = $centerCell->getX() - $cell->getX();
            $x = $centerCell->getX() - $centerDistanceX;
        }
        else {
            $x = 0;
        }

        if ($cell->getY() > $centerCell->getY()) {
            $centerDistanceY = $cell->getY() - $centerCell->getY();
            $y = $centerCell->getY() - $centerDistanceY;
        }
        elseif ($cell->getY() < $centerCell->getY()) {
            $centerDistanceY = $centerCell->getY() - $cell->getY();
            $y = $centerCell->getY() - $centerDistanceY;
        }
        else {
            $y = 0;
        }

        return $this->getCell($x, $y);
    }

    public function getCenterCellCoordinates() : array
    {
        if ($this->height % 2 > 0)
        {
            $y = (int) (($this->height / 2));
        }
        else {
            $y = (int) $this->height / 2;
        }

        if ($this->width % 2 > 0)
        {
            $x = (int) (($this->width / 2));
        }
        else {
            $x = (int) $this->width / 2;
        }

        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    public function simulate() : void
    {
        while ($this->currentYear <= $this->simulateYearCount) {
            Debug::log("Simulating year {$this->currentYear}");
            if ($this->currentYear === 0) {
                $this->plantSeed();
                $this->print("Seed planted");
            }

            $this->growTrees();
            $this->growSeeds();

            $this->currentYear++;
        }
    }

    private function plantSeed() : void
    {
        if (count($this->getTrees()) === 0) {
            $cell = $this->getCenterCell();
            $cell->plantSeed($this->currentYear);
            $this->print("Seed planted");
            return;
        }

        if (count($this->getTrees()) === 1) {
            $optimumDistance = 3;
            $tree = current($this->getTrees());

            $centerCell = $this->getCenterCell();
            if ($tree->getX() > $centerCell->getX()) {
                $x = $tree->getX() - $optimumDistance;
            }
            elseif ($tree->getX() < $centerCell->getX()) {
                $x = $tree->getX() + $optimumDistance;
            }
            else {
                $x = 0;
            }

            if ($tree->getY() > $centerCell->getY()) {
                $y = $tree->getY() - $optimumDistance;
            }
            elseif ($tree->getY() < $centerCell->getY()) {
                $y = $tree->getY() + $optimumDistance;
            }
            else {
                $y = 0;
            }


            $cell = $this->getCell($x, $y);
            if ($cell === null) {
                throw new RuntimeException("Cannot plant seed at $x:$y");
            }

            $cell->plantSeed($this->currentYear);
            return;
        }

        $margin = 3;
        foreach ($this->getTrees() as $tree) {
            $tree->setInvalidForFirstSeed(true);
            for ($i = 1; $i < $margin; $i++) {
                $plusX = $tree->getX() + $i;
                $minusX = $tree->getX() - $i;
                $plusY = $tree->getY() + $i;
                $minusY = $tree->getY() - $i;

                $samePlusCell = $this->getCell($tree->getX(),$plusY);
                if ($samePlusCell !== null) {
                    $samePlusCell->setInvalidForFirstSeed(true);
                }
                $sameMinusCell = $this->getCell($tree->getX(),$minusY);
                if ($sameMinusCell !== null) {
                    $sameMinusCell->setInvalidForFirstSeed(true);
                }
                $plusSameCell = $this->getCell($plusX,$tree->getY());
                if ($plusSameCell !== null) {
                    $plusSameCell->setInvalidForFirstSeed(true);
                }
                $minusSameCell = $this->getCell($minusX,$tree->getY());
                if ($minusSameCell !== null) {
                    $minusSameCell->setInvalidForFirstSeed(true);
                }

                $plusPlusCell = $this->getCell($plusX,$plusY);
                if ($plusPlusCell !== null) {
                    $plusPlusCell->setInvalidForFirstSeed(true);
                }
                $plusMinusCell = $this->getCell($plusX,$minusY);
                if ($plusMinusCell !== null) {
                    $plusMinusCell->setInvalidForFirstSeed(true);
                }
                $minusPlusCell = $this->getCell($minusX,$plusY);
                if ($minusPlusCell !== null) {
                    $minusPlusCell->setInvalidForFirstSeed(true);
                }
                $minusMinusCell = $this->getCell($minusX,$minusY);
                if ($minusMinusCell !== null) {
                    $minusMinusCell->setInvalidForFirstSeed(true);
                }
            }
        }
    }

    private function growTrees() : void
    {
        foreach ($this->getTrees() as $tree) {
            if (($tree->getPlantedYear() + 1) === $this->currentYear) {

                $topCell = $this->getTopCell($tree);
                $rightCell = $this->getRightCell($tree);
                $bottomCell = $this->getBottomCell($tree);
                $leftCell = $this->getLeftCell($tree);

                if ($topCell !== null) {
                    $topCell->plantSeed($this->currentYear);
                }
                if ($rightCell !== null) {
                    $rightCell->plantSeed($this->currentYear);
                }
                if ($bottomCell !== null) {
                    $bottomCell->plantSeed($this->currentYear);
                }
                if ($leftCell !== null) {
                    $leftCell->plantSeed($this->currentYear);
                }
            }
        }
    }

    private function growSeeds() : void
    {
        foreach ($this->getSeeds() as $seed) {
            if (($seed->getPlantedYear() + 10) === $this->currentYear) {
                $seed->growTree($this->currentYear);
            }
        }
    }

    public function print(string $label) : void
    {
        Debug::log($label);
        $header = " ";
        for ($i = 0; $i < $this->width; $i++) {
            $header .= $i;
        }
        Debug::log($header);
        foreach ($this->cells as $index => $row) {
            $rowAsString = (string)$index;
            /** @var Cell $cell */
            foreach ($row as $cell) {
                $rowAsString .= $cell->getSymbol();
            }

            Debug::log($rowAsString);
        }
    }
}

$cells = [];
for ($i = 0; $i < $H; $i++) {
    $row = stream_get_line(STDIN, 1024 + 1, "\n");
    $rwoCells = str_split($row);
    $fieldRow = [];
    foreach ($rwoCells as $index => $cell) {
        $fieldRow[] = new Cell(
            Cell::getTypeFromSymbol($cell),
            $index,
            $i
        );
    }

    $cells[] = $fieldRow;
}

$field = new Field($cells, $W, $H, 33);
// Write an answer using echo(). DON'T FORGET THE TRAILING \n
// To debug: error_log(var_export($var, true)); (equivalent to var_dump)
$field->print("Start layout");
$field->simulate();
$field->print("End layout");
$treeCount = count($field->getTrees());

echo("$treeCount\n");

