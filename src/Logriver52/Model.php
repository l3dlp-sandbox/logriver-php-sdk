<?php
/*
 * This file is part of LogRiver package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author LogRiver <contact@logriver.io>
 */

class Logriver_Model {

    public $type;
    public $file;
    public $line;
    public $mt;
    public $m;
    public $st;
    public $message;
    public $ct;
    public $cat;
    public $ds;
    public $t;
    public $md;

    public function doGenerateHttpParams() {
        $send = 'type=' . $this->type;
        $send .= '&mt=' . $this->mt . '&m=' . $this->m . '&st=' . $this->st;

        $send .= '&data=' . $this->message;

        if($this->file !== null && $this->line !== null) {
            $send .= '&f=' . $this->file;
            $send .= '&l=' . $this->line;
        }

        if($this->ct !== null) {
            $send .= '&ct=' . $this->ct;
        }

        if($this->cat !== null) {
            $send .= '&cat=' . $this->cat;
        }

        if($this->ds !== null) {
            $send .= '&ds=' . @json_encode($this->ds);
        }

        if($this->t !== null) {
            $send .= '&t=' . @json_encode($this->t);
        }

        if($this->md !== null) {
            $send .= '&md=' . @json_encode($this->md);
        }

        return $send;
    }
}
