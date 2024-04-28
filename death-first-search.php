<?php
/**
 * https://www.codingame.com/ide/puzzle/death-first-search-episode-1
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 **/

class Debug
{
    public static function log(string $message) : void
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

class Node
{
    /** @var int */
    private $id;
    /** @var Node[] */
    private $linkedNodes = [];
    /** @var bool  */
    private $isExit = false;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addLinkedNode(Node $node) : void
    {
        $this->linkedNodes[$node->getId()] = $node;
    }

    public function getLinkedNodes(): array
    {
        return $this->linkedNodes;
    }

    public function removeLinkedNode(Node $node) : void
    {
        unset($this->linkedNodes[$node->getId()]);
    }


    public function isExit(): bool
    {
        return $this->isExit;
    }

    public function setIsExit(bool $isExit): void
    {
        $this->isExit = $isExit;
    }

    public function debugLinks() : void
    {
        $links = [];

        foreach ($this->getLinkedNodes() as $linkedNode) {
            $links[] = "Node {$this->getId()} => {$linkedNode->getid()}";
        }
        Debug::log(__CLASS__.'::'.__FUNCTION__);
        Debug::log(implode("\n", $links));
    }
}

class NodeList
{
    /** @var Node[] */
    private $nodes = [];


    public function addNode(Node $node) : void
    {
        $this->nodes[$node->getId()] = $node;
    }

    public function getNode(int $id) : Node
    {
        return $this->nodes[$id];
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }

    public function linkById(int $id1, int $id2) : void
    {
        $this->link(
            $this->getNode($id1),
            $this->getNode($id2)
        );
    }

    public function link(Node $node1, Node $node2) : void
    {
        $node1->addLinkedNode($node2);
        $node2->addLinkedNode($node1);
    }

    public function unlink(Node $node1, Node $node2): void
    {
        $node1->removeLinkedNode($node2);
        $node2->removeLinkedNode($node1);
    }

    public function unlinkById(int $id1, int $id2): void
    {
        $this->unlink(
            $this->getNode($id1),
            $this->getNode($id2)
        );
    }

    public function getExitNodes() : array
    {
        return array_filter($this->nodes, static function (Node $node) {
            return $node->isExit();
        });
    }
}

class BobNet
{
    /** @var int */
    private $position;

    /**
     * @param int $position
     */
    public function __construct(int $position)
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}

/**
 * @see https://www.sitepoint.com/data-structures-4/
 */
class Graph
{
    protected $graph;
    protected $visited = array();

    public function __construct($graph) {
        $this->graph = $graph;
    }

    // find least number of hops (edges) between 2 nodes
    // (vertices)
    public function breadthFirstSearch($origin, $destination) {
        // mark all nodes as unvisited
        foreach ($this->graph as $vertex => $adj) {
            $this->visited[$vertex] = false;
        }

        // create an empty queue
        $q = new SplQueue();

        // enqueue the origin vertex and mark as visited
        $q->enqueue($origin);
        $this->visited[$origin] = true;

        // this is used to track the path back from each node
        $path = array();
        $path[$origin] = new SplDoublyLinkedList();
        $path[$origin]->setIteratorMode(
            SplDoublyLinkedList::IT_MODE_FIFO|SplDoublyLinkedList::IT_MODE_KEEP
        );

        $path[$origin]->push($origin);

        $found = false;
        // while queue is not empty and destination not found
        while (!$q->isEmpty() && $q->bottom() != $destination) {
            $t = $q->dequeue();

            if (!empty($this->graph[$t])) {
                // for each adjacent neighbor
                foreach ($this->graph[$t] as $vertex) {
                    if (!$this->visited[$vertex]) {
                        // if not yet visited, enqueue vertex and mark
                        // as visited
                        $q->enqueue($vertex);
                        $this->visited[$vertex] = true;
                        // add vertex to current path
                        $path[$vertex] = clone $path[$t];
                        $path[$vertex]->push($vertex);
                    }
                }
            }
        }

        if (isset($path[$destination])) {
            return $path[$destination];
        }

        return null;

        if (isset($path[$destination])) {
            echo "$origin to $destination in ",
                count($path[$destination]) - 1,
            " hopsn";
            $sep = '';
            foreach ($path[$destination] as $vertex) {
                echo $sep, $vertex;
                $sep = '->';
            }
            echo "n";
        }
        else {
            echo "No route from $origin to $destinationn";
        }
    }
}


class Game
{
    /** @var NodeList */
    private $nodeList;

    /**
     * @param $bobNet
     * @param NodeList $nodeList
     */
    public function __construct(NodeList $nodeList)
    {
        $this->nodeList = $nodeList;
    }

    public function debugExitLinks() : void
    {
        $links = [];

        foreach ($this->nodeList->getExitNodes() as $node) {
            foreach ($node->getLinkedNodes() as $linkedNode) {
                $links[] = "Node {$node->getId()} => {$linkedNode->getid()}";
            }
            $links[] = "";
        }

        Debug::log(__CLASS__.'::'.__FUNCTION__);
        Debug::log(implode("\n", $links));
    }

    public function getGraph() : Graph
    {
        $graphData = [];
        foreach ($this->nodeList->getNodes() as $node) {
            $graphData[$node->getId()] = [];
            foreach ($node->getLinkedNodes() as $linkedNode) {
                $graphData[$node->getId()][] = $linkedNode->getid();
            }
        }

        return new Graph($graphData);
    }

}

// $N: the total number of nodes in the level, including the gateways
// $L: the number of links
// $E: the number of exit gateways
fscanf(STDIN, "%d %d %d", $N, $L, $E);

$nodeList = new NodeList();

for ($i = 0; $i < $N; $i++)
{
    $node = new Node($i);
    $nodeList->addNode($node);
}

for ($i = 0; $i < $L; $i++)
{
    // $N1: N1 and N2 defines a link between these nodes
    fscanf(STDIN, "%d %d", $N1, $N2);
    $nodeList->linkById((int) $N1, (int) $N2);
}

for ($i = 0; $i < $E; $i++)
{
    // $EI: the index of a gateway node
    fscanf(STDIN, "%d", $EI);
    $nodeList->getNode((int) $EI)->setIsExit(true);
    $exits[] = (int) $EI;
}

$game = new Game($nodeList);
$game->debugExitLinks();
$bobNet = new BobNet(0);

// game loop
while (TRUE)
{
    // $SI: The index of the node on which the Bobnet agent is positioned this turn
    fscanf(STDIN, "%d", $SI);
    $bobNet->setPosition((int) $SI);


    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug: error_log(var_export($var, true)); (equivalent to var_dump)
    /** @var Node $exitNode */

    $shortestPath = null;
    foreach ($nodeList->getExitNodes() as $exitNode) {
        $path = $game->getGraph()->breadthFirstSearch($exitNode->getId(), $bobNet->getPosition());
        if ($path === null) {
            continue;
        }

        if ($shortestPath === null) {
            $shortestPath = $path;
        }

        if ($shortestPath->count() > $path->count()) {
            $shortestPath = $path;
        }
    }

    if ($shortestPath === null) {
        Debug::log("No path found");
    }

    $nodeList->unlinkById($shortestPath[0], $shortestPath[1]);
    Action::do(["$shortestPath[0] $shortestPath[1]"]);
}
?>
