<?php namespace Jakten\Console;

/**
 * Class Progress
 * @package Jakten\Console
 */
class Progress
{
    /**
     * show a status bar in the console
     *
     * <code>
     * for($x=1;$x<=100;$x++){
     *
     *     show_status($x, 100);
     *
     *     usleep(100000);
     *
     * }
     * </code>
     *
     * @param   int     $done   how many items are completed
     * @param   int     $total  how many items are to be done total
     * @param   int     $size   optional size of the status bar
     *
     * @return  void
     */
    public function showProgress($done, $total, $size = 30)
    {
        static $start_time;

        // if we go over our bound, just ignore it
        if ($done > $total) {
            return;
        }

        if (empty($start_time)) {
            $start_time = time();
        }
        $now = time();

        $perc = (double) ($done / $total);

        $bar = floor($perc * $size);

        $status_bar = "\r[";
        $status_bar .= str_repeat('=', $bar);
        if ($bar < $size) {
            $status_bar .= '>';
            $status_bar .= str_repeat(' ', $size - $bar);
        } else {
            $status_bar .= '=';
        }

        $disp = number_format($perc * 100, 0);

        $status_bar .= "] $disp%  $done/$total";

        $rate = ($now - $start_time) / $done;
        $left = $total - $done;
        $eta = round($rate * $left, 2);

        $elapsed = $now - $start_time;

        $status_bar .= ' remaining: ' . number_format($eta) . ' sec.  elapsed: ' . number_format($elapsed) . ' sec.';

        echo "$status_bar  ";

        flush();

        // when done, send a newline
        if ($done == $total) {
            echo "\n";
        }
    }
}
