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
    /** @var array[int][int][Cell]  */
    private $cells;
    /** @var int */
    private $width;
    /** @var int */
    private $height;
    /** @var int */
    private $currentYear;
    /** @var int */
    private $simulateYearCount;
    /** @var array[int][int][Cell]  */
    private $originalCells;

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

        $this->originalCells = [];
        foreach ($this->cells as $y => $row) {
            foreach ($row as $x => $cell) {
                $this->originalCells[$y][$x] = clone $cell;
            }
        }
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
    public function getGrasses() : array
    {
        return array_reduce($this->cells, static function (array $carry, array $row) {
            /** @var Cell $cell */
            foreach ($row as $cell) {
                if ($cell->isGrass()) {
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

    public function simulateAll() : int
    {
        $maxTreeCount = 0;

        $grasses = $this->getGrasses();

        foreach ($grasses as $grass) {
            $this->resetCells();
            $this->currentYear = 0;
            $this->getCell($grass->getX(), $grass->getY())->plantSeed($this->currentYear);

            $this->print("Simulation start for {$grass->getX()}:{$grass->getY()}");
            $simulationTreeCount = $this->simulate();
            $this->print("Simulation end for {$grass->getX()}:{$grass->getY()}");

            Debug::log("\n");
            Debug::log("\n");
            if ($simulationTreeCount > $maxTreeCount) {
                $maxTreeCount = $simulationTreeCount;
            }

        }

        return $maxTreeCount;
    }


    private function resetCells() : void
    {
        unset($this->cells);
        foreach ($this->originalCells as $y => $row) {
            foreach ($row as $x => $cell) {
                $this->cells[$y][$x] = clone $cell;
            }
        }
    }

    private function simulate() : int
    {
        while ($this->currentYear <= $this->simulateYearCount) {
            $this->growTrees();
            $this->growSeeds();
            $this->currentYear++;
        }

        return count($this->getTrees());
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
$treeCount = $field->simulateAll();

echo("$treeCount\n");

