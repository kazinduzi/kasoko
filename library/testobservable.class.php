<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testObserver
 *
 * @author Emmanuel_Leonie
 */
class Testobserver extends Observer
{

    public function update(&$sender, $arg)
    {
        switch ($arg) {
            case 'changed':
                echo 'Changed<br />';
                print_r($sender);
                break;
            case 'deleted':
                echo 'Deleted<br />';
                print_r($sender);
                break;
            default :
                print_r($sender);
                break;
        }
    }

}

class TestObservable extends Observable
{

    public function changed()
    {
        echo 'Observable is changed<br/>';
        //Notify all attached observers to this
        $this->notifyAll('changed');
    }

    //
    public function deleted()
    {
        echo 'Observable is deleted<br/>';
        //Notify all attached observers to this
        $this->notifyAll('deleted');
    }

}

?>
