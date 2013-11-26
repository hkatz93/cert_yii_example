<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class DbTest extends CTestCase {
    public function testConnection(){
        //$this->assertTrue(true);
        $this->assertNotEquals(NULL, Yii::app()->db);
    }
}
?>
