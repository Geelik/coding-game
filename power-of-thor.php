<?php
/**
 * https://www.codingame.com/ide/puzzle/power-of-thor-episode-1
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 * ---
 * Hint: You can use the debug stream to print initialTX and initialTY, if Thor seems not follow your orders.
 **/

// $lightX: the X position of the light of power
// $lightY: the Y position of the light of power
// $initialTx: Thor's starting X position
// $initialTy: Thor's starting Y position
fscanf(STDIN, "%d %d %d %d", $lightX, $lightY, $initialTx, $initialTy);

$thorX = $initialTx;
$thorY = $initialTy;

// game loop
while (TRUE)
{
    // $remainingTurns: The remaining amount of turns Thor can move. Do not remove this line.
    fscanf(STDIN, "%d", $remainingTurns);

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug: error_log(var_export($var, true)); (equivalent to var_dump)

    // A single line providing the move to be made: N NE E SE S SW W or NW
    $command = '';
    if ($lightY > $thorY) {
        $thorY++;
        $command .= 'S';
    }
    elseif ($lightY < $thorY) {
        $thorY--;
        $command .= 'N';
    }

    if ($lightX > $thorX) {
        $thorX++;
        $command .= 'E';
    }
    elseif ($lightX < $thorX) {
        $thorX--;
        $command .= 'W';
    }

    echo("$command\n");

}
?>
